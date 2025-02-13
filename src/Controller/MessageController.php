<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class MessageController extends AbstractController
{
    public function __construct(
        private DiscussionRepository $dr,
        private MessageRepository $mr,
        private EntityManagerInterface $em,
    ) {}

    #[Route('/messages', name: 'app_message', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', []);
    }

    #[Route('/messages/{id}', name: 'app_message_show', methods: ['GET', 'POST'])]
    public function show($id, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $mes = new Message();
            $mes
                ->setDiscussion($this->dr->find($id))
                ->setUser($this->getUser())
                ->setContent($request->get('message'))
                ->setStatus(true)
            ;

            $this->em->persist($mes);
            $this->em->flush();

            if ($request->headers->get('HX-Request')) {
                return $this->render('message/_message.html.twig', [
                    'item' => $mes
                ]);
            }
        }

        return $this->render('message/show.html.twig', [
            'messages' => $this->mr->findByDiscussion($id, ['created_at' => 'DESC']),
        ]);
    }
}
