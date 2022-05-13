<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\SearchType;
use App\Entity\Conversation;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Symfony\Component\Mercure\Update;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ConversationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChatController extends AbstractController
{
    private $tokenStorageInterface;
    private $messageRepository;
    private $doctrine;
    private $conversationRepository;
    private $userRepository;

    public function __construct(TokenStorageInterface $tokenStorageInterface, MessageRepository $messageRepository, ManagerRegistry $doctrine, ConversationRepository $conversationRepository, UserRepository $userRepository)
    {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->messageRepository = $messageRepository;
        $this->doctrine = $doctrine;
        $this->conversationRepository = $conversationRepository;
        $this->userRepository = $userRepository;
    }
    
    #[Route('/chat/{id}', name: 'app_chat')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function chat(Conversation $conversation, Request $request): Response
    {
        $messages = $this->messageRepository->findMessageByConversationId(
            $conversation->getId()
        );
        $conversations = $this->conversationRepository->findConversationsByUser($this->tokenStorageInterface->getToken()->getUser());
        $form = $this->createFormBuilder()
            ->add('message', TextType::class, ['attr' => ['autocomplete' => 'off']])
            ->getForm();

        $user = $this->userRepository->findOneBy(['username' => $conversations[0]['username']]);
        $new = $this->messageRepository->new($this->tokenStorageInterface->getToken()->getUser(), $user);
        $users = null;
        $formMessage = $this->createForm(SearchType::class);
        if ($request->isMethod('POST')) {
            $formMessage->submit($request->request->get($formMessage->getName()));
            if ($formMessage->isSubmitted() && $formMessage->isValid()) {
                $pseudo = $formMessage->get('username')->getData();
                $users = $this->userRepository->search($pseudo);
                $conversationAll = $this->conversationRepository->searchParticipant($this->tokenStorageInterface->getToken()->getUser(), $pseudo);
                if ($conversationAll != null) {
                    foreach($conversationAll as $key => $value) {
                        $valeur = $conversationAll[0]['username'];
                        $last = $conversationAll[0]['content'];
                        $date = $conversationAll[0]['createdAt'];
                        $conversationId = $conversationAll[0]['conversationId'];
                        
                        if($valeur != $this->tokenStorageInterface->getToken()->getUser()) {
                            $this->addFlash(
                                'success',
                                ['valeur' => $valeur,
                                'last' => $last,
                                'date' => $date,
                                'conversationId' => $conversationId]
                            );
                        } else {
                            $this->addFlash(
                                'success',
                                'C\'est vous!'
                            );
                        }
                    }
                    return $this->redirectToRoute('app_chat', ['id' => $conversation->getId(), '_fragment' => 'last']);
                } else {
                    $this->addFlash(
                        'success',
                        'aucun'
                    );
                }
                return $this->redirectToRoute('app_chat', ['id' => $conversation->getId(), '_fragment' => 'last']);
            }
        }
        if ($new) {
            foreach($new as $value) {
                $entityManager = $this->doctrine->getManager();
                $value->setNew(false);
                $entityManager->persist($value);
                $entityManager->flush();
            }
            
        }
        $emptyForm = clone $form; // Used to display an empty form after a POST request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager = $this->doctrine->getManager();
            $message = new Message();
            $message->setContent($form->get('message')->getData());
            $message->setUser($this->tokenStorageInterface->getToken()->getUser());

            $conversation->addMessage($message);
            $conversation->setLastMessage($message);
            $entityManager->persist($conversation);
            $entityManager->persist($message);
            $entityManager->flush();
            
            // 🔥 The magic happens here! 🔥
            // The HTML update is pushed to the client using Mercure
            
            $form = $emptyForm;
            return $this->redirectToRoute('app_chat', ['id' => $conversation->getId(), '_fragment' => 'last']);
            // Force an empty form to be rendered below
            // It will replace the content of the Turbo Frame after a post
        }
        $messageLast = $this->messageRepository->last($conversation->getId());
        return $this->renderForm('chat/index.html.twig', [
            'form' => $form,
            'formMessage' => $formMessage,
            'message' => $messages,
            'user' => $conversations,
            'last' => $messageLast,
            'new' => $new
         ]);
    }
}
