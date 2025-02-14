<?php

namespace App\Service;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractService
{
    public function __construct(readonly private EntityManagerInterface $em){}

    public function addPayment(Payment $payment): void
    {
        $this->em->persist($payment);
        $this->em->flush();
    }
}
                    