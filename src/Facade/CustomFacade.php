<?php
use Illuminate\Support\Facades\Facade;

class CustomFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Spatie\Image\Image::class;
    }
}
