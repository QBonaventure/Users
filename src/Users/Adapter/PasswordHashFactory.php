<?php
namespace Users\Adapter;

use Users\Adapter\PasswordHash;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class PasswordHashFactory implements FactoryInterface {
	
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$dbAdapter			= $serviceLocator->get('Zend\Db\Adapter\Adapter');
		$service	= new PasswordHash($dbAdapter, 'users.users', 'username', 'password');
		return $service;
	}
}