<?php

namespace Simtabi\UsernameWrangler\Facades;

use Illuminate\Support\Facades\Facade;

class UsernameWranglerFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return 'username';
    }
}
