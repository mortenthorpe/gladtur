<?php

namespace UAParser;

use UAParser\Result\ResultInterface;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
interface UAParserInterface
{
    /**
     * Parse a given user agent string.
     *
     * @param string $userAgent A user agent string to parse.
     * @param string|null $referer A request referer to parse.
     *
     * @return ResultInterface A ResultInterface result object.
     */
    public function parse($userAgent, $referer = null);
}