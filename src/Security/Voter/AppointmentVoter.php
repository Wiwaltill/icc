<?php

namespace App\Security\Voter;

use App\Entity\Appointment;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Settings\AppointmentsSettings;
use App\Utils\EnumArrayUtils;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AppointmentVoter extends Voter {

    const New = 'new-appointment';
    const Edit = 'edit';
    const Remove = 'remove';
    const View = 'view';
    const Confirm = 'confirm';

    private AppointmentsSettings $settings;
    private DateHelper $dateHelper;
    private AccessDecisionManagerInterface $accessDecisionManager;

    public function __construct(AppointmentsSettings $settings, DateHelper $dateHelper, AccessDecisionManagerInterface $accessDecisionManager) {
        $this->settings = $settings;
        $this->dateHelper = $dateHelper;
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::Edit,
            self::Remove,
            self::View,
            self::Confirm
        ];

        return $attribute === self::New ||
              (in_array($attribute, $attributes) && $subject instanceof Appointment);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch ($attribute) {
            case self::New:
                return $this->canCreate($token);

            case self::Edit:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);

            case self::Confirm:
                return $this->canConfirm($token);

            case self::View:
                return $this->canView($subject, $token);
        }

        throw new \LogicException('This code should be reached.');
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENT_CREATOR']);
    }

    private function canConfirm(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']);
    }

    private function canEdit(Appointment $appointment, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']) === true) {
            return true;
        }

        if($appointment->getCreatedBy() === null) {
            return false;
        }

        /** @var User|null $user */
        $user = $token->getUser();

        if($user === null) {
            return false;
        }

        return $appointment->getCreatedBy()->getId() === $user->getId();
    }

    private function canRemove(Appointment $appointment, TokenInterface $token): bool {
        return $this->canEdit($appointment, $token);
    }

    private function canView(Appointment $appointment, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']) || $this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        $start = $this->settings->getStart($user->getUserType());
        $end = $this->settings->getEnd($user->getUserType());
        $today = $this->dateHelper->getToday();

        if($start === null || $start > $today || ($end !== null && $end < $today)) {
            return false;
        }

        $isStudentOrParent = EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ]);

        if($isStudentOrParent !== true) {
            // Everyone but students and parents may pass
            return true;
        }

        // Check confirmation status (students and parents must not see non-confirmed appointments)
        if($appointment->isConfirmed() === false) {
            return false;
        }

        // Check visibility (students and parents only)
        if($this->checkVisibility($appointment, $user->getUserType()) !== true) {
            return false;
        }

        $appointmentStudyGroupsIds = $appointment->getStudyGroups()
            ->map(function(StudyGroup $studyGroup) {
                return $studyGroup->getId();
            })
            ->toArray();

        /** @var Student[] $students */
        $students = $user->getStudents();

        foreach($students as $student) {
            $studentStudyGroupsIds = $student->getStudyGroupMemberships()
                ->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudyGroup()->getId();
                })
                ->toArray();

            $intersection = array_intersect($appointmentStudyGroupsIds, $studentStudyGroupsIds);

            if(count($intersection) > 0) {
                return true;
            }
        }

        return false;
    }

    private function checkVisibility(Appointment $appointment, UserType $userType): bool {
        foreach($appointment->getVisibilities() as $visibility) {
            if($visibility->getUserType()->equals($userType)) {
                return true;
            }
        }

        return false;
    }
}