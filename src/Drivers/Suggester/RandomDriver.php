<?php

namespace Simtabi\UsernameWrangler\Drivers\Suggester;

class RandomDriver extends BaseSuggesterDriver
{
    /**
     * @inheritDoc
     */
    public function makeUnique(string $username): string
    {
        $value = rand();

        while(!$this->isUnique($username.$value)) {
            $value = rand();
        }

        return $username.$value;
    }

}
