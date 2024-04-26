<?php
// src/Controller/FilmController.php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use App\Entity\Film;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Categorie;

class FilmController extends AbstractController
{
    private $filmRepository;
    private $entityManager;
    private $serializer;

    public function __construct(FilmRepository $filmRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->filmRepository = $filmRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/films", name="film_list", methods={"GET"})
     */
    public function listFilms(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $films = $this->filmRepository->findAllPaginated($page, $limit);

        $data = $this->serializer->serialize($films, 'json', ['groups' => ['film:read']]);

        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/films/{id}", name="film_show", methods={"GET"})
     */
    public function showFilm(int $id): Response
    {
        $film = $this->filmRepository->find($id);

        if (!$film) {
            return new Response('Film not found', Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($film, 'json', ['groups' => ['film:read']]);

        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/films", name="film_create", methods={"POST"})
     */
    public function createFilm(Request $request): Response
    {
        $filmData = json_decode($request->getContent(), true);

        $film = new Film();
        $film->setName($filmData['name']);
        $film->setDescription($filmData['description']);
        $film->setReleaseDate(new \DateTime($filmData['release_date']));
        $film->setNote($filmData['note']);

        // Handle categories
        $categories = [];
        foreach ($filmData['categories'] as $categoryName) {
            $category = new Categorie();
            $category->setName($categoryName);
            $categories[] = $category;
        }

        $this->entityManager->persist($film);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($film, 'json', ['groups' => ['film:read']]);

        return new Response($data, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/films/{id}", name="film_update", methods={"PUT"})
     */
    public function updateFilm(int $id, Request $request): Response
    {
        $film = $this->filmRepository->find($id);

        if (!$film) {
            return new Response('Film not found', Response::HTTP_NOT_FOUND);
        }

        $filmData = json_decode($request->getContent(), true);

        // Update film properties if provided in the request
        if (isset($filmData['name'])) {
            $film->setName($filmData['name']);
        }
        if (isset($filmData['description'])) {
            $film->setDescription($filmData['description']);
        }
        if (isset($filmData['release_date'])) {
            $film->setReleaseDate(new \DateTime($filmData['release_date']));
        }
        if (isset($filmData['note'])) {
            $film->setNote($filmData['note']);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($film, 'json', ['groups' => ['film:read']]);

        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    
    /**
     * @Route("/categories", name="categorie_create", methods={"POST"})
     */
    public function createCategorie(Request $request): Response
    {
        $categorieData = json_decode($request->getContent(), true);

        $categorie = new Categorie();
        $categorie->setName($categorieData['name']);

        $this->entityManager->persist($categorie);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($categorie, 'json', ['groups' => ['categorie:read']]);

        return new Response($data, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }
     /**
     * @Route("/films/search/name/{name}", name="film_search_by_name", methods={"GET"})
     */
    public function searchFilmsByName(string $name): Response
    {
        $films = $this->filmRepository->findByName($name);

        $data = $this->serializer->serialize($films, 'json', ['groups' => ['film:read']]);

        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/films/search/description/{description}", name="film_search_by_description", methods={"GET"})
     */
    public function searchFilmsByDescription(string $description): Response
    {
        $films = $this->filmRepository->findByDescription($description);

        $data = $this->serializer->serialize($films, 'json', ['groups' => ['film:read']]);

        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}    