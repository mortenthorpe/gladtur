<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 26/08/14
 * Time: 14.57
 */

namespace Gladtur\TagBundle\Controller;

class loggableParam{
    private $paramName;
    private $paramValue;
    private $paramMappedTo;

    public function __construct($paramName = '', $paramValue=null, $paramMappedTo=null){
        $this->paramName = $paramName;
        $this->paramValue = $paramValue;
        $this->paramMappedTo = $paramMappedTo;
    }

    public function dump(){

    }
}