<?php

namespace Padam87\FormFilterBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

class Filters
{
    public function apply(QueryBuilder $qb, FormInterface $filters)
    {
        foreach ($filters->all() as $name => $filter) {
            $ignoreNull = $filter->getConfig()->getOption('filter_ignore_null');

            if ($ignoreNull && $filter->isEmpty()) {
                continue;
            }

            $callback = $filter->getConfig()->getOption('filter');
            $expr = $filter->getConfig()->getOption('filter_expr');
            $alias = $filter->getConfig()->getOption('filter_alias');
            $field = $filter->getConfig()->getOption('filter_field');

            if (null === $alias) {
                $alias = $qb->getRootAliases()[0];
            }

            if (null === $field) {
                $field = $name;
            }

            if ($callback === false) {
                continue;
            }

            if ($filter->getData() === null && !$ignoreNull) {
                $expr = 'isNull';
            }

            if ($callback === true) {
                switch ($expr) {
                    case 'like':
                        $value = '%' . $filter->getData() . '%';
                        break;
                    default:
                        $value = $filter->getData();
                }

                $param = str_replace('.', '__', $alias) . '_' . $field;

                $qb
                    ->andWhere($qb->expr()->$expr($alias . '.' . $field, ':' . $param))
                    ->setParameter($param, $value)
                ;
            }

            if (is_callable($callback)) {
                $callback($qb, $alias, $filter->getData(), $field);
            }
        }
    }
}
