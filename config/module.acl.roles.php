<?php
return array(
    'guest'=> array(
    	'inherits'	=> null,
	    'resources' => array(
	        'Application/Index/index',
	        'Application/Index/contact',
	        'Application/Index/biography',
	        'Application/Index/curriculum-vitae',
	        'Blog/Index/index',
	    	'Blog/Posts/index',
	        'Blog/Posts/read',
	        'Blog/Posts/category-index',
	        'Users/Login/login',
	    ),
    ),
    'member'=> array(
    	'inherits'	=> 'guest',
	    'resources' => array(
	    ),
    ),
    'administrator'=> array(
    	'inherits'	=> 'member',
	    'resources' => array(
	        'Blog/Administration/index',
	        'Blog/Posts/administration-index',
	        'Blog/Posts/create',
	        'Blog/Posts/edit',
	        'Blog/Posts/preview',
	    ),
    ),
);