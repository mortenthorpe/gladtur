<?php

namespace UAParser;

use UAParser\Result;
use UAParser\Result\ResultFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class UAParser implements UAParserInterface
{
    /**
     * @var array
     */
    private $regexes = array();

    /**
     * Constructor
     *
     * @param string $regexesPath
     */
    public function __construct($regexesPath = null)
    {
        if (null === $regexesPath) {
            $regexesPath = __DIR__.'/../../regexes.yml';
        }

        $this->regexes = Yaml::parse($regexesPath);
    }

    /**
     * {@inheritDoc}
     */
    public function parse($userAgent, $referer = null)
    {
        $data = array(
            'browser'          => $this->parseBrowser($userAgent),
            'rendering_engine' => $this->parseRenderingEngine($userAgent),
            'device'           => $this->parseDevice($userAgent),
            'operating_system' => $this->parseOperatingSystem($userAgent),
            'email_client'     => $this->parseEmailClient($userAgent, $referer),
        );

        return $this->prepareResult($data);
    }

    /**
     * Parse the user agent an extract the browser informations
     *
     * @param string $userAgent the user agent string
     *
     * @return array
     */
    protected function parseBrowser($userAgent)
    {
        $result = array(
            'family' => 'Other',
            'major'  => null,
            'minor'  => null,
            'patch'  => null,
        );

        if (!isset($this->regexes['browser_parsers'])) {
            return $result;
        }

        foreach ($this->regexes['browser_parsers'] as $expression) {
            if (preg_match('/'.str_replace('/','\/',str_replace('\/','/', $expression['regex'])).'/i', $userAgent, $matches)) {
                if (!isset($matches[1])) { $matches[1] = 'Other'; }
                if (!isset($matches[2])) { $matches[2] = null; }
                if (!isset($matches[3])) { $matches[3] = null; }
                if (!isset($matches[4])) { $matches[4] = null; }

                $result['family'] = isset($expression['family_replacement']) ? str_replace('$1', $matches[1], $expression['family_replacement']) : $matches[1];
                $result['major']  = isset($expression['major_replacement']) ? $expression['major_replacement'] : $matches[2];
                $result['minor']  = isset($expression['minor_replacement']) ? $expression['minor_replacement'] : $matches[3];
                $result['patch']  = isset($expression['patch_replacement']) ? $expression['patch_replacement'] : $matches[4];

                return $result;
            }
        }

        return $result;
    }

    /**
     * Parse the user agent an extract the rendering engine informations
     *
     * @param string $userAgent the user agent string
     *
     * @return array
     */
    protected function parseRenderingEngine($userAgent)
    {
        $result = array(
            'family' => 'Other',
            'version' => null,
        );

        if (!isset($this->regexes['rendering_engine_parsers'])) {
            return $result;
        }

        foreach ($this->regexes['rendering_engine_parsers'] as $expression) {
            if (preg_match('/'.str_replace('/','\/',str_replace('\/','/', $expression['regex'])).'/i', $userAgent, $matches)) {

                if (!isset($matches[1])) { $matches[1] = 'Other'; }
                if (!isset($matches[2])) { $matches[2] = null; }

                $result['family'] = isset($expression['family_replacement']) ? str_replace('$2', $matches[2], $expression['family_replacement']) : $matches[1];
                $result['version'] = isset($expression['version_replacement']) ? str_replace('$1', $matches[1], $expression['version_replacement']) : $matches[2];

                return $result;
            }
        }

        return $result;
    }

    /**
     * Parse the user agent an extract the operating system informations
     *
     * @param string $userAgent the user agent string
     *
     * @return array
     */
    protected function parseOperatingsystem($userAgent)
    {
        $result = array(
            'family' => 'Other',
            'major'  => null,
            'minor'  => null,
            'patch'  => null,
        );

        if (!isset($this->regexes['operating_system_parsers'])) {
            return $result;
        }

        foreach ($this->regexes['operating_system_parsers'] as $expression) {
            if (preg_match('/'.str_replace('/','\/',str_replace('\/','/', $expression['regex'])).'/i', $userAgent, $matches)) {
                if (!isset($matches[1])) { $matches[1] = 'Other'; }
                if (!isset($matches[2])) { $matches[2] = null; }
                if (!isset($matches[3])) { $matches[3] = null; }
                if (!isset($matches[4])) { $matches[4] = null; }

                $result['family'] = isset($expression['family_replacement']) ? str_replace('$1', $matches[1], $expression['family_replacement']) : $matches[1];
                $result['major']  = isset($expression['major_replacement']) ? $expression['major_replacement'] : $matches[2];
                $result['minor']  = isset($expression['minor_replacement']) ? $expression['minor_replacement'] : $matches[3];
                $result['patch']  = isset($expression['patch_replacement']) ? $expression['patch_replacement'] : $matches[4];

                return $result;
            }
        }

        return $result;
    }

    /**
     * Parse the user agent an extract the device informations
     *
     * @param string $userAgent the user agent string
     *
     * @return array
     */
    protected function parseDevice($userAgent)
    {
        $result = array(
            'constructor' => 'Other',
            'model'       => null,
            'type'        => null,
        );

        if (!isset($this->regexes['device_parsers'])) {
            return $result;
        }

        foreach ($this->regexes['device_parsers'] as $expression) {
            if (preg_match('/'.str_replace('/','\/',str_replace('\/','/', $expression['regex'])).'/i', $userAgent, $matches)) {
                if (!isset($matches[1])) { $matches[1] = 'Other'; }
                if (!isset($matches[2])) { $matches[2] = null; }
                if (!isset($matches[3])) { $matches[3] = null; }

                $result['constructor'] = isset($expression['constructor_replacement']) ? str_replace(array('$1', '$2'), array($matches[1], $matches[2]), $expression['constructor_replacement']) : $matches[1];
                $result['model']       = isset($expression['model_replacement']) ? str_replace(array('$1', '$2'), array($matches[1], $matches[2]), $expression['model_replacement']) : $matches[2];
                $result['type']        = isset($expression['type_replacement']) ? $expression['type_replacement'] : $matches[3];

                return $result;
            }
        }

        return $result;
    }

    /**
     * Parse the user agent and optionnaly the referer an extract the email client informations
     *
     * @param string $userAgent the user agent string
     * @param string|null $referer A request referer to parse.
     *
     * @return array
     */
    protected function parseEmailClient($userAgent, $referer = null)
    {
        $result = array(
            'family' => 'Other',
            'major'  => null,
            'minor'  => null,
            'patch'  => null,
        );

        if (!isset($this->regexes['email_client_parsers'])) {
            return $result;
        }

        foreach ($this->regexes['email_client_parsers'] as $expression) {
            if (preg_match('/'.str_replace('/','\/',str_replace('\/','/', $expression['regex'])).'/i', $userAgent, $matches)) {
                if (!isset($matches[1])) { $matches[1] = 'Other'; }
                if (!isset($matches[2])) { $matches[2] = null; }
                if (!isset($matches[3])) { $matches[3] = null; }
                if (!isset($matches[4])) { $matches[4] = null; }
                if (!isset($matches[5])) { $matches[5] = null; }

                $result['family'] = isset($expression['family_replacement']) ? str_replace('$1', $matches[1], $expression['family_replacement']) : $matches[1];
                $result['major']  = isset($expression['major_replacement']) ? $expression['major_replacement'] : $matches[2];
                $result['minor']  = isset($expression['minor_replacement']) ? $expression['minor_replacement'] : $matches[3];
                $result['patch']  = isset($expression['patch_replacement']) ? $expression['patch_replacement'] : $matches[4];
                $result['type']   = isset($expression['type_replacement']) ? $expression['type_replacement'] : $matches[5];

                goto referer;
            }
        }

        referer:

        if ($result['family'] == 'Other' && null !== $referer) {
            foreach ($this->regexes['email_client_parsers'] as $emailClientRegexe) {
                if (preg_match('/'.str_replace('/','\/',str_replace('\/','/', $emailClientRegexe['regex'])).'/i', $referer, $emailClientRefererMatches)) {
                    if (!isset($emailClientRefererMatches[1])) { $emailClientRefererMatches[1] = 'Other'; }
                    if (!isset($emailClientRefererMatches[2])) { $emailClientRefererMatches[2] = null; }

                    $result['family'] = isset($emailClientRegexe['family_replacement']) ? str_replace('$1', $emailClientRefererMatches[1], $emailClientRegexe['family_replacement']) : $emailClientRefererMatches[1];
                    $result['type']   = isset($emailClientRegexe['type_replacement']) ? $emailClientRegexe['type_replacement'] : $emailClientRefererMatches[2];

                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * Prepare the result set
     *
     * @param array $data An array of data.
     *
     * @return ResultInterface
     */
    protected function prepareResult(array $data = array())
    {
        $resultFactory = new ResultFactory();

        return $resultFactory->createFromArray($data);
    }
}
