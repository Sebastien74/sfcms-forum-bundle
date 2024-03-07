<?php

namespace App\Form\Type\Module\Forum;

use App\Entity\Module\Forum\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * FrontCommentType.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class FrontCommentType extends AbstractType
{
    /**
     * FrontCommentType constructor.
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', Type\TextType::class, [
            'attr' => [
                'placeholder' => $this->translator->trans('Votre commentaire...', [], 'admin'),
            ],
            'constraints' => [new Assert\NotBlank()],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'form_data' => null,
            'translation_domain' => 'front_form',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'front_forum_comment';
    }
}
