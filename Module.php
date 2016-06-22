<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\View\Model\ViewModel;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\TableIdentifier;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;


use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Validator\RemoteAddr;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable;

use Zend\Authentication\Storage\Session;


class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $this->initSession(array(
//         		'remember_me_seconds' => 1800,
        		'use_cookies' => true,
        		'cookie_httponly' => true,
        ), $e->getApplication()->getServiceManager());
        $this->initAcl($e);
//         $e->getApplication()->getEventManager()->attach('route', array($this, 'authPreDispatch')); //Authentication check
    	$e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));
    	$e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'forbidden'), -999);
    }
	
    
    public function forbidden(MvcEvent $e) {
    	$error = $e->getError();
    	
    	if (empty($error) || $error != "ACL_ACCESS_DENIED") {
    		return;
    	}
    	
    	$result = $e->getResult();
    	
    	if ($result instanceof StdResponse) {
    		return;
    	}
    	
    	$baseModel = new ViewModel();
    	$baseModel->setTemplate('layout/layout');
    	
    	$model = new ViewModel();
    	$model->setTemplate('error/403');
    	
    	$baseModel->addChild($model);
    	$baseModel->setTerminal(true);
    	
    	$e->setViewModel($baseModel);
    	
    	$response = $e->getResponse();
    	$response->setStatusCode(403);
    	
    	$e->setResponse($response);
    	$e->setResult($baseModel);
    	
    	return false;
    }

    public function initSession($config, \Zend\ServiceManager\ServiceManager $sm)
	{
	    $sessionConfig = new SessionConfig();
	    $sessionConfig->setOptions($config);
	    $sessionManager = new SessionManager($sessionConfig);
		$sessionManager->getValidatorChain()
			->attach('session.validate',
						array(new HttpUserAgent(), 'isValid'));
		$sessionManager->getValidatorChain()
		    ->attach('session.validate',
		             array(new RemoteAddr(), 'isValid'));

		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		$tableGateway = new TableGateway(new TableIdentifier('sessions', 'users'), $dbAdapter);
		$saveHandler  = new DbTableGateway($tableGateway, new DbTableGatewayOptions());

		$sessionManager->setSaveHandler($saveHandler);

	    $sessionManager->start();
	    Container::setDefaultManager($sessionManager);
	}
	

	public function initAcl(MvcEvent $e)
	{
    $acl = new \Zend\Permissions\Acl\Acl();
    $roles = include __DIR__ . '/config/module.acl.roles.php';
    $allResources = array();
    foreach ($roles as $role => $values) {
    	$resources	= $values['resources'];
        $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
        $acl->addRole($role, $values['inherits']);
 
        $allResources = array_merge($resources, $allResources);
 
        //Resources
        foreach ($resources as $resource) {
            if (!$acl->hasResource($resource)) {
                $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
            }
        }
        //Restrictions
        foreach ($resources as $resource) {
            $acl->allow($role, $resource);
        }
    }
    
    //setting to view
    $e->getViewModel()->acl = $acl;
	}
 
	
	public function checkAcl(MvcEvent $e) {
	    $matches = $e->getRouteMatch();
	    $action = $matches->getParam('action');
	    $controller = explode("\\", $matches->getParam('controller'));

	    $route = $controller[0].'/'.$controller[2] . '/' . $action;
	    
	    $authService	= $e->getApplication()->getServiceManager()->get('AuthService');
	 	
	    
	    if(!$e->getViewModel()->acl->hasResource($route))
	    {
	    	$e->getResponse()->setStatusCode(404);
	    	return;
	    }
	    
	    if($authService->hasIdentity() &&
	    	!$e->getViewModel()->acl->isAllowed($authService->getStorage()->read()->role, $route))
	    {
	    	$e->setError('ACL_ACCESS_DENIED') // Pick your own value, would be better to use a const
	    		->setParam('route', $matches->getMatchedRouteName());
	    	 $e->getTarget()->getEventManager()->trigger('dispatch.error', $e);
	    }
	    
	    elseif(!$authService->hasIdentity() &&
	    		!$e->getViewModel()->acl->isAllowed('guest', $route))
	    {	
	    	$sessionContainer = new Container('base');
			$sessionContainer->offsetSet('lastRequest', $matches->getMatchedRouteName());
			$router = $e->getRouter();
			$url    = $router->assemble(array(), array('name' => 'login'));
            $response = $e->getResponse();
            $response->setStatusCode(302);
            
            $response->getHeaders()->addHeaderLine('Location', $url);
            $e->stopPropagation();
	    }
	}
	
	
	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'Users\Model\AuthStorage' => function ($sm) {
					return new Session();
				},
				'AuthService' => function ($sm) {
	                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	                $dbTableAuthAdapter  = new DbTable($dbAdapter, 
	                                          new TableIdentifier('users', 'users'),'username','password');
	                $authService = new AuthenticationService();
	                $authService->setAdapter($dbTableAuthAdapter);
					$authService->setStorage($sm->get('Users\Model\AuthStorage'));
	                return $authService;
				},
			),
		);
	}


	public function getAutoloaderConfig()
	{
		return array(
				'Zend\Loader\ClassMapAutoloader' => array(
						include __DIR__ . '/autoload_classmap.php',
				),
				'Zend\Loader\StandardAutoloader' => array(
						'namespaces' => array(
								'Users' => __DIR__ . '/src/Users',
						),
				),
		);
	}


    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
	
}