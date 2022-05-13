<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchType;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HomeController extends AbstractController
{
    /** @var TokenStorageInterface */
    private $tokenStorageInterface;
    /** @var ConversationRepository */
    private $conversationRepository;
    /** @var MessageRepository */
    private $messageRepository;
    /** @var UserRepository */
    private $userRepository;
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(ConversationRepository $conversationRepository, TokenStorageInterface $tokenStorageInterface, MessageRepository $messageRepository, UserRepository $userRepository, ManagerRegistry $doctrine)
    {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->conversationRepository = $conversationRepository;
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
        $this->doctrine = $doctrine;
    }

    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function index(Request $request): Response
    {
        $conversations = $this->conversationRepository->findConversationsByUser($this->tokenStorageInterface->getToken()->getUser());
        $user = $this->userRepository->findOneBy(['username' => $conversations[0]['username']]);
        $new = $this->messageRepository->new($this->tokenStorageInterface->getToken()->getUser(), $user);
        if ($new) {
            foreach ($new as $value) {
                $entityManager = $this->doctrine->getManager();
                $value->setNew(false);
                $entityManager->persist($value);
                $entityManager->flush();
            }
        }
        $userform = new User();
        $users = null;
        $form = $this->createForm(SearchType::class);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                $pseudo = $form->get('username')->getData();
                $users = $this->userRepository->search($pseudo);
                if (null != $users) {
                    foreach ($users as $key => $value) {
                        $valeur = $value->getUsername();
                        if ($valeur != $this->tokenStorageInterface->getToken()->getUser()) {
                            $this->addFlash(
                                'success',
                                ['valeur' => $valeur]
                            );
                        } else {
                            $this->addFlash(
                                'success',
                                'C\'est vous!'
                            );
                        }
                    }

                    return $this->redirectToRoute('app_home');
                } else {
                    $this->addFlash(
                        'success',
                        'Aucun utilisateur trouvé!'
                    );
                }

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('home/index.html.twig', [
            'user' => $conversations,
            'new' => $new,
            'form' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
    }
}
