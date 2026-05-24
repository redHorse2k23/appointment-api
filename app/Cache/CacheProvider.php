<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class CacheProvider
{

    protected $key;
    protected $expiration;    
    protected $store;

    public function __construct($key, $expiration = 60,$store = null)
    {
        $this->key = $key;
        $this->expiration = $expiration;
        $this->store = $store === null ? config('cache.default') : $store;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function store(){
        return Cache::store($this->store);
    }

    public function setData($data){
        $this->store()->put($this->key, $data, $this->expiration);
    }

    public function getData(){
        return $this->store()->get($this->key);
    }

    public function rememberData($callback){
        return $this->store()->remember($this->key, $this->expiration, $callback);
    }

    public function hasData(){
        return $this->store()->has($this->key);
    }

    public function clearData(){
        $this->store()->forget($this->key);
    }

  
}