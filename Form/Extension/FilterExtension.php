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
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}