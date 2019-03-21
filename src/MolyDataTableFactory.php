<?php

namespace Kodevz\MolyDatatable;

use Illuminate\Http\Request;
use Kodevz\MolyDatatable\MolyDataTableFactoryInterface;
use Kodevz\MolyDatatable\Utils\AbstractMolyScopeAccessor;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;



/**
 * Class UkaDataTableFactory
 *
 * Laravel Data Table is distributed under MIT
 * Copyright (C) 2019 Karthikesan Uthirapathy <karthi.php.developer at gmail dot com>
 *
 * @package uka\datatable
 */
class MolyDataTableFactory extends AbstractMolyScopeAccessor
{   

    /**
     * The total number of items before slicing.
     *
     * @var int
     */
    protected $perPage;


    /**
     * The total number of items slicing
     *
     * @var int
     */
    protected $sliceLength;



    /**
     * The current page number for request
     *
     * @var int
     */
    protected $currentPage;

    /**
     * The columns that can be used this instance
     *
     * @var array
     */
    protected $columns;

    /**
     * The paginator
     *
     * @var array
     */
    protected $paginator;

    /**
     * The total records of this instance of paginator
     *
     * @var int
     */
    protected $totalRecords;

    /**
     * Total filtered record of items after slicing
     *
     * @var int
     */
    protected $filteredRecords;

    /**
     * Global filter of values for all columns
     *
     * @var array
     */
    protected $globalFilter;

    /**
     * Filter of column values
     *
     * @var array
     */
    protected $filters;

    /**
     * Custom filter of column values
     *
     * @var array
     */
    protected $customFilters;

    /**
     * Multi sorting meta data
     *
     * @var array
     */
    protected $multiSortMeta;





    /**
     * Create a new Datatable instance.
     */
    public function __construct() 
    {      
      
        //For Material  Prime NG DataTable


        $this->perPage = \Request::input('rows');

        $this->sliceLength = \Request::input('first');

        $this->columns = \Request::input('columns');

        $this->globalFilter = \Request::input('globalFilter');

        $this->filters = \Request::input('filters');

        $this->customFilters = \Request::input('customFilters');

        $this->multiSortMeta = \Request::input('multiSortMeta');

        $this->currentPage = $this->perPage && $this->setCurrentPage();        
    }


    /**
     * Create Datatable
     *
     * @param [type] $items
     * @return void
     */
    public function create($items)
    {
        if ( $items instanceof \Illuminate\Database\Eloquent\Model )
        {
            $this->modelInstance($items);
        } 
        
        if ( $items instanceof \Illuminate\Database\Eloquent\Builder )
        {
            $this->builderInstance($items);
        } 

        return $this;
    }

    /**
     * Create a data table for model instance
     *
     * @param \Illuminate\Database\Eloquent\Model $items
     * @return self
     */
    public function modelInstance(Model $items) :  self 
    {
        $items = $this->sqlCalcFoundRows($items);

        $items = $this->ifInputHasSearch($items);

        $items = $this->ifInputHasSort($items);

        $this->paginator = $items->skip($this->sliceLength)->take($this->perPage);

        return $this;
    }


    /**
     * Create a datatable for builder instance 
     *
     * @param \Illuminate\Database\Eloquent\Builder $items
     * @return $this
     */
    public function builderInstance(Builder $items) : self
    {
        $items = $this->ifInputHasSearch($items);

        $items = $this->ifInputHasSort($items);

        $this->paginator = $items->skip($this->sliceLength)->take($this->perPage);

        return $this;
    }

    /**
     * Append raw query for found rows
     *
     * @param \Illuminate\Database\Eloquent\Model $items
     * @return \Illuminate\Database\Eloquent\Builder $items
     */
    public function sqlCalcFoundRows(Model $items) : Builder
    {
        return $items::select(DB::raw("SQL_CALC_FOUND_ROWS *"));
    }

    /**
     * Check input has search array
     *
     * @param array $items
     * @return \Illuminate\Database\Eloquent\Builder $items
     */
    public function ifInputHasSearch($items) 
    {
        $items = $this->globalSearchFilters($items);

        $items = $this->multiColumnFilters($items);

        $items = $this->customKeyFilters($items);
        
        return $items;
    }

    /**
     * Check input hava global search
     *
     * @param array $items
     * @return \Illuminate\Database\Eloquent\Builder $items
     */
    protected function globalSearchFilters(Builder $items) : Builder
    {
        if($this->globalFilter)
        {
            $search = $this->globalFilter;

            $searchableColumns = array_values(array_filter($this->columns,array($this, 'filterGlobalSearchableColumns')));

            if($search)
            {
                if($searchableColumns)
                {
                    
                    $items->where(function($query) use ($search ,$searchableColumns){
                        
                        foreach($searchableColumns as $key => $row)
                        {
                            if($key == 0)
                            {
                                $query->where($row['field'],'like','%'.$search.'%');
                            }
                            if($key > 0)
                            {
                                $query->orWhere($row['field'],'like','%'.$search.'%');
                            }  
                        } 
                        
                        return $query;
                    });
                }
            }
        }

        return $items;
    }

