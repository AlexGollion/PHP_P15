<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Repository\UserRepository;

#[IsGranted('ROLE_ADMIN')]
final class GuestController extends AbstractController
{
    public function __construct(private UserRepository $userRepository){
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
}
