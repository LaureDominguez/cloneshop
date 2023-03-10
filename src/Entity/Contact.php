<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Constraints\NotBlank()]
    #[Constraints\Length(min: 2, max: 50)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    #[Constraints\NotBlank()]
    #[Constraints\Length(min: 2, max: 50)]
    private ?string $lastName = null;

    #[ORM\Column(length: 100)]
    #[Constraints\Email()]
    #[Constraints\Length(min: 5, max: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 150)]
    #[Constraints\NotBlank()]
    #[Constraints\Length(min: 2, max: 150)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Constraints\NotBlank()]
    private ?string $message = null;

    #[ORM\Column]
    #[Constraints\NotBlank()]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
