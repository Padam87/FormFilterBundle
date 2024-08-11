<?php

namespace Padam87\FormFilterBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

class Filters
{
    public function apply(QueryBuilder $qb, FormInterface $filters, ?string $rootAlias = null): void
    {
        $rootAlias = $rootAlias ?? $qb->getRootAliases()[0];

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
                $alias = $rootAlias;
            }

            if (null === $field) {
                $field = $name;
            }

            if ($filter->getConfig()->getOption('filter_compound')) {
                $this->apply($qb, $filter, $alias);

                continue;
            }

            if ($callback === false) {
                continue;
            }

            if ($filter->getData() === null && !$ignoreNull) {
                $expr = 'isNull';
            }

            if ($callback === true) {
                $value = match ($expr) {
                    'like' => '%' . $filter->getData() . '%',
                    default => $filter->getData(),
                };
                $param = str_replace('.', '__', (string) $alias) . '_' . str_replace('.', '_', (string) $field);
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
