<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="app_admin")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/set-user/{user}", name="app_admin_set_regular")
     */
    public function setRegularUser(User $user, UserManager $userManager): Response
    {
        $user = $userManager->setRegularUser($user);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/set-admin/{user}", name="app_admin_set_admin")
     */
    public function setAdmin(User $user, UserManager $userManager): Response
    {
        $user = $userManager->setAdmin($user);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/set-demo/{user}", name="app_admin_set_demo")
     */
    public function setDemo(User $user, UserManager $userManager): Response
    {
        $user = $userManager->setDemo($user);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete/{user}", name="app_admin_delete_user")
     */
    public function delete(Request $request, User $user, UserManager $userManager): Response
    {     
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $name = $user->getName();
            $userManager->delete($user);
            $this->addFlash('error', 'User : '.$name.' deleted');
        }
 
        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/reset-password/{user}", name="app_admin_reset_pwd")
     * @IsGranted("ROLE_ADMIN")
     */
    public function resetPwd(User $user, Request $request, UserManager $userManager): Response
    {
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {          
            $user = $userManager->changePassword($user, $form);
            $this->addFlash('success', 'reset pwd for user : '.$user->getName());

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('admin/resetPwd.html.twig', [
            'user' => $user->getName(),
            'form' => $form->createView(),
        ]);
    }
}
