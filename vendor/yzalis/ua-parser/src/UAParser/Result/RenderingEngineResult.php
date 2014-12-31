<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class RenderingEngineResult implements RenderingEngineResultInterface
{
    /**
     * @var string
     */
    private $family = null;

    /**
     * @var string
     */
    private $version = null;
    
    /**
     * {@inheritDoc}
     */
    public function getFamily()
    {
        return $this->family;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function __toString()
    {
        return implode(' ', array(
            $this->getFamily(),
            $this->getVersion(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function fromArray(array $data = array())
    {
        if (isset($data['family'])) {
            $this->family = (string) $data['family'];
        }
        if (isset($data['version'])) {
            $this->version = (string) $data['version'];
        }
    }
}