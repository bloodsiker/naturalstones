<?php

namespace AppBundle\Controller;

use AppBundle\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use UserBundle\Entity\User;

class SecurityController extends BaseController
{
    private const MIN_PASSWORD_LENGTH = 6;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserAuthenticatorInterface $userAuthenticator,
        private readonly LoginFormAuthenticator $authenticator,
    ) {
    }

    public function loginAction(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('cabinet');
        }

        return $this->render('@App/Security/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error' => $authUtils->getLastAuthenticationError(),
        ]);
    }

    public function registerAction(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('cabinet');
        }

        $error = null;
        $lastEmail = '';
        $lastFirstname = '';
        $lastLastname = '';
        $lastPhone = '';

        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));
            $password = $request->request->get('password', '');
            $firstname = trim($request->request->get('firstname', ''));
            $lastname = trim($request->request->get('lastname', ''));
            $phone = trim($request->request->get('phone', ''));

            $error = $this->validateRegistration($request, $email, $password);

            if (null === $error) {
                $user = $this->createUser($email, $password, $firstname, $lastname, $phone);

                $this->em->persist($user);
                $this->em->flush();

                return $this->userAuthenticator->authenticateUser($user, $this->authenticator, $request);
            }

            $lastEmail = $email;
            $lastFirstname = $firstname;
            $lastLastname = $lastname;
            $lastPhone = $phone;
        }

        return $this->render('@App/Security/register.html.twig', [
            'error' => $error,
            'last_email' => $lastEmail,
            'last_firstname' => $lastFirstname,
            'last_lastname' => $lastLastname,
            'last_phone' => $lastPhone,
        ]);
    }

    private function validateRegistration(Request $request, string $email, string $password): ?string
    {
        if (!$this->isCsrfTokenValid('register', $request->request->get('_csrf_token'))) {
            return 'frontend.register.error.invalid_csrf';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'frontend.register.error.invalid_email';
        }
        if (mb_strlen($password) < self::MIN_PASSWORD_LENGTH) {
            return 'frontend.register.error.weak_password';
        }
        if ($this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return 'frontend.register.error.email_exists';
        }

        return null;
    }

    private function createUser(
        string $email,
        string $password,
        string $firstname,
        string $lastname,
        string $phone,
    ): User {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setEnabled(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setFirstname($firstname ?: null);
        $user->setLastname($lastname ?: null);
        $user->setPhone($phone ?: null);

        return $user;
    }
}
