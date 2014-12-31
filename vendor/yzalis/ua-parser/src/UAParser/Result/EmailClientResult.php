<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class EmailClientResult implements EmailClientResultInterface
{
    /**
     * @var string
     */
    private $family = null;
    
    /**
     * @var string
     */
    private $major = null;
    
    /**
     * @var string
     */
    private $minor = null;
    
    /**
     * @var string
     */
    private $patch = null;
    
    /**
     * @var string
     */
    private $type = 'desktop';

    public function __toString()
    {
        return $this->getFamily().' '.implode('.', array(
            $this->getMajor(),
            $this->getMinor(),
            $this->getPatch(),
        ));
    }

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
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * {@inheritDoc}
     */
    public function getMinor()
    {
        return $this->minor;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function is($type)
    {
        return $type === $this->type;
    }

    /**
     * Check if the email client is a wemail
     * 
     * @return boolean
     */
    public function isWebmail()
    {
        return $this->is('webmail');
    }

    /**
     * Check if the email client is a desktop software
     * 
     * @return boolean
     */
    public function isDesktop()
    {
        return $this->is('desktop');
    }

    /**
     * {@inheritDoc}
     */
    public function fromArray(array $data = array())
    {
        if (isset($data['family'])) {
            $this->family = (string) $data['family'];
        }
        if (isset($data['major'])) {
            $this->major = (string) $data['major'];
        }
        if (isset($data['minor'])) {
            $this->minor = (string) $data['minor'];
        }
        if (isset($data['patch'])) {
            $this->patch = (string) $data['patch'];
        }
        if (isset($data['type'])) {
            $this->type = (string) $data['type'];
        }
    }
}