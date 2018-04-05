<?php

namespace Padam87\FormFilterBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

class Filters
{
    public function apply(QueryBuilder $qb, FormInterface $filters)
    {
        foreach ($filters->all() as $field => $filter) {
            if ($filter->isEmpty()) {
                continue;
            }

            $callback = $filter->getConfig()->getOption('filter');
            $expr = $filter->getConfig()->getOption('filter_expr');

            if ($callback === false) {
                continue;
            }

            if ($callback === true) {
                switch ($expr) {
                    case 'like':
                        $value = '%' . $filter->getData() . '%';
                        break;
                    default:
                        $value = $filter->getData();
                }

                $qb
                    ->andWhere($qb->expr()->$expr("{$qb->getRootAliases()[0]}.$field", ":$field"))
                    ->setParameter($field, $value)
                ;
            }

            if (is_callable($callback)) {
                $callback($qb, $qb->getRootAlias(), $filter->getData());
            }
        }
    }
}
