<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users\Adapter;

use Zend\Authentication;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Db\Adapter\Adapter;

use Zend\Authentication\Result as AuthenticationResult;
// use Zend\Http\Request as HTTPRequest;
// use Zend\Http\Response as HTTPResponse;
// use Zend\Uri\UriFactory;
// use Zend\Crypt\Utils as CryptUtils;

/**
 * HTTP Authentication Adapter
 *
 * Implements a pretty good chunk of RFC 2617.
 *
 * @todo       Support auth-int
 * @todo       Track nonces, nonce-count, opaque for replay protection and stale support
 * @todo       Support Authentication-Info header
 */
class PasswordHash extends AbstractAdapter implements AdapterInterface
// class PasswordHash implements AdapterInterface
{
	protected $dbAdapter;
	
	protected $tableName;
	
	protected $identityColumn;
	
	protected $credentialColumn;
	
	protected $resultRow;

	/**
	 * __construct() - Sets configuration options
	 *
	 * @param DbAdapter $zendDb
	 * @param string    $tableName           Optional
	 * @param string    $identityColumn      Optional
	 * @param string    $credentialColumn    Optional
	 */
	public function __construct(
								Adapter $zendDb,
								$tableName,
								$identityColumn,
								$credentialColumn
								) {
		$this->dbAdapter		= $zendDb;
		$this->credentialColumn	= $credentialColumn;
		$this->identityColumn	= $identityColumn;
		$this->tableName		= $tableName;
	}
	
	
	public function authenticate() {
		var_dump($this->getCredential());
		$spResponse = 0;
		$prepareStmt = $this->dbAdapter->createStatement();
		$sql	= 'SELECT * FROM '.$this->tableName.' WHERE '.$this->identityColumn.' = :identity';
		
		$prepareStmt->prepare($sql);
		$result = $prepareStmt->execute(array('identity'	=> $this->getIdentity()));
		var_dump(password_verify($this->getCredential(), $result->current()['password']));
		var_dump($result->count());
		
		if($result->count() <> 1)
		{
			return new AuthenticationResult(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND, $this->getIdentity());
		}
		
		$this->resultRow	= $result->current();
		if(password_verify($this->getCredential(), $this->resultRow[$this->credentialColumn]))
		{
			unset($this->resultRow[$this->credentialColumn]);
			return new AuthenticationResult(AuthenticationResult::SUCCESS, $this->getIdentity());
		}
		
		return new AuthenticationResult(AuthenticationResult::FAILURE, $this->getIdentity());
	}
	
	public function getResultRowObject() {
 		return (object) $this->resultRow;
	}
}