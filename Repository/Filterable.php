<?php

namespace Padam87\FormFilterBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

trait Filterable
{
    abstract public function getRootAlias(): string;

    public function createFilteredQueryBuilder(FormInterface $filters = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getRootAlias());

        if ($filters) {
            $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    protected function applyFilters(QueryBuilder $qb, FormInterface $filters)
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
                    ->andWhere($qb->expr()->$expr("{$this->getRootAlias()}.$field", ":$field"))
                    ->setParameter($field, $value)
                ;
            }

            if (is_callable($callback)) {
                $callback($qb, $this->getRootAlias(), $filter->getData());
            }
        }
    }
}