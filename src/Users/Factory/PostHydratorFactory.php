<?php
namespace Users\Factory;

use Users\Hydrator\UserHydrator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class UserHydratorFactory implements FactoryInterface {
	
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$hydrator	= new UserHydrator();
		return $hydrator;
	}
}