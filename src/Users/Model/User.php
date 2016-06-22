<?php
namespace Users\Model;


class User implements UserInterface {



	/**
	 * @var \Users\Model\UserInterface
	 */
	protected $userPrototype;
	
	/**
	 * @var int
	 */
	protected $id;
	
	/**
	 * @var string $username
	 */
	protected $username;
	
	/**
	 * @var string role;
	 */
	protected $role;
	
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId($userId)
	{
		$this->id	= $userId;
	}
	
	public function getUsername()
	{
		return $this->username;
	}
	
	public function setUsername($username)
	{
		$this->username	= $username;
	}
	
	public function getRole()
	{
		return $this->role;
	}
	
	public function setRole($userRole)
	{
		$this->role	= $userRole;
	}
}