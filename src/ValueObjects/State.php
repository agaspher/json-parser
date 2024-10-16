<?php

namespace Cerbero\JsonParser\ValueObjects;

use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\CompoundBegin;
use Cerbero\JsonParser\Tokens\Parser;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\Tokens\Tokens;
use Closure;

/**
 * The JSON parsing state.
 *
 */
final class State
{
    /**
     * The JSON tree.
     *
     * @var Tree
     */
    public Tree $tree;

    /**
     * The JSON buffer.
     *
     * @var Parser|string
     */
    private $buffer = '';

    /**
     * Whether an object key is expected.
     *
     * @var bool
     */
    public bool $expectsKey = false;

    /**
     * The expected token.
     *
     * @var int
     */
    public int $expectedToken = Tokens::COMPOUND_BEGIN;

    private ?Pointers $pointers;
    private Closure $lazyload;

    /**
     * Instantiate the class.
     *
     * @param Pointers $pointers
     * @param Closure $lazyLoad
     */
    public function __construct(Pointers $pointers, Closure $lazyLoad)
    {
        $this->pointers = $pointers;
        $this->lazyload = $lazyLoad;
        $this->tree = new Tree($pointers);
    }

    /**
     * Determine whether the parser can stop parsing
     *
     * @return bool
     */
    public function canStopParsing(): bool
    {
        return $this->pointers->wereFoundInTree($this->tree);
    }

    /**
     * Call the current pointer callback
     *
     * @param mixed $value
     * @param mixed $key
     * @return mixed
     */
    public function callPointer($value, &$key)
    {
        return $this->pointers->matching()->call($value, $key);
    }

    /**
     * Mutate state depending on the given token
     *
     * @param Token $token
     * @return void
     */
    public function mutateByToken(Token $token): void
    {
        $this->tree->traverseToken($token, $this->expectsKey);

        if ($this->tree->isMatched() && ((!$this->expectsKey && $token->isValue()) || $this->tree->isDeep())) {
            $pointer = $this->pointers->markAsFound();

            if ($token instanceof CompoundBegin && $pointer->isLazy) {
                $this->buffer = ($this->lazyLoad)();
                $token->shouldLazyLoad = true;
            } else {
                /** @phpstan-ignore-next-line */
                $this->buffer .= $token;
            }
        }

        $token->mutateState($this);
    }

    /**
     * Determine whether the buffer contains tokens
     *
     * @return bool
     */
    public function hasBuffer(): bool
    {
        return $this->buffer != '';
    }

    /**
     * Retrieve the value from the buffer and reset it
     *
     * @return Parser|string
     */
    public function value()
    {
        $buffer = $this->buffer;

        $this->buffer = '';

        return $buffer;
    }
}
