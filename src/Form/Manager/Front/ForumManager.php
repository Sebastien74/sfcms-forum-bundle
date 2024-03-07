<?php

namespace App\Form\Manager\Front;

use App\Entity\Module\Forum\Comment;
use App\Entity\Module\Forum\Forum;
use App\Service\Content\RecaptchaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * ForumManager.
 *
 * Manage front Forum Action
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class ForumManager
{
    private ?Request $request;

    /**
     * ForumManager constructor.
     */
    public function __construct(
        private readonly RecaptchaService $recaptcha,
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack)
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    /**
     * Execute request.
     *
     * @throws \Exception
     */
    public function execute(FormInterface $form, Forum $forum, Comment $comment): bool
    {
        $website = $forum->getWebsite();

        if (!$this->recaptcha->execute($website, $forum, $form)) {
            return false;
        }

        $position = $forum->getComments()->count() + 1;
        $comment->setPosition($position);
        $comment->setLocale($this->request->getLocale());
        $forum->addComment($comment);

        if (!$forum->isModeration()) {
            $comment->setActive(true);
        }

        $this->entityManager->persist($forum);
        $this->entityManager->flush();
        $this->entityManager->refresh($forum);

        return true;
    }
}
