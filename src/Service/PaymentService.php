<?php

namespace App\Service;

use App\Service\AbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/*
 * Classe PaymentService dédiée à la gestion du 
 * paiement des abonnements des utilisateurs
*/
class PaymentService extends AbstractService
{
    public function __construct(
        private readonly ParameterBagInterface $params,
        private readonly EntityManagerInterface $em
    ) 
    {
        $this->params = $params;
        $this->em = $em;
        // parent::__construct();
    }

    public function setPayment(): void
    {
        $this->params->get('STRIPE_SK');
    }
}