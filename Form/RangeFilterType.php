<?php

namespace Padam87\FormFilterBundle\Form;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RangeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $options['from_field_name'],
                $options['from_field_type'],
                $options['from_field_options']
            )
            ->add(
                $options['to_field_name'],
                $options['to_field_type'],
                $options['to_field_options']
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(
                [
                    'from_field_name' => 'from',
                    'from_field_type' => TextType::class,
                    'from_field_options' => [],
                    'from_field_expr' => 'gte',
                    'to_field_name' => 'to',
                    'to_field_type' => TextType::class,
                    'to_field_options' => [],
                    'to_field_expr' => 'lte',
                    'filter' => function (Options $options) {
                        return function(QueryBuilder $qb, string $alias, array $value, string $field) use ($options): void {
                            if ($value['from'] != null) {
                                $parameter = str_replace('.', '_', $field) . '_from';

                                $qb
                                    ->andWhere($qb->expr()->{$options['from_field_expr']}($alias . '.' . $field, ':' . $parameter))
                                    ->setParameter($parameter, $value['from'])
                                ;
                            }
                            
                            if ($value['to'] != null) {
                                $parameter = str_replace('.', '_', $field) . '_to';

                                $qb
                                    ->andWhere($qb->expr()->{$options['to_field_expr']}($alias . '.' . $field, ':' . $parameter))
                                    ->setParameter($parameter, $value['to'])
                                ;
                            }
                        };
                    }
                ]
            )
        ;
    }
}
