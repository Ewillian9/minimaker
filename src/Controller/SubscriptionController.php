<?php

namespace App\Controller;

use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription', methods: ['POST'])]
    public function subscription(Request $request, PaymentService $ps): RedirectResponse
    {
        try {
            $subscription = $this->getUser()->getSubscription();

            if ($subscription == null || $subscription->isActive() === false) {
                $checkoutUrl = $ps->setPayment(
                    $this->getUser(),
                    intval($request->get('plan'))
                );
                return $this->redirectToRoute('app_subscription_check', ['link' => $checkoutUrl]);
                // return new RedirectResponse($checkoutUrl);
            }

            $this->addFlash('warning', "Vous êtes déjà abonné(e)");
            return $this->redirectToRoute('app_profile');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la création du paiement');
            return $this->redirectToRoute('app_profile');
        }
    }

    #[Route('/subscription/check', name: 'app_subscription_check')]
    public function check(Request $request): Response
    {
        // Logique de traitement du succès
        return $this->render('subscription/check.html.twig', [
            'link' => $request->get('link'),
        ]);
    }

    #[Route('/subscription/success', name: 'app_subscription_success')]
    public function success(EntityManagerInterface $em): Response
    {
        // Logique de traitement du succès
        $subscription = $this->getUser()->getSubscription();
        
        $subscription->setIsActive(true);

        $em->persist($subscription);
        $em->flush();

        $this->addFlash('success', 'Votre abonnement a bien ete pris en compte. Merci!');

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/subscription/cancel', name: 'app_subscription_cancel')]
    public function cancel(): Response
    {
        // Logique de traitement de l'annulation
        $this->addFlash('error', "Votre souscription a ete annulé ou une erreur s'est produite");
        return $this->redirectToRoute('app_profile');
    }
}
