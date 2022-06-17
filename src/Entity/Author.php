<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[UniqueEntity("name", message: "An author with this name already exists")]
#[ApiResource(
    itemOperations: [
        'delete',
        'get' => [
            'normalization_context' => ['groups' => ['read:author:item']]
        ],
        'put'
    ],
    denormalizationContext: ['groups' => ['write:author:item']],
    normalizationContext: ['groups' => ['read:author:collection']],
    paginationItemsPerPage: 5
)]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:author:collection', 'read:author:item'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(['read:post:item', 'read:author:collection', 'read:author:item', 'write:author:item', 'write:post:item']),
        Assert\Length(max: 128, maxMessage: "The title of the author must be less than 128 characters"),
        Assert\NotBlank(message: "The name cannot be blank")
    ]
    private $name;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Post::class, orphanRemoval: true)]
    #[Groups(['read:author:collection', 'read:author:item'])]
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }
}
