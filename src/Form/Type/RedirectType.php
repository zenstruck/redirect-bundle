<?php

namespace Zenstruck\RedirectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source', null, array(
                'label' => 'form.source',
                'translation_domain' => 'ZenstruckRedirectBundle',
                'disabled' => $options['disable_source'],
                'read_only' => $options['disable_source'],
            ))
            ->add('destination', null, array('label' => 'form.destination', 'translation_domain' => 'ZenstruckRedirectBundle'))
        ;

        if ($options['status_code']) {
            $builder->add('status_code', 'choice', array(
                'label' => 'form.status_code',
                'translation_domain' => 'ZenstruckRedirectBundle',
                'choices' => array(
                    301 => '301 (Moved Permanently)',
                    302 => '302 (Found)',
                )
            ));
        }
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
        $resolver->setDefaults(array(
            'data_class'     => $this->class,
            'intention'      => 'redirect',
            'status_code'    => false,
            'disable_source' => false,
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
