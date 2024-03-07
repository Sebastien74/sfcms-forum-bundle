<?php

namespace App\Controller\Admin\Module\Forum;

use App\Controller\Admin\AdminController;
use App\Entity\Module\Forum\Forum;
use App\Form\Interface\ModuleFormManagerInterface;
use App\Form\Type\Module\Forum\ForumType;
use App\Service\Interface\AdminLocatorInterface;
use App\Service\Interface\CoreLocatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * ForumController.
 *
 * Forum Action management
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[IsGranted('ROLE_FORUM')]
#[Route('/admin-%security_token%/{website}/forums', schemes: '%protocol%')]
class ForumController extends AdminController
{
    protected ?string $class = Forum::class;
    protected ?string $formType = ForumType::class;

    /**
     * ForumController constructor.
     */
    public function __construct(
        protected ModuleFormManagerInterface $moduleFormInterface,
        protected CoreLocatorInterface $baseLocator,
        protected AdminLocatorInterface $adminLocator
    ) {
        $this->formManager = $moduleFormInterface->forum();
        parent::__construct($baseLocator, $adminLocator);
    }

    /**
     * Index Forum.
     *
     * {@inheritdoc}
     */
    #[Route('/index', name: 'admin_forum_index', methods: 'GET|POST')]
    public function index(Request $request, PaginatorInterface $paginator)
    {
        return parent::index($request, $paginator);
    }

    /**
     * New Forum.
     *
     * {@inheritdoc}
     */
    #[Route('/new', name: 'admin_forum_new', methods: 'GET|POST')]
    public function new(Request $request)
    {
        return parent::new($request);
    }

    /**
     * Edit Forum.
     *
     * {@inheritdoc}
     */
    #[Route('/edit/{forum}', name: 'admin_forum_edit', methods: 'GET|POST')]
    public function edit(Request $request)
    {
        return parent::edit($request);
    }

    /**
     * Show Forum.
     *
     * {@inheritdoc}
     */
    #[Route('/show/{forum}', name: 'admin_forum_show', methods: 'GET')]
    public function show(Request $request)
    {
        return parent::show($request);
    }

    /**
     * Position Forum.
     *
     * {@inheritdoc}
     */
    #[Route('/position/{forum}', name: 'admin_forum_position', methods: 'GET|POST')]
    public function position(Request $request)
    {
        return parent::position($request);
    }

    /**
     * Delete Forum.
     *
     * {@inheritdoc}
     */
    #[Route('/delete/{forum}', name: 'admin_forum_delete', methods: 'DELETE')]
    public function delete(Request $request)
    {
        return parent::delete($request);
    }
}