<?php

namespace App\Security\User;

use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Utils\CollectionUtils;
use LightSaml\ClaimTypes;
use LightSaml\Model\Protocol\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;
use SchulIT\CommonBundle\Saml\ClaimTypes as SamlClaimTypes;
use SchulIT\CommonBundle\Security\User\AbstractUserMapper;

class UserMapper extends AbstractUserMapper {
    const ROLES_ASSERTION_NAME = 'urn:roles';

    private array $typesMap;
    private TeacherRepositoryInterface $teacherRepository;
    private StudentRepositoryInterface $studentRepository;
    private LoggerInterface $logger;

    public function __construct(array $typesMap, TeacherRepositoryInterface $teacherRepository, StudentRepositoryInterface $studentRepository, LoggerInterface $logger = null) {
        $this->typesMap = $typesMap;
        $this->teacherRepository = $teacherRepository;
        $this->studentRepository = $studentRepository;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string $type
     * @return UserType
     */
    private function getUserType(string $type): UserType {
        if(!array_key_exists($type, $this->typesMap) || !in_array($type, UserType::values())) {
            $this->logger
                ->notice(sprintf('User type "%s" is not a valid UserType. Setting type "user"', $type));
            return UserType::User();
        }

        return new UserType($type);
    }

    /**
     * @param User $user
     * @param Response|array[] $data Either a SAMLResponse or an array (keys: SAML Attribute names, values: corresponding values)
     * @return User
     */
    public function mapUser(User $user, $data): User {
        if(is_array($data)) {
            return $this->mapUserFromArray($user, $data);
        } else if($data instanceof Response) {
            return $this->mapUserFromResponse($user, $data);
        }
    }

    private function mapUserFromResponse(User $user, Response $response): User {
        return $this->mapUserFromArray($user, $this->transformResponseToArray(
            $response,
            [
                ClaimTypes::COMMON_NAME,
                SamlClaimTypes::ID,
                ClaimTypes::GIVEN_NAME,
                ClaimTypes::SURNAME,
                ClaimTypes::EMAIL_ADDRESS,
                SamlClaimTypes::EXTERNAL_ID,
                SamlClaimTypes::TYPE,
            ],
            [
                self::ROLES_ASSERTION_NAME
            ]
        ));
    }

    /**
     * @param User $user User to populate data to
     * @param array<string, mixed> $data
     * @return User
     */
    private function mapUserFromArray(User $user, array $data): User {
        $username = $data[ClaimTypes::COMMON_NAME];
        $firstname = $data[ClaimTypes::GIVEN_NAME];
        $lastname = $data[ClaimTypes::SURNAME];
        $email = $data[ClaimTypes::EMAIL_ADDRESS];
        $roles = $data[self::ROLES_ASSERTION_NAME] ?? [ ];
        $type = $this->getUserType($data[SamlClaimTypes::TYPE]);

        if(!is_array($roles)) {
            $roles = [ $roles ];
        }

        if(count($roles) === 0) {
            $roles = [ 'ROLE_USER' ];
        }

        $user
            ->setUsername($username)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setUserType($type)
            ->setRoles($roles);

        if(UserType::Teacher()->equals($type)) {
            $externalId = $data[SamlClaimTypes::EXTERNAL_ID];

            if($externalId !== null) {
                $teacher = $this->teacherRepository->findOneByExternalId($externalId);

                if($teacher === null) {
                    $teacher = $this->teacherRepository->findOneByAcronym($externalId);
                }

                if ($teacher !== null) {
                    $user->setTeacher($teacher);
                } else {
                    $this->logger
                        ->notice(sprintf('Cannot map teacher with internal ID "%s" as such teacher does not exist.', $externalId));
                }
            } else {
                $this->logger
                    ->notice(sprintf('Cannot map teacher with username "%s" as his/her internal ID is not set.', $user->getUsername()));
            }
        } else if(UserType::Student()->equals($type)) {
            $studentId = $data[SamlClaimTypes::EXTERNAL_ID];

            if($studentId !== null) {
                $student = $this->studentRepository->findOneByExternalId($studentId);

                if ($student !== null) {
                    CollectionUtils::synchronize(
                        $user->getStudents(),
                        [$student],
                        function (Student $student) {
                            return $student->getId();
                        }
                    );
                } else {
                    $this->logger
                        ->notice(sprintf('Cannot map student with student ID "%s" as such student does not exist.', $studentId));
                }
            } else {
                $this->logger
                    ->notice(sprintf('Cannot map student with username "%s" as his/her internal ID is not set.', $user->getUsername()));
            }
        } else if(UserType::Parent()->equals($type)) {
            $rawStudentIds = $data[SamlClaimTypes::EXTERNAL_ID];

            if($rawStudentIds !== null) {
                $studentIds = explode(',', $rawStudentIds);
                $students = $this->studentRepository->findAllByExternalId($studentIds);

                CollectionUtils::synchronize(
                    $user->getStudents(),
                    $students,
                    function (Student $student) {
                        return $student->getId();
                    }
                );
            } else {
                $this->logger
                    ->notice(sprintf('Cannot map parent with username "%s" as his/her internal ID is not set.', $user->getUsername()));
            }
        }

        return $user;
    }
}