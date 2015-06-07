<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/pandao/slimore
 * @license     MIT License https://github.com/pandao/slimore#license
 * @version     0.1.0
 * @package     Slimore\Pagination
 */

namespace Slimore\Pagination;

use \Slimore\Debug as Debug;

/**
 * Class Paginator
 *
 * @author Pandao
 * @updateTime 2015-06-05 23:55:05
 * @package Slimore\Pagination
 */

class Paginator
{
    public  $sql;
    public  $num;
    public  $page;
    public  $prev;
    public  $next;
    public  $last;
    public  $first;
    public  $total;
    public  $range;
    public  $ranges;
    public  $offset;
    public  $pageTotal;
    public  $baseUrl = '';
    public  $select  = '*';
    public  $where   = [];
    public  $orderBy = 'id';
    public  $sortBy  = 'ASC';
    public  $model;
    public  $query;
    private $params;
    private $data    = [];

    /**
     * Construction method
     *
     * @param \Slimore\Mvc\Model $model
     * @param $page
     * @param $total
     * @param int $num 10
     * @param int $range 6
     * @return void
     */

	public function __construct(\Slimore\Mvc\Model $model, $page, $total, $num = 10, $range = 6)
	{
        $this->num   = $num;
        $this->page  = $page;
        $this->total = $total;
        $this->range = $range;
        $this->model = $model;
        $this->first = 1;

        $this->make();
	}

    /**
     * Make paginator params
     *
     * @return void
     */

    public function make()
    {
        $this->pageTotal = ceil($this->total / $this->num);
        $this->page      = ($this->page > $this->pageTotal) ? $this->pageTotal : $this->page;
        $this->offset    = ($this->page == 1) ? 0 : (($this->page - 1) * $this->num);
        $this->prev      = ($this->page == 1) ? 1 : $this->page - 1;
        $this->next      = ($this->page == $this->pageTotal) ? $this->pageTotal : $this->page + 1;
        $this->last      = $this->pageTotal;

        $this->range();
    }

    /**
     * Set/Get paginator ranges
     *
     * @return array
     */

    public function range()
    {
        $start = $this->page - $this->range;
        $start = ($start < 1) ? 1 : $start;
        $end   = $this->page + $this->range;
        $end   = ($end > $this->pageTotal) ? $this->pageTotal : $end;

        $this->ranges = [
            'start'   => $start,
            'end'     => $end
        ];

        return $this->ranges;
    }

    /**
     * Reset model
     *
     * @param \Slimore\Mvc\Model $model
     * @return \Slimore\Mvc\Model $model
     */

    public function model(\Slimore\Mvc\Model $model)
    {
        $this->model = $model;

        return $model;
    }

    /**
     * Sql query
     *
     * @param bool $toJson
     * @return array
     */

    public function query()
    {
        $model       = $this->model;

        $this->query = $model->select($this->select)
                            ->where($this->where)
                            ->orderBy($this->orderBy, $this->sortBy)
                            ->take($this->num)
                            ->offset($this->offset);

        $this->sql   = $this->query->getQuery()->toSql();

        $this->data  = $this->query->get()->toArray();

        return $this->data;
    }

    /**
     * @return mixed
     */

    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */

    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param bool $sql false
     * @return array
     */

    public function params($sql = false)
    {
        $this->params = [
            'sql'       => ($sql) ? $this->sql : '',
            'num'       => $this->num,
            'data'      => $this->data,
            'page'      => $this->page,
            'prev'      => $this->prev,
            'next'      => $this->next,
            'last'      => $this->last,
            'first'     => $this->first,
            'range'     => $this->range,
            'total'     => $this->total,
            'ranges'    => $this->ranges,
            'offset'    => $this->offset,
            'baseUrl'   => $this->baseUrl,
            'pageTotal' => $this->pageTotal
        ];

        return $this->params;
    }

    /**
     * @param bool $sql true
     */

    public function debug($sql = true)
    {
        Debug::printr($this->params($sql));
    }

    /**
     * @param bool $sql  true
     * @param bool $return  false
     */

    public function json($sql = true, $return = false)
    {
        Debug::json($this->params($sql), $return);
    }
}