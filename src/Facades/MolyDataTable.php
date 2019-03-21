<?php
namespace Kodevz\MolyDatatable\Facades;
use Illuminate\Support\Facades\Facade;
class MolyDataTable extends Facade {
    protected static function getFacadeAccessor() {
        return 'molydatatable';
    }
}