<?php

namespace App\Controller\Front\Action;

use App\Controller\Front\FrontController;
use App\Entity\Core\Website;
use App\Entity\Module\Forum\Comment;
use App\Entity\Module\Forum\Forum;
use App\Form\Manager\Front\ForumManager;
use App\Form\Type\Module\Forum\FrontCommentType;
use App\Form\Type\Module\Forum\FrontForumType;
use App\Repository\Module\Forum\CommentRepository;
use App\Repository\Module\Forum\ForumRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ForumController.
 *
 * Front Forum renders
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class ForumController extends FrontController
{
    private array $arguments = [];

    /**
     * View.
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    #[Route('/front/forum/view/{website}/{filter}', name: 'front_forum_view', options: ['isMainRequest' => false], methods: 'GET|POST', schemes: '%protocol%')]
    public function view(
        Request $request,
        ForumRepository $forumRepository,
        CommentRepository $commentRepository,
        ForumManager $forumManager,
        Website $website,
        mixed $filter = null)
    {
        if (!$filter) {
            return new Response();
        }

        /** @var Forum $forum */
        $forum = $forumRepository->findOneByFilter($website, $request->getLocale(), $filter);

        if (!$forum) {
            return new Response();
        }

        $this->getArguments($request, $forum, $commentRepository, $website);
        $this->getForm($request, $forumManager, $forumRepository, $commentRepository, $filter);

        if ($this->arguments['response'] instanceof JsonResponse) {
            return $this->arguments['response'];
        }

        return $this->render('front/'.$this->arguments['websiteTemplate'].'/actions/forum/view.html.twig', $this->arguments);
    }

    /**
     * Comment render view.
     */
    #[Route('/front/forum/comment/{comment}', name: 'front_forum_comment', options: ['isMainRequest' => false, 'expose' => true], methods: 'GET|POST', schemes: '%protocol%')]
    public function comment(Request $request, Comment $comment): JsonResponse
    {
        $success = false;
        $forum = $comment->getForum();
        $website = $comment->getForum()->getWebsite();
        $template = $website->getConfiguration()->getTemplate();
        $newComment = new Comment();
        $newComment->setPosition($comment->getComments()->count() + 1);

        $form = $this->createForm(FrontCommentType::class, $newComment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment = $form->getData();
            $newComment->setParent($comment);
            $newComment->setActive(!$forum->isModeration());
            $newComment->setForum($forum);
            $newComment->setLocale($request->getLocale());
            $this->coreLocator->em()->persist($newComment);
            $this->coreLocator->em()->flush();
            $this->coreLocator->em()->refresh($comment);
            $success = true;
        }

        return new JsonResponse([
            'success' => $success,
            'html' => $this->renderView('front/'.$template.'/actions/forum/comment.html.twig', [
                'forum' => $forum,
                'website' => $website,
                'websiteTemplate' => $template,
                'submit' => $form->isSubmitted(),
                'comment' => $comment,
                'form' => $form->createView(),
            ]),
        ]);
    }

    /**
     * Get arguments.
     */
    private function getArguments(Request $request, Forum $forum, CommentRepository $commentRepository, Website $website)
    {
        $configuration = $website->getConfiguration();
        $this->arguments = [
            'configuration' => $configuration,
            'websiteTemplate' => $configuration->getTemplate(),
            'website' => $website,
            'comment' => $request->get('comment'),
            'forum' => $forum,
            'comments' => $commentRepository->findByForum($forum),
        ];
    }

    /**
     * Get Form.
     *
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    private function getForm(
        Request $request,
        ForumManager $forumManager,
        ForumRepository $forumRepository,
        CommentRepository $commentRepository,
        mixed $filter)
    {
        /** @var Forum $forum */
        $forum = $this->arguments['forum'];
        $website = $this->arguments['website'];
        $form = $this->createForm(FrontForumType::class, new Comment(), ['form_data' => $this->arguments['forum']]);
        $form->handleRequest($request);

        $this->arguments['response'] = false;
        $this->arguments['form'] = $form->createView();

        if ($form->isSubmitted()) {
            $success = $form->isValid() ? $forumManager->execute($form, $forum, $form->getData()) : false;
            $this->arguments['forum'] = $forumRepository->findOneByFilter($website, $request->getLocale(), $filter);
            $this->arguments['comments'] = $commentRepository->findByForum($forum);

            if ($success) {
                $session = new Session();
                $message = $forum->isModeration() ? $this->coreLocator->translator()->trans('Merci pour votre commentaire. Avant publication, il doit être validé par le modérateur.')
                    : $this->coreLocator->translator()->trans('Merci pour votre commentaire.');
                $session->getFlashBag()->add('success_form', $message);
            }

            $this->arguments['response'] = new JsonResponse([
                'success' => $success,
                'html' => $this->renderView('front/'.$this->arguments['websiteTemplate'].'/actions/forum/view.html.twig', $this->arguments
                )]);
        }
    }
}
