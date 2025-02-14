<?php

namespace App\Controller;

use App\Entity\Subscription;
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
    private Subscription $subscription; 
    public function __construct(
        private EntityManagerInterface $em
    ){
        $this->subscription = $this->getUser()->getSubscription();
    }

    #[Route('/subscription', name: 'app_subscription', methods: ['POST'])]
    public function subscription(Request $request, PaymentService $ps): RedirectResponse
    {
        try {
            if ($this->subscription == null || !$this->subscription->isActive()) {
                $checkoutUrl = $ps->setPayment(
                    $this->getUser(),
                    intval($request->get('plan'))
                );
                return $this->redirectToRoute('app_subscription_check', ['link' => $checkoutUrl]);
            }

            $this->addFlash('warning', "Vous êtes déjà abonné(e)");
            return $this->redirectToRoute('app_profile');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la création du paiement');
            return $this->redirectToRoute('app_profile');
        }
    }

    #[Route('/subscription/check', name: 'app_subscription_check', methods:['GET'])]
    public function check(Request $request): Response
    {
        // Logique de traitement du succès
        return $this->render('subscription/check.html.twig', [
            'link' => $request->get('link'),
        ]);
    }

    #[Route('/subscription/success', name: 'app_subscription_success', methods:['GET'])]
    public function success(): Response
    {
        // Logique de traitement du succès
        $this->subscription = $this->getUser()->getSubscription();
        
        $this->subscription->setIsActive(true);

        $this->em->persist($this->subscription);
        $this->em->flush();

        $this->addFlash('success', 'Votre abonnement a bien ete pris en compte. Merci!');

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/subscription/cancel', name: 'app_subscription_cancel', methods:['GET'])]
    public function cancel(): Response
    {
        // Logique de traitement de l'annulation
        $this->addFlash('error', "Votre souscription a ete annulé ou une erreur s'est produite");
        return $this->redirectToRoute('app_profile');
    }
}
