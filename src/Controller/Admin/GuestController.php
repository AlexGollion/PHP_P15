<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\UserType;
use App\Entity\User;
use App\Repository\UserRepository;

#[IsGranted('ROLE_ADMIN')]
final class GuestController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager,
    private UserPasswordHasherInterface $passwordHasher){
    }

    #[Route('/admin/guests', name: 'admin_guests_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $guests = $this->userRepository->findBy(
            ['admin' => false],
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );
        $total = $this->userRepository->count(['admin' => false]);

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
            'page' => $page,
            'total' => $total
        ]);
    }

    #[Route('/admin/guests/add', name: 'admin_guests_add')]
    public function add(Request $request): Response
    {
        $guest = new User();
        $form = $this->createForm(UserType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guest->setAdmin(false);
            $guest->setPassword($this->passwordHasher->hashPassword($guest, $guest->getPassword()));
            $this->entityManager->persist($guest);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_guests_index');
        }

        return $this->render('admin/guest/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
