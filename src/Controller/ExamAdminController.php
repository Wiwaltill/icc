<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\User;
use App\Form\ExamBulkType;
use App\Form\ExamStudentsType;
use App\Form\ExamType;
use App\Grouping\ExamWeekStrategy;
use App\Grouping\Grouper;
use App\Repository\ExamRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Sorting\ExamDateGroupStrategy as ExamDateSortingStrategy;
use App\Sorting\ExamWeekGroupStrategy;
use App\Sorting\Sorter;
use App\Utils\CollectionUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/exams")
 */
class ExamAdminController extends AbstractController {

    private const NumberOfExams = 25;

    private ExamRepositoryInterface $repository;

    public function __construct(RefererHelper $redirectHelper, ExamRepositoryInterface $examRepository) {
        parent::__construct($redirectHelper);

        $this->repository = $examRepository;
    }

    /**
     * @Route("", name="admin_exams")
     */
    public function index(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter, Grouper $grouper, ExamRepositoryInterface $examRepository, Sorter $sorter, Request $request) {
        $this->denyAccessUnlessGranted(ExamVoter::Manage);

        $page = $request->query->getInt('page');

        /** @var User $user */
        $user = $this->getUser();
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $request->query->get('teacher') !== '✗' && $gradeFilterView->getCurrentGrade() === null);

        $paginator = $this->repository->getPaginator(self::NumberOfExams,
            $page,
            $gradeFilterView->getCurrentGrade(),
            $teacherFilterView->getCurrentTeacher(),
            null,
            null,
            false,
            null,
            null,
            $sectionFilterView->getCurrentSection()
        );
        $pages = 1;

        if($paginator->count() > 0) {
            $pages = ceil((float)$paginator->count() / self::NumberOfExams);
        }

        $exams = [ ];

        foreach($paginator->getIterator() as $exam) {
            if($this->isGranted(ExamVoter::Edit, $exam)) {
                $exams[] = $exam;
            }
        }

        $groups = $grouper->group($exams, ExamWeekStrategy::class);
        $sorter->sort($groups, ExamWeekGroupStrategy::class);
        $sorter->sortGroupItems($groups, ExamDateSortingStrategy::class);

        return $this->render('admin/exams/index.html.twig', [
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/add", name="new_exam")
     */
    public function add(Request $request) {
        $this->denyAccessUnlessGranted(ExamVoter::Add);
        $exam = new Exam();

        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->get('group_tuitions')->get('addStudents')->getData() === true) {
                $this->addAllStudents($exam);
            }

            $this->repository->persist($exam);

            $this->addFlash('success', 'admin.exams.add.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/bulk", name="bulk_exams")
     */
    public function addBulk(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_EXAMS_CREATOR');

        $defaultData = [
            'number' => 3,
            'tuitions' => [ ],
            'add_students' => true
        ];

        $form = $this->createForm(ExamBulkType::class, $defaultData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->bulkCreateExams($data['number'], $data['tuitions'], $data['add_students']);

            $this->addFlash('success', 'admin.exams.bulk.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/bulk.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_exam")
     */
    public function edit(Exam $exam, Request $request) {
        $this->denyAccessUnlessGranted(ExamVoter::Edit, $exam);

        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->get('group_tuitions')->get('addStudents')->getData() === true) {
                $this->addAllStudents($exam);
            }

            $this->repository->persist($exam);

            $this->addFlash('success', 'admin.exams.edit.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/edit.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    /**
     * @Route("/{uuid}/unplan", name="unplan_exam")
     */
    public function unplan(Exam $exam, Request $request, TranslatorInterface $translator) {
        $this->denyAccessUnlessGranted(ExamVoter::Unplan, $exam);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.exams.unplan.confirm',
            'message_parameters' => [
                '%date%' => $exam->getDate()->format($translator->trans('date.format')),
                '%lessons%' => $translator->trans('label.exam_lessons', [
                    '%start%' => $exam->getLessonStart(),
                    '%end%' => $exam->getLessonEnd(),
                    '%count%' => $exam->getLessonEnd() - $exam->getLessonStart() + 1
                ])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $exam->setDate(null);
            $exam->setLessonStart(0);
            $exam->setLessonEnd(0);

            $this->repository->persist($exam);

            $this->addFlash('success', 'admin.exams.unplan.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/unplan.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_exam")
     */
    public function remove(Exam $exam, Request $request, TranslatorInterface $translator) {
        $this->denyAccessUnlessGranted(ExamVoter::Remove, $exam);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.exams.remove.confirm',
            'message_parameters' => [
                '%date%' => $exam->getDate() !== null ? $exam->getDate()->format($translator->trans('date.format')) : 'N/A',
                '%lessons%' => $translator->trans('label.exam_lessons', [
                    '%start%' => $exam->getLessonStart(),
                    '%end%' => $exam->getLessonEnd(),
                    '%count%' => $exam->getLessonEnd() - $exam->getLessonStart() + 1
                ])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($exam);

            $this->addFlash('success', 'admin.exams.remove.success');

            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/remove.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    /**
     * @Route("/{uuid}/students", name="edit_exam_students")
     */
    public function students(Exam $exam, Request $request) {
        $form = $this->createForm(ExamStudentsType::class, $exam);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($exam);

            $this->addFlash('success', 'admin.exams.students.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/students.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    /**
     * Adds all students of the related tuitions to the exam (which means they attend to this exam)
     *
     * @param Exam $exam
     */
    private function addAllStudents(Exam $exam): void {
        $students = [];

        /** @var Tuition $tuition */
        foreach ($exam->getTuitions() as $tuition) {
            /** @var StudyGroupMembership $membership */
            foreach ($tuition->getStudyGroup()->getMemberships() as $membership) {
                $students[] = $membership->getStudent();
            }
        }

        CollectionUtils::synchronize(
            $exam->getStudents(),
            $students,
            function (Student $student) {
                return $student->getId();
            }
        );
    }

    /**
     * @param int $number
     * @param Tuition[] $tuitions
     * @param bool $addStudents
     */
    private function bulkCreateExams(int $number, array $tuitions, bool $addStudents): void {
        $this->repository->beginTransaction();

        foreach($tuitions as $tuition) {
            $students = $tuition->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
                return $membership->getStudent();
            })->toArray();

            for($i = 0; $i < $number; $i++) {
                $exam = new Exam();
                $exam->addTuition($tuition);

                if($addStudents === true) {
                    foreach ($students as $student) {
                        $exam->addStudent($student);
                    }
                }

                $this->repository->persist($exam);
            }
        }

        $this->repository->commit();
    }
}