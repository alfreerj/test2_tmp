<?php
class User extends Model
{
	protected $id	= null;
	protected $username = null;
	protected $password = null;
	protected $email = null;
	protected $userprofile = null;
	protected $status = null;
	protected $activate = null;
	
	protected $errormsg = null;
	protected $table = "users";
	protected $dataList = array();
	
	public function __construct()
	{
		$this->table = "users";
	}
	
	public function isLoginFree($_login)
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('SELECT id 
				FROM users
				WHERE username = :username');
			$sqlstmt->bindParam(':username', $_login);
			$sqlstmt->execute();
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		if ($sqlstmt->rowCount()>0)
		{
			$this->errormsg = 'Этот логин ( '.$_login.') уже занят. Попробуйте использовать другой.';
			return false;
		}
		return true;
	}
	
	public function setUserReActivate()
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('UPDATE users
				SET activate="'.$this->activate.'"
				WHERE id = :id');
			$sqlstmt->bindParam(':id', $this->id);
			$sqlstmt->execute();
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		return true;
	}
	
	public function setNewPassword()
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('UPDATE users
				SET password=:password
				, activate=:activate
				WHERE id = :id');
			$sqlstmt->execute(array('password' => $this->password, 'activate' => $this->activate, 'id' => $this->id ));
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		return true;
	}
	
	public function updateItem()
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('UPDATE users
				SET username=:username
				, email=:email
				WHERE id = :id');
			$sqlstmt->execute(array('username' => $this->username, 'email' => $this->email, 'id' => $this->id ));
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		return true;
	}
	
	public function setUserActivateById($_id)
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('UPDATE users
					SET status = 1,
					activate=""
					WHERE id = :id');
			$sqlstmt->bindParam(':id', $this->id);
			$sqlstmt->execute();
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		return true;
	}
	
	public function setUserActivate($_key)
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('UPDATE users
				SET status = 1,
				activate=""
				WHERE activate = :activate');
			$sqlstmt->bindParam(':activate', $_key);
			$sqlstmt->execute();
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		return true;
	}
	
	public function checkUserNotActivate($_key)
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('SELECT id 
				FROM users
				WHERE activate = :activate and status=0');
			$sqlstmt->bindParam(':activate', $_key);
			$sqlstmt->execute();
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		if ($sqlstmt->rowCount()==0)
		{
			$this->errormsg = 'Ваш аккаунт уже активирован';
			return false;
		}
		return true;
	}
	
	public function checkActivateKey($_key)
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('SELECT id, status
				FROM users
				WHERE activate = :activate');
			$sqlstmt->bindParam(':activate', $_key);
			$sqlstmt->execute();
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		if ($sqlstmt->rowCount()==0)
		{
			$this->errormsg = 'Неверный ключ активации';
			return false;
		}
		return true;
	}
	
	public function checkPassword($_password)
	{
		return password_verify($_password, $this->password);
	}
	
	protected function newItem()
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('INSERT INTO users
				(username, password, email, activate)
				VALUES(:username, :password, :email, :activate)');
			
			$sqlstmt->execute(array('username' => $this->username,
									'password' => $this->password,
									'email' => $this->email,
									'activate' => $this->activate ));
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		return true;
	}
	
	public function getData()
	{
		$retData=array(
			'id'=>$this->id,
			'username'=>$this->username, 
			'email'=>$this->email, 
			'userprofile'=>$this->userprofile, 
			'status'=>$this->status,
			'activate'=>$this->activate);
		return $retData;
	}
	
	public function getUserId()
	{
		return $this->id;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function getProfile()
	{
		return $this->userprofile;
	}
	
	public function getUsername()
	{
		return $this->username;
	}
	
	public function getEmail()
	{
		return $this->email;
	}
	
	public function getActivate()
	{
		return $this->activate;
	}
	
	public function getDataList()
	{
		return $this->dataList;
	}

	public function loadDataByLogin($_parameter=null)
	{
		if (!isset($_parameter))
		{
			$this->errormsg="Model class ".get_class($this)." login parametr is not exist";
			return false;
		}
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('SELECT * '.
				' FROM users WHERE username = :username' );
			$sqlstmt->bindParam(':username', $_parameter);
			$sqlstmt->execute();
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		if ($sqlstmt->rowCount()==0)
		{
			$this->errormsg = 'Пользователь с таким логином не найден';
			return false;
		}
		$this->setDataProperty($sqlstmt->fetch(PDO::FETCH_ASSOC));
		return true;
	}
	
	public function loadDataByKey($_parameter=null)
	{
		if (!isset($_parameter))
		{
			$this->errormsg="Model class ".get_class($this)." key parametr is not exist";
			return false;
		}
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare('SELECT * '.
				' FROM users WHERE activate = :activate' );
			$sqlstmt->bindParam(':activate', $_parameter);
			$sqlstmt->execute();
		}
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		if ($sqlstmt->rowCount()==0)
		{
			$this->errormsg = 'Пользователь не найден';
			return false;
		}
		$this->setDataProperty($sqlstmt->fetch(PDO::FETCH_ASSOC));
		return true;
	}
	
	
	function loadList($_where="1")
	{
		$dbo = MyApp::getDBO();
		try
		{
			$sqlstmt = $dbo->prepare("SELECT id, username, email, status, userprofile FROM users WHERE :_where ORDER BY id");
			$sqlstmt->bindParam(':_where', $_where);
			/* когда понадобятся варианты условия - переделать - обработку массива - условие - плейсхолдеры в запрос и бандить */
			$sqlstmt->execute();
		} 
		catch(PDOException $e)
		{  
			$this->errormsg=$e->getMessage();
			return false;
		}
		
		if ($sqlstmt->rowCount()==0)
		{
			$this->errormsg = 'Список пуст';
			return false;
		}
		
		$this->dataList = $sqlstmt->fetchAll(PDO::FETCH_ASSOC);
		return true;
	}
	
	static function getLoginByID($_id_user)
	{
		$db =MyApp::getDBO();
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		try
		{
			$stmt = $db->prepare("SELECT users.login
				FROM users WHERE id=:id");
			$sqlstmt->bindParam(':id', $_id_user);
			$stmt->execute();
		} 
		catch(PDOException $e)
		{  
			$this->errormsg = $e->getMessage();
		}
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data["login"];
	}
}