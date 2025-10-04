<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AuthorRepository $authorRepository): Response
    {
        if ($request->isMethod('POST')) {
            $author = $authorRepository->find($request->request->get('author'));
            if (!$author) {
                throw $this->createNotFoundException('Author not found');
            }

            $book = new Book();
            $book->setTitle($request->request->get('title'));
            $book->setCategory($request->request->get('category'));
            $book->setPrice((float)$request->request->get('price'));
            $book->setPublicationDate(new \DateTimeImmutable($request->request->get('publication_date')));
            $book->setRef($request->request->get('ref'));
            $book->setAuthor($author);

            $author->addBook($book);

            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/new.html.twig', [
            'authors' => $authorRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager, AuthorRepository $authorRepository): Response
    {
        if ($request->isMethod('POST')) {
            $newAuthor = $authorRepository->find($request->request->get('author'));
            if (!$newAuthor) {
                throw $this->createNotFoundException('Author not found');
            }

            $oldAuthor = $book->getAuthor();
            if ($oldAuthor !== $newAuthor) {
                $oldAuthor->removeBook($book);
                $newAuthor->addBook($book);
            }

            $book->setTitle($request->request->get('title'));
            $book->setCategory($request->request->get('category'));
            $book->setPrice((float)$request->request->get('price'));
            $book->setPublicationDate(new \DateTimeImmutable($request->request->get('publication_date')));
            $book->setRef($request->request->get('ref'));
            $book->setAuthor($newAuthor);

            $entityManager->flush();

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'authors' => $authorRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $author = $book->getAuthor();
            $author->removeBook($book);
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_index');
    }
}