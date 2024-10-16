<?php

namespace Cerbero\JsonParser\Decoders;

/**
 * The decoder using the built-in JSON decoder.
 *
 */
final class JsonDecoder extends AbstractDecoder
{
    private bool $decodesToArray = true;
    private int $depth = 512;

    /**
     * Instantiate the class.
     *
     * @param bool $decodesToArray
     * @param int<1, max> $depth
     */
    public function __construct(bool $decodesToArray = true, int $depth = 512)
    {
        $this->depth = $depth;
        $this->decodesToArray = $decodesToArray;
    }

    /**
     * Retrieve the decoded value of the given JSON
     *
     * @param string $json
     * @return mixed
     * @throws \Throwable
     */
    protected function decodeJson(string $json)
    {
        return json_decode($json, $this->decodesToArray, $this->depth, JSON_THROW_ON_ERROR);
    }
}
