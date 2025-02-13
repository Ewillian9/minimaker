<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\LoginHistory;
use Symfony\Component\Mime\Email;
use DeviceDetector\DeviceDetector;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Classe de gestion de l'historique de connexion des utilisateurs
 */

class LoginHistoryService extends AbstractController
{
    public function __construct(readonly private EntityManagerInterface $em, MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    public function addHistory(User $user, string $userAgent, string $ip): void
    {
        $deviceDetector = new DeviceDetector($userAgent);
        $deviceDetector->parse();

        $loginHistory = new LoginHistory();
        $loginHistory
            ->setUser($user)
            ->setIpAddress($ip)
            ->setDevice($deviceDetector->getDeviceName())
            ->setOs($deviceDetector->getOs()['name'])
            ->setBrowser($deviceDetector->getClient()['name'])
            ;
        $this->em->persist($loginHistory);
        $this->em->flush();

        $email = (new TemplatedEmail())
            ->from(new Address('contact@miniamaker.fr', 'miniamaker'))
            ->to((string) $user->getEmail())
            ->subject('Nouvelle connexion détéctée')
            ->htmlTemplate('security/login_email.html.twig');

        $this->mailer->send($email);
    }
}