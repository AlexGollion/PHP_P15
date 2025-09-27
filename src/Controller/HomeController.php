<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;

class HomeController extends AbstractController
{

    public function __construct(private UserRepository $userRepository, private AlbumRepository $albumRepository, 
    private MediaRepository $mediaRepository){
        
    }

    #[Route("/", name: "home")]
    public function home()
    {
        return $this->render('front/home.html.twig');
    }

    #[Route("/guests", name: "guests")]
    public function guests(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $guests = $this->userRepository->findBy(
            ['admin' => false, 'blocked' => false],
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );
        $total = $this->userRepository->count(['admin' => false, 'blocked' => false]);

        return $this->render('front/guests.html.twig', [
            'guests' => $guests,
            'page' => $page,
            'total' => $total
        ]);
    }

    #[Route("/guest/{id}", name: "guest")]
    public function guest(Request $request, int $id)
    {

        $guest = $this->userRepository->find($id);

        if ($guest->isBlocked()) {
            return $this->redirectToRoute('guests');
        }

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 25;

        $result = $this->mediaRepository->findByUserPaginated($guest, $page, $limit);

        $total = (int) ceil($result['total'] / $limit);

        return $this->render('front/guest.html.twig', [
            'guest' => $guest,
            'medias' => $result['medias'],
            'page' => $page,
            'total' => $total
        ]);
    }

    #[Route("/portfolio/{id}", name: "portfolio")]
    public function portfolio(Request $request, ?int $id = null)
    {
        $albums = $this->albumRepository->findAll();
        $album = $id ? $this->albumRepository->find($id) : null;
        $user = $this->userRepository->findOneByAdmin(true);

        $page = max(1, $request->query->getInt('page', 1)); 
        $limit = 25;

        $medias = $album
            ? $this->mediaRepository->findBy(['album' => $album])
            : $this->mediaRepository->findByUserPaginated($user, $page, $limit);

        if (isset($medias['total'])) {
            $total = (int) ceil($medias['total'] / $limit);
    
            return $this->render('front/portfolio.html.twig', [
                'albums' => $albums,
                'album' => $album,
                'medias' => $medias['medias'],
                'page' => $page,
                'total' => $total
            ]);
        } else {
            return $this->render('front/portfolio.html.twig', [
                'albums' => $albums,
                'album' => $album,
                'medias' => $medias
            ]);
        }

    }

    #[Route("/about", name: "about")]
    public function about()
    {
        return $this->render('front/about.html.twig');
    }

    #[Route('/test')]
    public function test(): Response
    {
        if (extension_loaded('newrelic')) {
            newrelic_name_transaction('/test');
            newrelic_add_custom_parameter('test', 1);
        }
        return new Response('OK');
    }
}