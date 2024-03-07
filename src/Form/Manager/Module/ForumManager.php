<?php

namespace App\Form\Manager\Module;

use App\Entity\Core\Website;
use App\Entity\Module\Forum\Forum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * ForumManager.
 *
 * Manage admin Forum form
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[Autoconfigure(tags: [
    ['name' => ForumManager::class, 'key' => 'module_forum_form_manager'],
])]
class ForumManager
{
    /**
     * ForumManager constructor.
     */
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @prePersist
     *
     * @throws \Exception
     */
    public function prePersist(Forum $forum, Website $website): void
    {
        $securityKey = str_replace(['/', '.'], '', crypt(random_bytes(30), 'rl'));
        $forum->setSecurityKey($securityKey);
        $this->entityManager->persist($forum);
    }
}