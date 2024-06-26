<?php

namespace App\Entity;

use App\Repository\BuildMessageRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BuildMessageRepository::class)]
class BuildMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotNull(message: 'Le contenu du message est obligatoire.')]
    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'buildMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Build $build = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $postedAt = null;

    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function getContent(): ?string
    {
        return $this->content;
    }

    final public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    final public function getBuild(): ?Build
    {
        return $this->build;
    }

    final public function setBuild(?Build $build): self
    {
        $this->build = $build;

        return $this;
    }

    final public function getAuthor(): ?User
    {
        return $this->author;
    }

    final public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    final public function getPostedAt(): ?DateTime
    {
        return $this->postedAt;
    }

    final public function setPostedAt(DateTime $postedAt): self
    {
        $this->postedAt = $postedAt;

        return $this;
    }
}
