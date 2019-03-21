<?php 
namespace Kodevz\MolyDatatable\Utils;

abstract class AbstractMolyUtils{

    /**
     * @param int $size
     * @return AbstractGenerator
     */
    public function data()
    {
        return $this->paginator->toArray()['data'];
    }

    public function count()
    {
        return $this->paginator->count();
    }

    public function currentPage()
    {
        return $this->paginator->currentPage();
    }

    public function firstItem()
    {
        return $this->paginator->firstItem();
    }

    public function hasMorePages()
    {
        return $this->paginator->hasMorePages();
    }

    public function lastItem()
    {
        return $this->paginator->lastItem();
    }

    public function nextPageUrl()
    {
        return $this->paginator->nextPageUrl();
    }

    public function onFirstPage()
    {
        return $this->paginator->onFirstPage();
    }

    public function perPage()
    {
        return $this->paginator->perPage();
    }

    public function previousPageUrl()
    {
        return $this->paginator->previousPageUrl();
    }

    public function total()
    {
        return $this->paginator->total();
    }

    public function url()
    {
        return NULL;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'current_page' => $this->paginator->currentPage(),
            'data' => $this->paginator->toArray(),
            'first_page_url' =>$this->paginator->url(1),
            'from' => $this->paginator->firstItem(),
            'last_page' => $this->paginator->lastPage(),
            'last_page_url' => $this->paginator->url($this->lastPage()),
            'next_page_url' => $this->paginator->nextPageUrl(),
            'path' => '',
            'per_page' => $$this->paginator->perPage(),
            'prev_page_url' => $this->paginator->previousPageUrl(),
            'to' => $this->paginator->lastItem(),
            'total' => $this->paginator->total(),
        ];
    }
    

    
    
}