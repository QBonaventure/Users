<?php
namespace Users\Form\Login;
 
// use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
 
class Login extends Form
 
{
    public function __construct()
    {
        parent::__construct('Login\forms\login');
 
        $this->setAttributes(array('method' => 'post',));
 
        $this->add(
            array(
                'name' => 'email_address',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'placeholder' => 'Email address',
                    'required' => 'required',
                ),
                'options' => array(
                    'label' => 'Email address',
                    'label_attributes' => array(
                        'class' => 'control-label'
                    ),
                ),
            )
        );
 
        $this->add(
            array(
                'name' => 'password',
                'type' => 'Zend\Form\Element\Password',
                'attributes' => array(
                    'placeholder' => 'Password',
                    'required' => 'required',
                ),
                'options' => array(
                    'label' => 'Password',
                    'label_attributes' => array(
                        'class' => 'control-label'
                    ),
                ),
            )
        );
 
        $this->add(
            array(
                'name' => 'login',
                'type' => 'Zend\Form\Element\Submit',
                'attributes' => array(
                    'value' => 'Login',
                    'class' => 'btn',
                    'id' => 'loginbutton',
                ),
            )
        );
 
    }
}