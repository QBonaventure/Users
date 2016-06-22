<?php
namespace Users\Factory;

use Users\Controller\LoginController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginControllerFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 *
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$authAdapter		= $serviceLocator->getServiceLocator()->get('AuthAdapter');
		$authService        = $serviceLocator->getServiceLocator()->get('AuthService');
		$authService->setAdapter($authAdapter);

		return new LoginController($authService);
	}
}