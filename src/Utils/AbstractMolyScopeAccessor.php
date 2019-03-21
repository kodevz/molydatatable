<?php 
namespace Kodevz\MolyDatatable\Utils;

abstract class AbstractMolyScopeAccessor{

    
    /**
     * Set the current page for the request.
     *
     * @return int
     */
    public function setCurrentPage()
    {
        return $this->currentPage = ( $this->sliceLength + $this->perPage ) /  $this->perPage;//For Jquery DataTables.net
    }

    /**
     * Get total records for the request
     *
     * @return int
     */
    public function totalRecords()
    {
        return  (int) $this->totalRecords;
    }


    /**
     * Get Fillterd Records Count
     *
     * @return int
     */
    public function filterRecords()
    {
        return (int) $this->filteredRecords;
    }

    /**
     * Get the instance as an array
     *
     * @return array
     */
    public function outPut()
    {
        return [
            "data" => $this->toData(),
            "recordsTotal" => $this->totalRecords(),
            "recordsFiltered" => $this->filterRecords(),
            '_param' => \Request::input(),
            '_sql' => $this->paginator->toSql()
        ];
        
    }

    /**
     * Get instance of data
     *
     * @return void
     */
    public function opArray()
    {
        return $this->outPut();
    }

    
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function opJson($options = 0)
    {
        return json_encode($this->outPut(), $options);
    }

    
}