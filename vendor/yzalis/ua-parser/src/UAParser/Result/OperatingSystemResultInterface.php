<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
interface OperatingSystemResultInterface
{
    /**
     * Returns the operating system family name.
     *
     * @return string
     */
    public function getFamily();

    /**
     * Returns the operating system major version.
     *
     * @return string
     */
    public function getMajor();

    /**
     * Returns the operating system minor version.
     *
     * @return string
     */
    public function getMinor();

    /**
     * Returns the operating system patch version.
     *
     * @return string
     */
    public function getPatch();

    /**
     * Extracts data from an array.
     *
     * @param array $data An array.
     */
    public function fromArray();
}