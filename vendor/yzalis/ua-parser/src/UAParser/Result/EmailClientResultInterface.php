<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
interface EmailClientResultInterface
{
    /**
     * Returns the email client family name.
     *
     * @return string
     */
    public function getFamily();

    /**
     * Returns the email client major version.
     *
     * @return string
     */
    public function getMajor();

    /**
     * Returns the email client minor version.
     *
     * @return string
     */
    public function getMinor();

    /**
     * Returns the email client patch version.
     *
     * @return string
     */
    public function getPatch();

    /**
     * Returns the email client type.
     *
     * @return string
     */
    public function getType();

    /**
     * Check the email client type.
     *
     * @param string $type Check if type is (webmail|desktop)
     * 
     * @return boolean
     */
    public function is($type);

    /**
     * Extracts data from an array.
     *
     * @param array $data An array.
     */
    public function fromArray();
}