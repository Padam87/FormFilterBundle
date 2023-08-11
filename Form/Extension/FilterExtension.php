<?php

namespace Padam87\FormFilterBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('filter', true);
        $resolver->setAllowedTypes('filter', ['bool', 'callable']);

        $resolver->setDefault('filter_expr', 'eq');
        $resolver->setAllowedTypes('filter_expr', ['string']);

        $resolver->setDefault('filter_alias', null);
        $resolver->setAllowedTypes('filter_alias', ['null', 'string']);

        $resolver->setDefault('filter_field', null);
        $resolver->setAllowedTypes('filter_field', ['null', 'string']);

        $resolver->setDefault('filter_ignore_null', true);
        $resolver->setAllowedTypes('filter_ignore_null', ['bool']);

        $resolver->setDefault('filter_compound', false);
        $resolver->setAllowedTypes('filter_compound', ['bool']);
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