    /**
     * Check input hava multi column filters
     *
     * @param array $items
     * @return \Illuminate\Database\Eloquent\Builder $items
     */
    protected function multiColumnFilters(Builder $items) : Builder
    {
        if($this->filters)
        {
            $filters = collect($this->filters)->forget('global')->toArray();
           
            if($filters)
            {
                $items->where(function($query) use ($filters){

                    foreach($filters as $key => $row)
                    {
                       
                        if($row['matchMode'] == 'in')
                        {
                            $query->whereIn($key, $row['value']);
                        }
                        else if($row['matchMode'] == 'dateRange')
                        {
                            if($row['value'][1])
                            { 
                                $row['value'][1] = Carbon::parse($row['value'][1])->addDay()->format('Y-m-d');

                                $query->whereBetween($key, $row['value']); 
                            }
                            else
                            { 
                                $query->whereDate($key,'=',Carbon::parse($row['value'][0])->addDay()->format('Y-m-d')); 
                            }
                        }
                        else
                        {
                            $query->where($key,'like',$row['value']);
                        }
                    }

                    return $query;

                });
            } 
        }

        
        

        return $items;
    }


    /**
     * Check custom filter options
     *
     * @param array $items
     * @return \Illuminate\Database\Eloquent\Builder $items
     */
    protected function customKeyFilters(Builder $items) : Builder
    {
        if($this->customFilters)
        {   
            $filters = $this->customFilters;

            if(count($filters))
            {
                $items->where(function($query) use ($filters){

                    foreach($filters as $key => $row)
                    {

                        if(isset($row['matchMode']))
                        {
                            if($row['matchMode'] == 'in')
                            {
                                $query->whereIn($key, $row['value']);
                            }
                            else if($row['matchMode'] == 'dateRange')
                            {
                                if($row['value'][1])
                                { 
                                    $row['value'][1] = Carbon::parse($row['value'][1])->addDay()->format('Y-m-d');
    
                                    $query->whereBetween($key, $row['value']); 
                                }
                                else
                                { 
                                    $query->whereDate($key,'=',Carbon::parse($row['value'][0])->addDay()->format('Y-m-d')); 
                                }
                            }
                            else
                            {
                                $query->whereIn($key, $row['value']); 
                            }
                        }
                        else
                        {
                            $query->whereIn($key, $row['value']); 
                        }
                                            
                    }

                    return $query;

                });
            } 
        }
        
        

        return $items;
    }


    /**
     * Check global searchable column are in request input of columns
     *
     * @param array $col
     * @return boolean
     */
    private function filterGlobalSearchableColumns(array $col) : bool
    {
        return ($col['globalsearch'] && TRUE);
    }
    
    /**
     * Check column searchable column are in request input of columns
     *
     * @param array $col
     * @return boolean
     */
    private function filterColumnSearchableColumns(array $col) : bool
    {
        return ($col['columnsearch'] && TRUE);
    }
  
    /**
     * Check input has sort array
     *
     * @param \Illuminate\Database\Eloquent\Builder $items
     * @return \Illuminate\Database\Eloquent\Builder $items
     */
    public function ifInputHasSort(Builder $items) : Builder
    {
        if($this->multiSortMeta)
        {   
            foreach($this->multiSortMeta as $key => $row)
            {
                $items->orderBy($row['field'], $row['order'] == 1 ? 'ASC' : 'DESC');
            }   
        }

        return $items;
    }

    /**
     * Get result set of data  
     *
     * @return array $data
     */
    public function toData()
    {

        $data = $this->paginator->get()->toArray();

        $this->setTotalRecords();

        $this->setFilterRecords($data);

        return $data;
    }

    
    /**
     * Set total records 
     *
     * @return void
     */
    public function setTotalRecords()
    {
        $countData = DB::Select(DB::raw("Select FOUND_ROWS() as rowsCnt"));

        if(count($countData))
        {
           $this->totalRecords  = $countData[0]->rowsCnt;
        }
    }

    /**
     * Set Filterd Records Count
     *
     * @param array $data
     * @return $this
     */
    public function setFilterRecords($data = array())
    {
        $this->filteredRecords = count($data);

        return $this;
    }

    /**
     * Moly datatable extension Export
     *
     * @param Builder $items
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder $items
     */
    public function export(Builder $items, $params = array()) : Builder
    {
        //Params
        $params = json_decode(stripslashes(\Request::input('param')), true);
       
        $this->columns = $params['columns'];

        $this->globalFilter = $params['globalFilter'];

        $this->filters = $params['filters'];

        $this->customFilters = isset($params['customFilters']) ? $params['customFilters'] : [];

       
        $this->multiSortMeta = isset($params['multiSortMeta']) && $params['multiSortMeta'];
       
        $items = $this->ifInputHasSearch($items);
       
        $items = $this->ifInputHasSort($items);

    
        return $items;
    }


    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

}
