<?php

namespace App\Controller;

use App\Book\Grade\GradeOverviewHelper;
use App\Book\Grade\GradePersister;
use App\Entity\Section;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\User;
use App\Repository\TuitionRepositoryInterface;
use App\Settings\TuitionGradebookSettings;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TuitionFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book/gradebook')]
class TuitionGradebookController extends AbstractController {

    #[Route('', name: 'gradebook')]
    public function index(Request $request, TuitionFilter $tuitionFilter, StudentFilter $studentFilter,
                          SectionFilter $sectionFilter, TuitionRepositoryInterface $tuitionRepository,
                          GradeOverviewHelper $gradeOverviewHelper, TuitionGradebookSettings $gradebookSettings, GradePersister $gradePersister): Response {
        /** @var User $user */
        $user = $this->getUser();
        
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user, true);

        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);

        $overview = null;

        if($tuitionFilterView->getCurrentTuition() !== null) {
            $overview = $gradeOverviewHelper->computeOverviewForTuition($tuitionFilterView->getCurrentTuition());
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $overview = $gradeOverviewHelper->computeOverviewForStudent($studentFilterView->getCurrentStudent(), $sectionFilterView->getCurrentSection());
        }

        if($request->isMethod('POST')) {
            if($this->isCsrfTokenValid('gradebook', $request->request->get('_csrf_token')) !== true) {
                $this->addFlash('error', 'CSRF token invalid.');
            } else {
                if($tuitionFilterView->getCurrentTuition() !== null) {
                    $gradePersister->persist($tuitionFilterView->getCurrentTuition(), $overview, $request->request->all('grades'));
                } else if($studentFilterView->getCurrentStudent() !== null) {
                    $gradePersister->persist($studentFilterView->getCurrentStudent(), $overview, $request->request->all('grades'));
                }

                $this->addFlash('success', 'book.grades.success');
                return $this->redirectToRequestReferer('gradebook');
            }
        }

        return $this->render('books/grades/overview.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'studentFilter' => $studentFilterView,
            'ownTuitions' => $ownTuitions,
            'overview' => $overview,
            'key' => $gradebookSettings->getEncryptedMasterKey()
        ]);
    }

    /**
     * @return Tuition[]
     */
    private function resolveOwnTuitions(?Section $currentSection, User $user, TuitionRepositoryInterface $tuitionRepository): array {
        if($currentSection === null) {
            return [ ];
        }

        $tuitions = [ ];

        if ($user->isStudentOrParent()) {
            $tuitions = $tuitionRepository->findAllByStudents($user->getStudents()->toArray(), $currentSection);
        } else if ($user->isTeacher()) {
            $tuitions = $tuitionRepository->findAllByTeacher($user->getTeacher(), $currentSection);
        }

        return array_filter($tuitions, fn(Tuition $tuition) => $tuition->getGradeCategories()->count() > 0);
    }
}