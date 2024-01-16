<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RedirectType extends AbstractType
{
    /**
     * @param string $class The Redirect class name
     */
    public function __construct(private string $class)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source', null, [
                'label' => 'form.source',
                'translation_domain' => 'ZenstruckRedirectBundle',
                'disabled' => $options['disable_source'],
            ])

            ->add('destination', null, [
                'label' => 'form.destination',
                'translation_domain' => 'ZenstruckRedirectBundle',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'zenstruck_redirect';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $class = $this->class;

        $resolver->setDefaults([
            'data_class' => $this->class,
            'disable_source' => false,
            'empty_data' => function(FormInterface $form) use ($class) {
                return new $class(
                    $form->get('source')->getData(),
                    $form->get('destination')->getData(),
                );
            },
        ]);
    }
}
