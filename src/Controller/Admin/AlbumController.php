<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Media;
use App\Form\AlbumType;
use App\Form\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AlbumRepository;

class AlbumController extends AbstractController
{

    public function __construct(private AlbumRepository $albumRepository, private EntityManagerInterface $entityManager){        
    }

    #[Route('/admin/album', name: 'admin_album_index')]
    public function index()
    {
        $albums = $this->albumRepository->findAll();

        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }

    #[Route('/admin/album/add', name: 'admin_album_add')]
    public function add(Request $request)
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($album);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/add.html.twig', ['form' => $form->createView()]);
    }
    
    #[Route('/admin/album/update/{id}', name: 'admin_album_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(Request $request, int $id)
    {
        $album = $this->albumRepository->find($id);
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/album/delete/{id}", name="admin_album_delete")
     */
    #[Route('/admin/album/delete/{id}', name: 'admin_album_delete')]
    public function delete(int $id)
    {
        $media = $this->albumRepository->find($id);
        $this->entityManager->remove($media);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_album_index');
    }
}