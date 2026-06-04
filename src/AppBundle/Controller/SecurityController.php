<?php

namespace AppBundle\Controller;

use AppBundle\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use UserBundle\Entity\User;

class SecurityController extends BaseController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private UserAuthenticatorInterface $userAuthenticator,
        private LoginFormAuthenticator $authenticator,
    ) {
    }

    public function loginAction(Request $request, AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('cabinet');
        }

        return $this->render('@App/Security/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error'         => $authUtils->getLastAuthenticationError(),
        ]);
    }

    public function registerAction(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('cabinet');
        }

        $error         = null;
        $lastEmail     = '';
        $lastFirstname = '';
        $lastLastname  = '';
        $lastPhone     = '';

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('register', $request->request->get('_csrf_token'))) {
                $error = 'frontend.register.error.invalid_csrf';
            } else {
                $email     = trim($request->request->get('email', ''));
                $password  = $request->request->get('password', '');
                $firstname = trim($request->request->get('firstname', ''));
                $lastname  = trim($request->request->get('lastname', ''));
                $phone     = trim($request->request->get('phone', ''));

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'frontend.register.error.invalid_email';
                } elseif (mb_strlen($password) < 6) {
                    $error = 'frontend.register.error.weak_password';
                } elseif ($this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
                    $error = 'frontend.register.error.email_exists';
                } else {
                    $user = new User();
                    $user->setEmail($email);
                    $user->setUsername($email);
                    $user->setEnabled(true);
                    $user->setPassword($this->passwordHasher->hashPassword($user, $password));
                    $user->setFirstname($firstname ?: null);
                    $user->setLastname($lastname ?: null);
                    $user->setPhone($phone ?: null);

                    $this->em->persist($user);
                    $this->em->flush();

                    return $this->userAuthenticator->authenticateUser(
                        $user,
                        $this->authenticator,
                        $request
                    );
                }

                $lastEmail     = $email;
                $lastFirstname = $firstname;
                $lastLastname  = $lastname;
                $lastPhone     = $phone;
            }
        }

        return $this->render('@App/Security/register.html.twig', [
            'error'          => $error,
            'last_email'     => $lastEmail,
            'last_firstname' => $lastFirstname,
            'last_lastname'  => $lastLastname,
            'last_phone'     => $lastPhone,
        ]);
    }
}