<?php
namespace Users\Model;


interface UserInterface {
	
	
	public function getId();
	
	public function setId($userId);
	
	public function getUsername();
	
	public function setUsername($username);
	
	public function getRole();
	
	public function setRole($userRole);
}