<?php

namespace App\Entity\Module\Forum;

use App\Entity\BaseEntity;
use App\Repository\Module\Forum\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_forum_comment')]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment extends BaseEntity
{
    /**
     * Configurations.
     */
    protected static string $masterField = 'forum';
    protected static array $interface = [
        'name' => 'forumcomment',
    ];

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $locale = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $authorName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = false;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Comment::class)]
    #[ORM\OrderBy(['position' => 'DESC'])]
    private ArrayCollection|PersistentCollection $comments;

    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: Like::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $likes;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private ?Comment $parent = null;

    #[ORM\ManyToOne(targetEntity: Forum::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Forum $forum = null;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(?string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getParent() === $this) {
                $comment->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setComment($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getComment() === $this) {
                $like->setComment(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): self
    {
        $this->forum = $forum;

        return $this;
    }
}
