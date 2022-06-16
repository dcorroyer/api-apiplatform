<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[UniqueEntity("title", message: "A post with this title already exists")]
#[ApiResource(
    itemOperations: [
        'delete',
        'get' => [
            'normalization_context' => ['groups' => ['read:post:item']]
        ],
        'put'
    ],
    denormalizationContext: ['groups' => ['write:post:item']],
    normalizationContext: ['groups' => ['read:post:collection']],
    paginationItemsPerPage: 5
)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:post:collection', 'read:post:item', 'read:user:item'])]
    private $id;

    #[ORM\Column(type: 'string', length: 128)]
    #[
        Groups(['read:post:collection', 'read:post:item', 'write:post:item', 'read:user:item']),
        Assert\Length(max: 128, maxMessage: "The title of the post must be less than 128 characters")
    ]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:post:item', 'write:post:item'])]
    private $content;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[
        Groups(['read:post:collection', 'read:post:item', 'write:post:item']),
        Assert\Type("\DateTimeInterface")
    ]
    private $publishedAt;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:post:collection', 'read:post:item', 'write:post:item'])]
    private $status;

    #[ORM\ManyToOne(targetEntity: Author::class, cascade: ['persist'], inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:post:collection', 'read:post:item', 'write:post:item'])]
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }
}
