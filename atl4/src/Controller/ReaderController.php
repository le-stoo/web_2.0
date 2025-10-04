<?php

namespace App\Controller;

use App\Entity\Reader;
use App\Entity\Book;
use App\Repository\ReaderRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reader')]
class ReaderController extends AbstractController
{
    #[Route('/', name: 'app_reader_index', methods: ['GET'])]
    public function index(ReaderRepository $readerRepository): Response
    {
        return $this->render('reader/index.html.twig', [
            'readers' => $readerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reader_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $reader = new Reader();
            $reader->setUsername($request->request->get('username'));
            
            $entityManager->persist($reader);
            $entityManager->flush();

            return $this->redirectToRoute('app_reader_index');
        }

        return $this->render('reader/new.html.twig');
    }

    #[Route('/{id}', name: 'app_reader_show', methods: ['GET'])]
    public function show(Reader $reader): Response
    {
        return $this->render('reader/show.html.twig', [
            'reader' => $reader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reader_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reader $reader, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $reader->setUsername($request->request->get('username'));
            $entityManager->flush();

            return $this->redirectToRoute('app_reader_index');
        }

        return $this->render('reader/edit.html.twig', [
            'reader' => $reader,
        ]);
    }

    #[Route('/{id}', name: 'app_reader_delete', methods: ['POST'])]
    public function delete(Request $request, Reader $reader, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reader->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reader);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reader_index');
    }

    #[Route('/{id}/borrow', name: 'app_reader_borrow', methods: ['GET', 'POST'])]
    public function borrow(Request $request, Reader $reader, BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $bookId = $request->request->get('book');
            $book = $bookRepository->find($bookId);
            
            if ($book) {
                $reader->addBorrowedBook($book);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_reader_show', ['id' => $reader->getId()]);
        }

        $availableBooks = $bookRepository->findAll();

        return $this->render('reader/borrow.html.twig', [
            'reader' => $reader,
            'books' => $availableBooks,
        ]);
    }

    #[Route('/{id}/return/{bookId}', name: 'app_reader_return_book', methods: ['POST'])]
    public function returnBook(Reader $reader, int $bookId, BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        $book = $bookRepository->find($bookId);
        
        if ($book) {
            $reader->removeBorrowedBook($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reader_show', ['id' => $reader->getId()]);
    }
}