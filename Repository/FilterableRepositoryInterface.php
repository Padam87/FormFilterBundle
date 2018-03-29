<?php

namespace Padam87\FormFilterBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

interface FilterableRepositoryInterface
{
    public function getRootAlias(): string;

    public function createFilteredQueryBuilder(FormInterface $filters = null): QueryBuilder;
}
