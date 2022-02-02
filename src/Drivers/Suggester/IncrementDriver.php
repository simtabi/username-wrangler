<?php

namespace Simtabi\UsernameWrangler\Drivers\Suggester;

class IncrementDriver extends BaseSuggesterDriver
{
    /**
     * @inheritDoc
     */
    public function makeUnique(string $username): string
    {
        $inc = 0;

        while(!$this->isUnique($username.$inc)) {
            $inc++;
        }

        return $username.$inc;
    }

}
