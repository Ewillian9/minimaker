<?php

namespace App\Controller;

use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription', methods: ['POST'])]
    public function subscription(Request $request, PaymentService $ps): Response
    {
        $subscription = $this->getUser()->getSubscription();

        if ($subscription == null || $subscription->isActive() === false) {
            $checkoutUrl = $ps->setPayment(
                $this->getUser(), 
                intval($request->get('plan'))
            );
            return $this->redirect($checkoutUrl);
        }

        $this->addFlash('warning', "Vous êtes déjà abonné(e)");
        return $this->redirectToRoute('app_profile');
    }
}
