<?php

namespace Simtabi\UsernameWrangler;

use Simtabi\UsernameWrangler\Factories\Generator;
use Simtabi\UsernameWrangler\Factories\Suggester;

class UsernameWrangler
{

    public function __construct()
    {
    }

    public function generator(array $config = []): Generator
    {
        return new Generator($config);
    }

    public function suggester(): Suggester
    {
        return new Suggester();
    }

}