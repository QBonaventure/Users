<?php
namespace Users\Hydrator\Strategy;

use Users\Model\User;
use Zend\Stdlib\Hydrator\Strategy\DefaultStrategy;

class UserStrategy extends DefaultStrategy
{
	
	/**
	* {@inheritdoc}
	*
	* Convert an array into a User object
	*/
 	public function hydrate($value)
	{
 		var_dump($value);
		if(is_string($value))
		{
 			$value	= json_decode($value, true);
		}
		$value	= (array) $value;
		if (is_array($value))
		{
			$keyword = new User();
			$keyword->setId($value['id']);
			$keyword->setUsername($value['username']);
			$keyword->setRole($value['role']);
		}
		
		return $keyword;
	}
	
	
	
 	public function extract($keywords)
	{

		if (is_array($keywords)) {
      		foreach($keywords as $keyword)
      		{
				$keywordArray['id']		= $keyword->getId();
				$keywordArray['word']	= $keyword->getWord();
				$keywordsArray[]		= $keywordArray;
      		}
      		$keywords	= $keywordsArray;
		}
		
		return $keywords;
	}
}