<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Pointers\Pointer;

/**
 * The JSON tree.
 *
 */
class Tree
{
    /**
     * The original JSON tree.
     *
     * @var array<int, string|int>
     */
    protected array $original = [];

    /**
     * The wildcarded JSON tree.
     *
     * @var array<int, string|int>
     */
    protected array $wildcarded = [];

    /**
     * The JSON tree depth.
     *
     * @var int
     */
    protected int $depth = -1;

    /**
     * Retrieve the original JSON tree
     *
     * @return array<int, string|int>
     */
    public function original(): array
    {
        return $this->original;
    }

    /**
     * Retrieve the wildcarded JSON tree
     *
     * @return array<int, string|int>
     */
    public function wildcarded(): array
    {
        return $this->wildcarded;
    }

    /**
     * Retrieve the JSON tree depth
     *
     * @return int
     */
    public function depth(): int
    {
        return $this->depth;
    }

    /**
     * Increase the tree depth
     *
     * @return void
     */
    public function deepen(): void
    {
        $this->depth++;
    }

    /**
     * Decrease the tree depth
     *
     * @return void
     */
    public function emerge(): void
    {
        $this->depth--;
    }

    /**
     * Traverse the given object key
     *
     * @param string $key
     * @return void
     */
    public function traverseKey(string $key): void
    {
        $trimmedKey = substr($key, 1, -1);

        $this->original[$this->depth] = $trimmedKey;
        $this->wildcarded[$this->depth] = $trimmedKey;

        $this->trim();
    }

    /**
     * Trim the tree after the latest traversed key
     *
     * @return void
     */
    protected function trim(): void
    {
        array_splice($this->original, $this->depth + 1);
        array_splice($this->wildcarded, $this->depth + 1);
    }

    /**
     * Traverse an array
     *
     * @param string[] $referenceTokens
     * @return void
     */
    public function traverseArray(array $referenceTokens): void
    {
        $referenceToken = $referenceTokens[$this->depth] ?? null;
        $this->original[$this->depth] = isset($this->original[$this->depth]) ? $this->original[$this->depth] + 1 : 0;
        $this->wildcarded[$this->depth] = $referenceToken == '-' ? '-' : $this->original[$this->depth];

        $this->trim();
    }

    /**
     * Retrieve the current key
     *
     * @return string|int
     */
    public function currentKey(): string|int
    {
        $key = $this->original[$this->depth];

        return is_string($key) ? "\"$key\"" : $key;
    }
}
