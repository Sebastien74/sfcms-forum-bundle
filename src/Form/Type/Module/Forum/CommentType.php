<?php

namespace App\Form\Type\Module\Forum;

use App\Entity\Module\Forum\Comment;
use App\Form\Widget as WidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * CommentType.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class CommentType extends AbstractType
{
    /**
     * CommentType constructor.
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Comment $data */
        $data = $builder->getData();
        $isNew = !$data->getId();

        $builder->add('authorName', Type\TextType::class, [
            'label' => $this->translator->trans('Auteur', [], 'admin'),
            'attr' => [
                'placeholder' => $this->translator->trans('Saisissez un auteur', [], 'admin'),
            ],
            'constraints' => [new Assert\NotBlank()],
        ]);

        $builder->add('message', Type\TextareaType::class, [
            'label' => $this->translator->trans('Message', [], 'admin'),
            'attr' => [
                'placeholder' => $this->translator->trans('Saisissez un message', [], 'admin'),
            ],
            'constraints' => [new Assert\NotBlank()],
        ]);

        $builder->add('active', Type\CheckboxType::class, [
            'label' => $this->translator->trans('Valider le commentaire ?', [], 'admin'),
            'attr' => ['group' => 'col-md-3'],
            'data' => $isNew ? true : $data->isActive(),
        ]);

        $save = new WidgetType\SubmitType($this->translator);
        $save->add($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'website' => null,
            'translation_domain' => 'admin',
        ]);
    }
}
