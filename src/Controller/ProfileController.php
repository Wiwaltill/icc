<?php

namespace App\Controller;

use App\Entity\IcsAccessToken;
use App\Entity\User;
use App\Form\NotificationsType;
use App\Grouping\Grouper;
use App\Grouping\UserTypeAndGradeStrategy;
use App\Repository\DeviceTokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\DeviceTokenVoter;
use App\Settings\NotificationSettings;
use App\Sorting\Sorter;
use App\Sorting\StringGroupStrategy;
use App\Sorting\UserUsernameStrategy;
use App\Utils\EnumArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController {

    private const RemoveAppCrsfTokenKey = '_remove_app_csrf';

    /**
     * @Route("", name="profile")
     */
    public function index() {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @Route("/notifications", name="profile_notifications")
     */
    public function notifications(Request $request, UserRepositoryInterface $userRepository, NotificationSettings $notificationSettings) {
        /** @var User $user */
        $user = $this->getUser();

        $allowedEmailUserTypes = $notificationSettings->getEmailEnabledUserTypes();
        $isAllowed = EnumArrayUtils::inArray($user->getUserType(), $allowedEmailUserTypes) !== false;

        $form = null;

        if($isAllowed === true) {
            $form = $this->createForm(NotificationsType::class, $user, [
                'allow_email' => $isAllowed
            ]);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $userRepository->persist($user);
                $this->addFlash('success', 'profile.notifications.success');

                return $this->redirectToRoute('profile_notifications');
            }
        }

        return $this->render('profile/notifications.html.twig', [
            'form' => $form !== null ? $form->createView() : null,
            'is_allowed' => $isAllowed,
            'email_allowed' => $isAllowed
        ]);
    }

    /**
     * @Route("/apps", name="profile_apps")
     */
    public function apps(DeviceTokenRepositoryInterface $deviceTokenRepository) {
        /** @var User $user */
        $user = $this->getUser();

        $devices = $deviceTokenRepository->findAllBy($user);

        return $this->render('profile/apps.html.twig', [
            'apps' => $devices,
            'csrf_key' => self::RemoveAppCrsfTokenKey
        ]);
    }

    /**
     * @Route("/apps/{uuid}/remove", name="profile_remove_app", methods={"POST"})
     */
    public function removeApp(IcsAccessToken $token, Request $request, DeviceTokenRepositoryInterface $deviceTokenRepository) {
        $this->denyAccessUnlessGranted(DeviceTokenVoter::Remove, $token);

        $csrfToken = $request->request->get('_csrf_token');
        if($this->isCsrfTokenValid(self::RemoveAppCrsfTokenKey, $csrfToken)) {
            $deviceTokenRepository->remove($token);

            $this->addFlash('success', 'profile.apps.remove.success');
        } else {
            $this->addFlash('success', 'profile.apps.remove.error.csrf');
        }

        return $this->redirectToRoute('profile_apps');
    }

    /**
     * @Route("/switch", name="switch_user")
     * @Security("is_granted('ROLE_ALLOWED_TO_SWITCH')")
     */
    public function switchUser(Grouper $grouper, Sorter $sorter, UserRepositoryInterface $userRepository, SectionResolverInterface $sectionResolver) {
        $users = $userRepository->findAll();
        $groups = $grouper->group($users, UserTypeAndGradeStrategy::class, ['section' => $sectionResolver->getCurrentSection()]);
        $sorter->sort($groups, StringGroupStrategy::class);
        $sorter->sortGroupItems($groups, UserUsernameStrategy::class);

        return $this->render('profile/switch.html.twig', [
            'groups' => $groups
        ]);
    }
}