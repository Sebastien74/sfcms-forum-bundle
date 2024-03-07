<?php

namespace App\Entity\Module\Forum;

use App\Entity\BaseEntity;
use App\Repository\Module\Forum\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Like.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_forum_comment_like')]
#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Like extends BaseEntity
{
    /**
     * Configurations.
     */
    protected static string $masterField = 'comment';
    protected static array $interface = [
        'name' => 'forumcommentlike',
    ];

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comment $comment = null;

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
