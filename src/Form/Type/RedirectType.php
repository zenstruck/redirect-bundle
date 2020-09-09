<?php

namespace Zenstruck\RedirectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectType extends AbstractType
{
    private $class;

    /**
     * @param string $class The Redirect class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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

    public function getBlockPrefix()
    {
        return 'zenstruck_redirect';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $class = $this->class;

        $resolver->setDefaults([
            'data_class' => $this->class,
            'disable_source' => false,
            'empty_data' => function(FormInterface $form) use ($class) {
                return new $class(
                    $form->get('source')->getData(),
                    $form->get('destination')->getData()
                );
            },
        ]);
    }
}
