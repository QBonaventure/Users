<?php
namespace Users\Hydrator;


use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Hydrator\ClassMethods;

class UserHydrator extends ClassMethods implements HydratorInterface
{
	public function __construct($underscoreSeparatedKeys = true) {
        parent::__construct($underscoreSeparatedKeys);
	}
	
}