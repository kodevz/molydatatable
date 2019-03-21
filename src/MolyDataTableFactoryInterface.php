<?php 

namespace Kodevz\MolyDatatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
interface MolyDataTableFactoryInterface{

    /**
     * Create datatable 
     *
     */
    public function create($items);

    /**
     * Create Uka Datatable with count
     *
     * @param \Illuminate\Database\Eloquent\Model $items
     */
    public function modelInstance(Model $items);


    /**
     * Create Uka Datatable
     *
     * @param \Illuminate\Database\Eloquent\Builder  
     */
    public function builderInstance(Builder $items);

    /**
     * Check input has search array
     *
     * @param Builder $items
     */
    public function ifInputHasSearch(Builder $items);

}