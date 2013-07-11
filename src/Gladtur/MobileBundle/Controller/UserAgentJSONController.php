<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/2/13
 * Time: 3:39 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;


interface UserAgentJSONController {

    /**
     * @param mixed $convertFromJSON
     */
    public function setConvertFromJSON($convertFromJSON);

    /**
     * @return mixed
     */
    public function getConvertFromJSON();
}
