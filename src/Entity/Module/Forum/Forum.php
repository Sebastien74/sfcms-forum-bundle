<?php

namespace App\Entity\Module\Forum;

use App\Entity\BaseEntity;
use App\Entity\Core\Website;
use App\Repository\Module\Forum\ForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Forum.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_forum')]
#[ORM\Entity(repositoryClass: ForumRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Forum extends BaseEntity
{
    /**
     * Configurations.
     */
    protected static string $masterField = 'website';
    protected static array $interface = [
        'name' => 'forum',
        'buttons' => [
            'admin_forumcomment_index',
        ],
    ];
    protected static array $labels = [
        'admin_forumcomment_index' => 'Commentaires',
    ];

    #[ORM\Column(type: 'boolean')]
    private bool $moderation = true;

    #[ORM\Column(type: 'boolean')]
    private bool $login = false;

    #[ORM\Column(type: 'boolean')]
    private bool $recaptcha = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $securityKey = null;

    #[ORM\Column(type: 'boolean')]
    private bool $hideDate = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formatDate = 'cccc dd MMMM Y à HH:mm';

    #[ORM\Column(type: 'json', nullable: true)]
    private array $fields = ['authorName', 'message'];

    #[ORM\Column(type: 'json', nullable: true)]
    private array $requireFields = ['authorName', 'message'];

    #[ORM\Column(type: 'json', nullable: true)]
    private array $widgets = ['comments', 'likes', 'shares'];

    #[ORM\OneToMany(mappedBy: 'forum', targetEntity: Comment::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $comments;

    #[ORM\ManyToOne(targetEntity: Website::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Website $website = null;

    /**
     * Forum constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function isModeration(): ?bool
    {
        return $this->moderation;
    }

    public function setModeration(bool $moderation): self
    {
        $this->moderation = $moderation;

        return $this;
    }

    public function isLogin(): ?bool
    {
        return $this->login;
    }

    public function setLogin(bool $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function isRecaptcha(): ?bool
    {
        return $this->recaptcha;
    }

    public function setRecaptcha(bool $recaptcha): self
    {
        $this->recaptcha = $recaptcha;

        return $this;
    }

    public function getSecurityKey(): ?string
    {
        return $this->securityKey;
    }

    public function setSecurityKey(?string $securityKey): self
    {
        $this->securityKey = $securityKey;

        return $this;
    }

    public function isHideDate(): ?bool
    {
        return $this->hideDate;
    }

    public function setHideDate(bool $hideDate): self
    {
        $this->hideDate = $hideDate;

        return $this;
    }

    public function getFormatDate(): ?string
    {
        return $this->formatDate;
    }

    public function setFormatDate(?string $formatDate): self
    {
        $this->formatDate = $formatDate;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getRequireFields(): array
    {
        return $this->requireFields;
    }

    public function setRequireFields(array $requireFields): self
    {
        $this->requireFields = $requireFields;

        return $this;
    }

    public function getWidgets(): array
    {
        return $this->widgets;
    }

    public function setWidgets(array $widgets): self
    {
        $this->widgets = $widgets;

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
            $comment->setForum($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getForum() === $this) {
                $comment->setForum(null);
            }
        }

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }
}
