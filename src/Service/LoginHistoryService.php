<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\LoginHistory;
use DeviceDetector\DeviceDetector;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Classe de gestion de l'historique de connexion des utilisateurs
 */

class LoginHistoryService
{
    public function __construct(readonly private EntityManagerInterface $em){}

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
    }
}