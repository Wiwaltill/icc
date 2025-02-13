<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Tuition;
use App\Section\SectionResolverInterface;
use App\Sorting\RoomNameStrategy;
use App\Sorting\StringStrategy;
use App\Sorting\TuitionStrategy;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ExamType extends AbstractType {

    private TuitionStrategy $tuitionStrategy;
    private StringStrategy $stringStrategy;
    private RoomNameStrategy $roomStrategy;
    private SectionResolverInterface $sectionResolver;

    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(TuitionStrategy $tuitionStrategy, StringStrategy $stringStrategy, RoomNameStrategy $roomStrategy,
                                SectionResolverInterface $sectionResolver, AuthorizationCheckerInterface $authorizationChecker) {
        $this->tuitionStrategy = $tuitionStrategy;
        $this->stringStrategy = $stringStrategy;
        $this->roomStrategy = $roomStrategy;
        $this->sectionResolver = $sectionResolver;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('date', DateType::class, [
                            'label' => 'label.date',
                            'widget' => 'single_text'
                        ])
                        ->add('lessonStart', IntegerType::class, [
                            'label' => 'label.start'
                        ])
                        ->add('lessonEnd', IntegerType::class, [
                            'label' => 'label.end'
                        ])
                        ->add('room', SortableEntityType::class, [
                            'label' => 'label.room',
                            'attr' => [
                                'size' => 10,
                                'data-choice' => 'true'
                            ],
                            'class' => Room::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('r')
                                    ->where('r.isReservationEnabled = true');
                            },
                            'choice_label' => function(Room $room) {
                                return $room->getName();
                            },
                            'sort_by' => $this->roomStrategy,
                            'required' => false,
                            'placeholder' => 'label.select.room',
                            'help' => 'label.room_reservation_hint'
                        ])
                        ->add('description', TextareaType::class, [
                            'label' => 'label.description',
                            'required' => false
                        ]);

                    if($this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR')) {
                        $builder->add('tuitionTeachersCanEditExam', CheckboxType::class, [
                            'label' => 'admin.exams.tuition_teachers_can_edit.label',
                            'help' => 'admin.exams.tuition_teachers_can_edit.help',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                    }
                }
            ])
            ->add('group_tuitions', FieldsetType::class, [
                'legend' => 'label.tuitions',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('tuitions', SortableEntityType::class, [
                            'attr' => [
                                'size' => 10,
                                'disabled' => $this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR') !== true
                            ],
                            'label' => 'label.tuitions',
                            'multiple' => true,
                            'class' => Tuition::class,
                            'query_builder' => function(EntityRepository $repository) {
                                $section = $this->sectionResolver->getCurrentSection();

                                $qb = $repository
                                    ->createQueryBuilder('t')
                                    ->select(['t', 's', 'sg', 'g']);

                                if($section !== null) {
                                    $qb->leftJoin('t.section', 's')
                                        ->leftJoin('t.studyGroup', 'sg')
                                        ->leftJoin('sg.grades', 'g')
                                        ->where('s.id = :section')
                                        ->setParameter('section', $section->getId());
                                }

                                return $qb;
                            },
                            'choice_label' => function(Tuition $tuition) {
                                if($tuition->getName() === $tuition->getStudyGroup()->getName()) {
                                    return sprintf('%s - %s', $tuition->getName(), $tuition->getSubject()->getName());
                                }

                                return sprintf('%s - %s - %s', $tuition->getName(), $tuition->getStudyGroup()->getName(), $tuition->getSubject()->getName());
                            },
                            'group_by' => function(Tuition $tuition) {
                                return implode(', ', $tuition->getStudyGroup()->getGrades()->map(function(Grade $grade) { return $grade->getName(); })->toArray());
                            },
                            'sort_by' => $this->stringStrategy,
                            'sort_items_by' => $this->tuitionStrategy,
                            'disabled' => $this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR') !== true
                        ]);

                    $builder
                        ->add('addStudents', CheckboxType::class, [
                            'required' => false,
                            'mapped' => false,
                            'label' => 'admin.exams.students.add_all',
                            'help' => 'admin.exams.students.info',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                }
            ]);
    }
}