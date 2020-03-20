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
        if (!empty($this->sorts)) {
            $sql .= ' ORDER BY ';
            foreach ($this->sorts as $fieldName => $direction) {
                $sql .= ':' . $fieldName . ' ' . $direction;
            }
        }
        $sql .= ' LIMIT ' . $this->size . ' OFFSET ' . $this->from;

        return $sql;
    }

    public function toQuerySearch(): string
    {
        $sql = '';
        if (empty($this->filters)) {
            return $sql;
        }
        $sql .= ' WHERE ';
        foreach ($this->filters as $fieldName => $value) {
            $sql .= $fieldName . ' LIKE :' . $fieldName;
            if (!end($this->filters)) {
                $sql .= ' AND ';
            }
        }
        $sql .= ' LIMIT ' . $this->size . ' OFFSET ' . $this->from;

        return $sql;
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