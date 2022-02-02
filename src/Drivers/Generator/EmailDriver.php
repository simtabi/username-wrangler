<?php

namespace Simtabi\UsernameWrangler\Drivers\Generator;

use Simtabi\UsernameWrangler\Drivers\Suggester\BaseSuggesterDriver;

class EmailDriver extends BaseSuggesterDriver
{
    public $field = 'email';

    /**
     * Strip everything after the @ symbol.
     *
     * @param string $text
     *
     * @return string
     */
    public function first(string $text): string
    {
        return preg_replace('/(@.*)$/', '', $text);
    }
}
