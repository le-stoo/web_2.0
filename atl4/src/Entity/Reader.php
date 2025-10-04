<?php

namespace App\Entity;

use App\Repository\ReaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReaderRepository::class)]
class Reader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\ManyToMany(targetEntity: Book::class, inversedBy: 'readers')]
    private Collection $borrowedBooks;

    public function __construct()
    {
        $this->borrowedBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBorrowedBooks(): Collection
    {
        return $this->borrowedBooks;
    }

    public function addBorrowedBook(Book $book): static
    {
        if (!$this->borrowedBooks->contains($book)) {
            $this->borrowedBooks->add($book);
            $book->addReader($this);
        }

        return $this;
    }

    public function removeBorrowedBook(Book $book): static
    {
        if ($this->borrowedBooks->removeElement($book)) {
            $book->removeReader($this);
        }

        return $this;
    }
}