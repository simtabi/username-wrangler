<?php

namespace Simtabi\UsernameWrangler\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Simtabi\UsernameWrangler\Factories\Generator;

interface SuggesterDriver
{
    /**
     * Driver constructor.
     *
     * @param Generator $generator
     */
    public function __construct(Generator $generator);

    /**
     * Generate suggestion collection.
     *
     * @param string|null $name
     * @return Collection
     */
    public function generateSuggestions(?string $name = null): Collection;

    /**
     * Set amount of suggestions.
     *
     * @param int $amount
     * @return $this
     */
    public function setAmount(int $amount): static;

    /**
     * Get the amount of suggestions.
     *
     * @return int
     */
    public function getAmount(): int;

    /**
     * Get the suggestion collection.
     *
     * @return Collection
     */
    public function suggestions(): Collection;

    /**
     * Check if a given username is unique.
     *
     * @param Model $model
     * @param string $username
     * @return bool
     */
    public function isUnique(Model $model, string $username): bool;

    /**
     * Make the given username unique.
     *
     * @param string $username
     * @return string
     */
    public function makeUnique(string $username): string;
}
