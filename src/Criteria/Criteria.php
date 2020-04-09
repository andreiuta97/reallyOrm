<?php


namespace ReallyOrm\Criteria;


class Criteria
{
    private $filters = [];
    private $sorts = [];
    private $from = 0;
    private $size = 10;

    /**
     * Criteria constructor.
     * @param array $filters
     * @param array $sorts
     * @param int $from
     * @param int $size
     */
    public function __construct(array $filters = [], array $sorts = [], int $from = 0, int $size = 10)
    {
        $this->filters = $filters;
        $this->sorts = $sorts;
        $this->from = $from;
        $this->size = $size;
    }

    public function filtersToQuery(string $sql): string
    {
        if (empty($this->filters)) {
            return '';
        }
        $sql .= 'WHERE ';
        $sql .= implode(' AND ', array_map(function ($filterName) {
            return sprintf('%s LIKE %s', $filterName, ':' . $filterName);
        }, array_keys($this->filters)));

        return $sql;
    }

    public function sortsToQuery(string $sql): string
    {
        if (empty($this->sorts)) {
            return '';
        }
        $sql .= ' ORDER BY ';
        foreach ($this->sorts as $fieldName => $direction) {
            $sql .= $fieldName . ' ' . $direction;
        }

        return $sql;
    }

    public function limitOffsetToQuery(string $sql): string
    {
        $sql .= ' LIMIT ' . $this->size . ' OFFSET ' . $this->from;

        return $sql;
    }

    public function toQuery(): string
    {
        $sql = '';
        if (!empty($this->filters)) {
            $sql .= ' WHERE ';
            foreach ($this->filters as $fieldName => $value) {
                $sql .= $fieldName . ' =:' . $fieldName;
                if (!end($this->filters)) {
                    $sql .= ' AND ';
                }
            }
        }
        $sql .= $this->sortsToQuery($sql);

        return $this->limitOffsetToQuery($sql);
    }

    public function toQuerySearch(): string
    {
        $sql = '';
        $sql .= $this->filtersToQuery($sql);
        $sql .= $this->sortsToQuery($sql);

        return $this->limitOffsetToQuery($sql);
    }

    /**
     * Builds the WHERE clause using configured filters
     * for a SELECT count query
     *
     * @return string
     */
    public function toQueryCount(): string
    {
        $sql = '';
        if (empty($this->filters)) {
            return $sql;
        }

        return $this->filtersToQuery($sql);
    }

    public function bindValueToStatementSearch(\PDOStatement $dbStmt)
    {
        foreach ($this->filters as $fieldName => $value) {
            $dbStmt->bindValue(':' . $fieldName, "%$value%");
        }
    }

    public function bindParamsToStatement(\PDOStatement $dbStmt)
    {
        foreach ($this->filters as $fieldName => $value) {
            $dbStmt->bindParam(':' . $fieldName, $value);

        }
        foreach ($this->sorts as $fieldName => $direction) {
            $dbStmt->bindParam(':' . $fieldName, $direction);
        }
    }
}