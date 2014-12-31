<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class DeviceResult implements DeviceResultInterface
{
    /**
     * @var string
     */
    private $constructor = null;
    
    /**
     * @var string
     */
    private $model = null;
    
    /**
     * @var string
     */
    private $type = 'desktop';

    /**
     * To string
     * 
     * @return string
     */
    public function __toString()
    {
        return implode(' ', array(
            $this->getConstructor(),
            $this->getModel(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getConstructor()
    {
        return $this->constructor;
    }

    /**
     * {@inheritDoc}
     */
    public function getModel()
    {
        return $this->model;
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
     * Check if the device is a mobile
     * 
     * @return boolean
     */
    public function isMobile()
    {
        return $this->is('mobile');
    }

    /**
     * Check if the device is a tablet
     * 
     * @return boolean
     */
    public function isTablet()
    {
        return $this->is('tablet');
    }

    /**
     * Check if the device is a desktop computer
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
        if (isset($data['constructor'])) {
            $this->constructor = (string) $data['constructor'];
        }
        if (isset($data['model'])) {
            $this->model = (string) $data['model'];
        }
        if (isset($data['type']) && in_array($data['type'], array('mobile', 'tablet', 'desktop'))) {
            $this->type = (string) $data['type'];
        }
    }
}