<?php

namespace Zenstruck\RedirectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source', null, array(
                'label' => 'form.source',
                'translation_domain' => 'ZenstruckRedirectBundle',
                'disabled' => $options['disable_source'],
                'read_only' => $options['disable_source'],
            ))

            ->add('destination', null, array(
                'label' => 'form.destination',
                'translation_domain' => 'ZenstruckRedirectBundle'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zenstruck_redirect';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $class = $this->class;

        $resolver->setDefaults(array(
            'data_class'     => $this->class,
            'intention'      => 'redirect',
            'disable_source' => false,
            'empty_data'     => function (FormInterface $form) use ($class) {
                return new $class(
                    $form->get('source')->getData(),
                    $form->get('destination')->getData()
                );
            }
        ));
    }

    /**
     * {@inheritdoc}
     *
     * BC for SF < 2.7
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }
}
