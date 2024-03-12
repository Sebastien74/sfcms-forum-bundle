<?php

namespace App\Form\Type\Module\Forum;

use App\Entity\Module\Forum\Forum;
use App\Form\Widget as WidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * ForumType.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class ForumType extends AbstractType
{
    private TranslatorInterface $translator;
    private bool $isInternalUser;

    /**
     * ContactType constructor.
     */
    public function __construct(
        private readonly CoreLocatorInterface $coreLocator,
        private readonly TokenStorageInterface $tokenStorage
    ) {
        $this->translator = $this->coreLocator->translator();
        $user = !empty($this->tokenStorage->getToken()) ? $this->tokenStorage->getToken()->getUser() : null;
        $this->isInternalUser = $user && in_array('ROLE_INTERNAL', $user->getRoles());
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = !$builder->getData()->getId();
        $fields = $this->getFields();

        $adminName = new WidgetType\AdminNameType($this->translator);
        $adminName->add($builder, ['slug-internal' => $this->isInternalUser]);

        if (!$isNew && $this->isInternalUser) {
            $builder->add('fields', Type\ChoiceType::class, [
                'label' => $this->translator->trans('Champs', [], 'admin'),
                'expanded' => false,
                'multiple' => true,
                'display' => 'search',
                'attr' => [
                    'data-config' => true,
                    'group' => 'col-md-4',
                    'data-placeholder' => $this->translator->trans('Sélectionnez', [], 'admin'),
                ],
                'choices' => $fields,
            ]);

            $builder->add('requireFields', Type\ChoiceType::class, [
                'label' => $this->translator->trans('Champs requis', [], 'admin'),
                'expanded' => false,
                'multiple' => true,
                'display' => 'search',
                'attr' => [
                    'data-config' => true,
                    'group' => 'col-md-4',
                    'data-placeholder' => $this->translator->trans('Sélectionnez', [], 'admin'),
                ],
                'choices' => $fields,
            ]);

            $builder->add('formatDate', WidgetType\FormatDateType::class, [
                'attr' => ['group' => 'col-md-4', 'data-config' => true],
            ]);

            $builder->add('widgets', Type\ChoiceType::class, [
                'label' => $this->translator->trans('Widgets', [], 'admin'),
                'required' => false,
                'expanded' => false,
                'multiple' => true,
                'display' => 'search',
                'attr' => [
                    'data-config' => true,
                    'data-placeholder' => $this->translator->trans('Sélectionnez', [], 'admin'),
                ],
                'choices' => [
                    $this->translator->trans('Commentaires', [], 'admin') => 'comments',
                    $this->translator->trans('Bouton de like', [], 'admin') => 'likes',
                    $this->translator->trans('Bouton de paratage', [], 'admin') => 'shares',
                ],
            ]);

            $builder->add('hideDate', Type\CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans('Cacher la date ?', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'data-config' => true],
            ]);

            $builder->add('moderation', Type\CheckboxType::class, [
                'label' => $this->translator->trans('Activer la modération ?', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'data-config' => true],
            ]);

            $builder->add('login', Type\CheckboxType::class, [
                'label' => $this->translator->trans('Connexion requise ?', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'data-config' => true],
            ]);

            $builder->add('recaptcha', Type\CheckboxType::class, [
                'label' => $this->translator->trans('Activer le recaptcha ?', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'data-config' => true],
            ]);
        }

        $save = new WidgetType\SubmitType($this->translator);
        $save->add($builder);
    }

    /**
     * Get fields.
     */
    private function getFields(): array
    {
        return [
            $this->translator->trans('Nom et prénom', [], 'admin') => 'authorName',
            $this->translator->trans('Message', [], 'admin') => 'message',
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Forum::class,
            'website' => null,
            'translation_domain' => 'admin',
        ]);
    }
}
