<?php
namespace Users;

return array(
    'controllers' => array(
        'factories' => array(
            'Users\Controller\Login' => 'Users\Factory\LoginControllerFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller' => 'Login',
                        'action'     => 'login',
                    ),
                ),
                'may_terminate' => true,
            ),
		),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'service_manager' => array(
//         'abstract_factories' => array(
//             'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
//             'Zend\Log\LoggerAbstractServiceFactory',
//         ),
    	'invokables' => array(
//     		'Blog\Form\KeywordFieldset'	=> 'Blog\Form\KeywordFieldset',
			'AuthService'	=> 'Zend\Authentication\AuthenticationService',
    	),
        'factories' => array(
			'AuthAdapter'	=> 'Users\Adapter\PasswordHashFactory',
        	'Users\Hydrator\UserHydratorInterface'	=> 'Users\Factory\UserHydratorFactory',
        ),
    ),
);