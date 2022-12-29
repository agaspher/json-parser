<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\State;

/**
 * The scalar string token.
 *
 */
final class ScalarString extends Token
{
    /**
     * Whether this token is an object key.
     *
     * @var bool
     */
    private bool $isKey = false;

    /**
     * Retrieve the token type
     *
     * @return int
     */
    public function type(): int
    {
        return Tokens::SCALAR_STRING;
    }

    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        if ($this->isKey = $state->expectsKey) {
            $state->expectsKey = false;
        }
    }

    /**
     * Determine whether this token ends a JSON chunk
     *
     * @return bool
     */
    public function endsChunk(): bool
    {
        return !$this->isKey;
    }
}
