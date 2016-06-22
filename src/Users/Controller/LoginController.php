<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Zend\View\Model\ViewModel;

use Zend\Authentication\AuthenticationServiceInterface;

use Users\Form\Login\Login;
use Users\Form\Login\LoginValidator;

use Zend\Db\Sql\TableIdentifier;
use Zend\Db\Adapter\Adapter;

use Zend\Session\Container;

use Inner\Cryptography;


class LoginController extends AbstractActionController
{
	protected $authService;
	
	public function __construct(AuthenticationServiceInterface $authService) {
		$this->authService	= $authService;
	}

	public function loginAction() {
// 		$crypt	= new Cryptography\Service();

        $form = new Login();
        $request = $this->getRequest();
 
        if ($request->isPost()) {
 
            //Validate the form
            $formValidator = new LoginValidator();
            {
                $form->setInputFilter($formValidator->getInputFilter());
                $form->setData($request->getPost());
            }
 
            if ($form->isValid()) {
 
                $formData = $form->getData();
//                 $dbAdapter = $this->authService->getAdapter();
 
                $authAdapter = $this->authService->getAdapter();
 
                $authAdapter->setIdentity($formData['email_address']);
                $authAdapter->setCredential($formData['password']);
                // Perform the authentication query, saving the result
                $result = $this->authService->authenticate($authAdapter);


                if ($result->isValid()) {
                    $data = $authAdapter->getResultRowObject(null,'password');
                    $this->authService->getStorage()->write($data);
 					
                    $sessionContainer	= new Container('base');
                    
                    $redirectUrl = $sessionContainer->offsetExists('lastRequest') ?
                    	$sessionContainer->offsetGet('lastRequest') :
                    	'home';
                    
                    return $this->redirect()->toRoute($redirectUrl);
 
                }
 
            }
             
            $this->flashMessenger()->addErrorMessage(
                'Validation failed'
            );
             
 
        }
 
        $viewModel = new ViewModel(
            array(
                'form' => $form,
                'errorMessages' => $this->flashMessenger()->getErrorMessages(),
                'successMessages' => $this->flashMessenger()->getCurrentSuccessMessages(),
            )
        );
         
//         $viewModel->setTerminal(true-); //Remove this if you want your layout to be shown
 
        return $viewModel;
    }
    
}
