<?php


trait Reshortz_MetaWorker
{
    public $prefix = 'reshortz_';

    /**
     * Make key for the meta box item
     *
     * @param $key
     * @return string
     */
    protected function makeKey($key)
    {
        return $this->prefix . $key;
    }
}
