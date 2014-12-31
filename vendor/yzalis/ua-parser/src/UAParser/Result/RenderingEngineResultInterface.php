<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
interface RenderingEngineResultInterface
{
    /**
     * Returns the rendering engine family.
     *
     * @return string
     */
    public function getFamily();
    
    /**
     * Returns the rendering engine version.
     *
     * @return string
     */
    public function getVersion();
}