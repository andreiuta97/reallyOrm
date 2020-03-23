<?php

namespace ReallyOrm\SearchResult;


class SearchResult
{
    /**
     * The number of entities retrieved does not have to match
     * the total number of existing entities that match the Criteria.
     *
     * @var array AbstractEntity[]
     */
    private $items = [];

    /**
     * Represents the total number of entities that match the filter given in the Criteria.
     * @var int
     */
    private $totalCount;

    /**
     * @param array $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->totalCount;
    }
}