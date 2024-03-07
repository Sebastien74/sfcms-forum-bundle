<?php

namespace App\Controller\Admin\Module\Forum;

use App\Controller\Admin\AdminController;
use App\Entity\Module\Forum\Comment;
use App\Form\Type\Module\Forum\CommentType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * CommentController.
 *
 * Faq Comment Action management
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[IsGranted('ROLE_FORUM')]
#[Route('/admin-%security_token%/{website}/forums/comments', schemes: '%protocol%')]
class CommentController extends AdminController
{
    protected ?string $class = Comment::class;
    protected ?string $formType = CommentType::class;

    /**
     * Index Comment.
     *
     * {@inheritdoc}
     */
    #[Route('/{forum}/index', name: 'admin_forumcomment_index', methods: 'GET|POST')]
    public function index(Request $request, PaginatorInterface $paginator)
    {
        return parent::index($request, $paginator);
    }

    /**
     * New Comment.
     *
     * {@inheritdoc}
     */
    #[Route('/{forum}/new', name: 'admin_forumcomment_new', methods: 'GET|POST')]
    public function new(Request $request)
    {
        return parent::new($request);
    }

    /**
     * Edit Comment.
     *
     * {@inheritdoc}
     */
    #[Route('/{forum}/edit/{forumcomment}', name: 'admin_forumcomment_edit', methods: 'GET|POST')]
    public function edit(Request $request)
    {
        return parent::edit($request);
    }

    /**
     * Show Comment.
     *
     * {@inheritdoc}
     */
    #[Route('/{forum}/show/{forumcomment}', name: 'admin_forumcomment_show', methods: 'GET')]
    public function show(Request $request)
    {
        return parent::show($request);
    }

    /**
     * Position Comment.
     *
     * {@inheritdoc}
     */
    #[Route('/position/{forumcomment}', name: 'admin_forumcomment_position', methods: 'GET|POST')]
    public function position(Request $request)
    {
        return parent::position($request);
    }

    /**
     * Delete Comment.
     *
     * {@inheritdoc}
     */
    #[Route('/delete/{forumcomment}', name: 'admin_forumcomment_delete', methods: 'DELETE')]
    public function delete(Request $request)
    {
        return parent::delete($request);
    }
}
