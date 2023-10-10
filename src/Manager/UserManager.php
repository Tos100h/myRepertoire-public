<?php

namespace App\Manager;

use App\Entity\User;
use Symfony\Component\Form\Form;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function setAdmin(User $user): User {
        $roles = ['ROLE_ADMIN'];
        $user = $this->setUserRoles($user, $roles);

        return $user;
    }

    public function setDemo(User $user): User {
        $roles = ['ROLE_DEMO'];
        $user = $this->setUserRoles($user, $roles);

        return $user;
    }
 
    public function setRegularUser(User $user): User {
        $roles = [];
        $user = $this->setUserRoles($user, $roles);

        return $user;
    }

    public function setUserRoles(User $user, Array $roles): User {
        $user->setRoles($roles);
        $this->userRepository->add($user, true);

        return $user;
    }

    public function changePassword(User $user, Form $form): User {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );       
        $this->userRepository->add($user, true);

        return $user;
    }

    public function delete(User $user) {
        $userRepository->remove($user, true);
    }

} 