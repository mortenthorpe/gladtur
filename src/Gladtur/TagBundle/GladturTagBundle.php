<?php

namespace Gladtur\TagBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GladturTagBundle extends Bundle
{
	
	public function getBooleanTxt($boolVal=true){
		return ($boolVal)?'Ja':'Nej';
	}
}
