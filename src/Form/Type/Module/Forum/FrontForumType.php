<?php

namespace App\Form\Type\Module\Forum;

use App\Entity\Module\Forum\Comment;
use App\Entity\Module\Forum\Forum;
use App\Form\Widget as WidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * FrontForumType.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class FrontForumType extends AbstractType
{
    private static array $fields = [
        'authorName', 'message',
    ];

    /**
     * FrontForumType constructor.
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Forum $forum */
        $forum = $options['form_data'];
        $fields = $forum->getFields();
        $requiredFields = $forum->getRequireFields();

        $recaptcha = new WidgetType\RecaptchaType($this->translator);
        $recaptcha->add($builder, $forum);

        foreach (self::$fields as $field) {
            $configuration = $this->getConfiguration($field);
            if ($configuration && in_array($field, $fields)) {
                $required = in_array($field, $requiredFields);
                $builder->add($field, $configuration['type'], [
                    'label' => $configuration['label'],
                    'required' => $required,
                    'attr' => [
                        'placeholder' => $configuration['placeholder'],
                        'autocomplete' => 'off',
                    ],
                    'constraints' => $required ? [new Assert\NotBlank()] : [],
                ]);
            }
        }
    }

    /**
     * Get field configuration.
     *
     * @return array|null
     */
    private function getConfiguration(string $name)
    {
        $configurations = [
            'authorName' => [
                'type' => Type\TextType::class,
                'label' => $this->translator->trans('Nom & prénom', [], 'front_form'),
                'placeholder' => $this->translator->trans('Saisissez votre nom & prénom', [], 'front_form'),
            ],
            'message' => [
                'type' => Type\TextareaType::class,
                'label' => $this->translator->trans('Message', [], 'front_form'),
                'placeholder' => $this->translator->trans('Saisissez un message', [], 'front_form'),
            ],
        ];

        return !empty($configurations[$name]) ? $configurations[$name] : null;
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
        return 'front_forum';
    }
}
