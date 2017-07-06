<?php
class UserController extends Controller
{
	protected $content=null;
	protected $errormsg="";
	
	function __construct()
	{
		//$this->init('user');

	}
	
	public function registrate($key=null)
	{
		/* если "зарегистрировать" нажата - проверяем, сохраняем данные */
		if (isset($_POST['goregistrate']))
		{
			if (!$this->checkRegistrationData())
			{
				/* чтобы не заполнять все поля заново - 
				создаем модель, устанавлваем свойства (из _POST),
				создаем представление и выводим сначала сообщения об ошибках, потом - представление */
				$model=new User;
				$model->setDataProperty($_POST);
				$view=new ViewUserRegistrate($model->getData());
				$this->content="<b>Не все данные введены:</b><br>".$this->errormsg."<br><br>".$view->getView();
				return true;
			}
			/* для работы с данными из БД создаем модель,
			проверяем - если логин не занят - шифруем пароль, ключ активации, 
			устанавлваем свойства (из _POST) и сохраняем
			далее - отправляем письмо с сылкой активации */
			$model=new User;
			if ($model->isLoginFree($_POST['username']))
			{
				$_POST['password']=MyApp::hashPassword($_POST['password']);
				$_POST['activate']=MyApp::hashPassword($_POST['username']);
				$model->setDataProperty($_POST);
				$model->save();
			}
			else
			{
				/* логин занят - 
				устанавлваем свойства (из _POST),
				создаем представление и выводим сначала сообщения об ошибках, потом - представление */
				$_POST['username']="";
				$model->setDataProperty($_POST);
				$view=new ViewUserRegistrate($model->getData());
				$this->content="<b>".$model->getErrorMsg()."<br><br>".$view->getView();
				return true;
			}
			/* новый пользователь сохранен в БД
			высылаем письмо с сылкой на активацию */
			$title="Регистрация на ".APP_SITE_PATH;
			$url=APP_SITE_PATH."/user/registrate/?activatekey=".$_POST['activate'];
			$message='<br><br>При регистрации на сайте '.APP_SITE_PATH.' был указан этот электронный адрес. 
				Если адрес указан ошибочно - проигнорируйте это письмо.
				<br><br>Для активации Вашего акаунта <b>'.$model->getUsername().'</b> пройдите по ссылке 
				<a href="'. $url .'">'. $url .'</a><br><br>';
			MyApp::sendMail($_POST['email'], null , $title, $message);
			/* потом пользователь получает письмо, нажимает на ссылку и аккаунт активируется (а надо, чтобы АДМИН!!!) */
			$this->content='<b>Поздравляем!<b>Вы успешно зарегистрировались!
			Пожалуйста, проверьте свою почту, указанную при регистрации и активируйте свой аккаунт!</b>';
			return true;
		}
		else
		{
		/* если не нажато "зарегистрировать" */
			/* нажата ссылка активации */
			if (isset($_GET['activatekey']))
			{
				$is_activate=true;
				$model=new User;
				if ($model->checkActivateKey($_GET['activatekey']))
				{
					if ($model->checkUserNotActivate($_GET['activatekey']))
					{
						if ($model->setUserActivate($_GET['activatekey']))
						{
							$this->content="<b>Ваш аккаунт на ".APP_SITE_PATH." успешно активирован!";
							return true;
						}
					}
					else $is_activate=false;
				}
				else $is_activate=false;
				$this->content=$model->getErrorMsg();
				return true;
			}
			else
			{
				/* ссылка активации не нажималась - выводим форму регистрации */
				$view=new ViewUserRegistrate();
				$this->content=$view->getView();
				return true;
			}
		}
		return;
	}
	
	public function authorize()
	{
		/* если "войти" нажата - проверяем пароль - если правильно считываем данные в сессию */
		if(isset($_POST['enter']))
		{
			if (!$this->checkAuthoriseData())
			{
				$view=new ViewUserAuthorise();
				$this->content="<b>Не все данные введены:</b><br>".$this->errormsg."<br><br>".$view->getView();
				return true;
			}
			$model=new User;
			if ($model->loadDataByLogin($_POST['username']))
			{
				if ($model->checkPassword($_POST['password']))
				{
					if ($model->getStatus()>0)
					{
						/*MyApp::logIn($model->getProfile());*/
						MyApp::logIn($model);
						$this->content="<b>Вы авторизованы</b><br>для дальнейшей работы выберите пункт меню";
						return true;
					}
					else
					{
						$this->content="<b>Ваш аккаунт еще не активирован</b><br>";
						return true;
					}
				}
				else
				{
					$view=new ViewUserAuthorise();
					$this->content="<b>При входе указан неверный пароль</b><br><br>".$view->getView();
					return true;
				}				
			}
			else
			{
				/* Возможно ошибка при вводе логина - выводим форму снова */
				$view=new ViewUserAuthorise();
				$this->content=$model->getErrorMsg()."<br><br>".$view->getView();
				return true;
			}
		}
		else
		{
			/* "не нажато 'войти' - выводим форму авторизации"; */
			$view=new ViewUserAuthorise();
			$this->content=$view->getView();
			return true;
		}
		
	}
	
	public function edit($_id_user)
	{
		if(isset($_POST['save_user']))
		{
			/* если своя запись, или профиль=администратор */
			if (MyApp::isMyAccount($_POST['id']) or (MyApp::checkAccess('user','edit_save')))
			{
				/* проверяем данные*/
				if ($this->checkUpdateData())
				{
					$model=new User;
					$model->setDataProperty($_POST);
					if ($model->save())
					{
						$view=new ViewUser($model->getData());
						$this->content="<b>Данные сохранены</b><br><br>".$view->getView();
					}
					else
					{
						$this->content="Ошибка сохранения данных. Попробуйте повторить действия позже.<br><br>";
					}
					return true;
				}
				else
				{
					$model=new User;
					$model->setId($_id_user);
					if($model->loadDataById())
					{
						$model->setDataProperty($_POST);/* чтобы не вносить все изменения заново */
						$view=new ViewUser($model->getData());
						$this->content="<b>".$this->errormsg."<br><br>".$view->getView();
						return true;
					}
				}
			}
			else
			{
				$this->content="Нет прав на запрашиваемое действие";
				return true;
			}
		}
		$model=new User;
		$model->setId($_id_user);
		if($model->loadDataById())
		{
			$view=new ViewUser($model->getData());
			$this->content=$view->getView();
			return true;
		}
	}
	
	public function activate($_id_user)
	{
		/* если в списке пользователей нажата кнопка "активировать"  - активируем пользователя и сохраняем данные */
		$model=new User;
		$model->setId($_id_user);
		$model->loadDataById();
		if ($model->setUserActivateById($_id_user))
		{
			$this->content="Аккаунт пользователя ".$model->getUsername()." активирован ";
				$this->content=$this->content.MyApp::getNextButton(APP_DIR_PATH."/user/listusers");
			return true;
		}
		$this->content="Не активирован аккаунт для ".$model->getUsername();
			$this->content=$this->content.MyApp::getNextButton(APP_DIR_PATH."/user/listusers");
		return false;
	}
	
	public function send_activate($_id_user)
	{
		/* если в списке пользователей нажата кнопка "выслать ключ"  - сохраняем данные 
			и высылаем выбранному пользователю письмо с сылкой на активацию */
		$model=new User;
		$model->setId($_id_user);
		$model->loadDataById();
		
		$title="Регистрация на ".APP_SITE_PATH;
		$url=APP_SITE_PATH."/user/registrate/?activatekey=".$model->getActivate();
		$message='<br><br>При регистрации на сайте '.APP_SITE_PATH.' был указан этот электронный адрес. 
				Если адрес указан ошибочно - проигнорируйте это письмо.
				<br><br>Для активации Вашего акаунта '.$model->getUsername().' пройдите по ссылке 
				<a href="'. $url .'">'. $url .'</a><br><br>';
			MyApp::sendMail($model->getEmail(), null , $title, $message);
			/* потом пользователь получает письмо, нажимает на ссылку и аккаунт активируется */
			$this->content='<b>Пользователю <b>'.$model->getUsername().'</b> отправлено письмо с кодом активации аккаунта.</b>';
			$this->content=$this->content.MyApp::getNextButton(APP_DIR_PATH."/user/listusers");
			return true;
	}
	
	public function reminde()
	{
		/* если не нажато "выслать" - выводим форму ввода логина/электронной почты */
		if(!isset($_POST['reminde_me']))
		{
			$view=new ViewUserReminde;
			$this->content=$view->getView();
			return true;
		}
		else
		{
		/* если "выслать" нажата - проверяем данные и высылаем письмо с ссылкой сброса пароля */
			if (empty($_POST['username']))
			{
				$view=new ViewUserReminde();
				$this->content="<b>Вы не указали логин.</b><br><br><br>".$view->getView();
				return true;
			}
			$model=new User;
			if (!$model->loadDataByLogin($_POST['username']))
				{
					$view=new ViewUserReminde();
					$this->content="<b>Указанного логина не найдено.</b><br><br><br>".$view->getView();
					/*echo $model->getErrorMsg();*/
					return true;
				}
			
			$_POST['activate']=MyApp::hashPassword($_POST['username']);
			$model->setDataProperty(array("activate"=>$_POST['activate']));
			if (!$model->setUserReActivate())
			{
				$this->content="<b>Ошибка восстановления пароля.</b><br><br><br>";
				return false;
			}
		
			$title="Восстановление пароля на ".APP_SITE_PATH;
			$url=APP_SITE_PATH."/user/newpassword/?activatekey=".$_POST['activate'];
			$message='<br><br>Для смены пароля Вашего акаунта <b>'.$model->getUsername().'</b> на '.APP_SITE_PATH.' пройдите по ссылке <br> 
				<a href="'. $url .'">'. $url .'</a><br>';
			MyApp::sendMail($model->getEmail(), null , $title, $message);
			/* потом пользователь получает письмо, нажимает на ссылку и аккаунт активируется */
			$this->content='Пожалуйста, проверьте свою почту, указанную при регистрации и введите новый пароль к своему аккаунту!</b>';
			return true;
		}
	}
	
	public function newpassword()
	{
		if (isset($_GET['activatekey']))
		{
			$model=new User;
			if (!$model->loadDataByKey($_GET['activatekey']))
			{
				$this->content=$this->errormsg;
				return false;
			}
			
			if(!isset($_POST['save_password']))
			{
				$view=new ViewUserNewpassword($model->getData());
				$this->content=$view->getView();
				return true;
			}
			else
			{
				if (!$this->checkNewPasswordData())
				{
					$view=new ViewUserNewpassword();
					$this->content="<b>Неверный ввод данных:</b><br>".$this->errormsg."<br><br>".$view->getView();
					return true;
				}
				else
				{
					$model->setDataProperty(array('password'=>MyApp::hashPassword($_POST['password']), 'activate'=>''));
					if (!$model->setNewPassword())
					{
						$this->content="<b>Ошибка восстановления пароля.</b><br><br><br>";
						return false;
					}
					else
					{
						$this->content="Новый  пароль для ".$model->getUsername()." сохранен ";
						$this->content=$this->content.MyApp::getNextButton(APP_DIR_PATH."/user/authorize");
						return true;
					}
				}
			}
		}
	}
	
	
	function listusers()
	{
		/* выводим форму список пользователей с кнопками "активировать" */
		$model=new User;
		if ($model->loadList())
		{
			$view=new ViewUserList($model->getDataList());
			$this->content=$view->getView();
			return true;
		}
		
	}
	
	function checkNewPasswordData()
	{
		$allcorrect=true;
				
		if (empty($_POST['password']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле пароля. <br>";
			$allcorrect = false;
		}
		else
		{
			if (strlen($_POST['password']) < 5 || strlen($_POST['password']) > 12)
			{
				$this->errormsg= $this->errormsg. "Пароль не может быть меньше 5 или больше 12 символов, повторите ввод <br>";
				$allcorrect = false;
			}
		}
		
		if (empty($_POST['password2']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле подтверждения пароля. <br>";
			$allcorrect = false;
		}
		else
		{
			if (!empty($_POST['password']) and !($_POST['password']===$_POST['password2'])) /* равен ли пароль его подтверждению */
			{
				$this->errormsg= $this->errormsg. "Пароль и его подтверждение не свопадают, повторите ввод <br>";
				$allcorrect = false;
			}
		}
		
		return $allcorrect;
	}
	
	function checkAuthoriseData()
	{
		$allcorrect=true;
		if (empty($_POST['username']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле логина. <br>";
			$allcorrect = false;
		}
		if (empty($_POST['password']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле пароля. <br>";
			$allcorrect = false;
		}
		return $allcorrect;
	}
	
	function checkUpdateData()
	{
		$allcorrect=true;
		if (empty($_POST['username']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле логина. <br>";
			$allcorrect = false;
		}
		else
		{
			if (strlen($_POST['username']) < 5 ||strlen($_POST['username']) > 20)
			{
				$this->errormsg= $this->errormsg. "Логин не может быть меньше 5 или больше 20 символов, повторите ввод <br>";
				$allcorrect = false;
			}
			else
			{
				if(!preg_match("/^[a-zA-Z0-9]+$/", $_POST['username'])) /* соответствует ли логин регулярному выражению */
				{
					$this->errormsg= $this->errormsg. " Логин пользователя введен некорректно. <br>";
					$allcorrect = false;
				}
			}
		}
		if (empty($_POST['email']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле e-mail. <br>";
			$allcorrect = false;
		}
		else
		{
			if(!preg_match("/^[a-z0-9_.-]+@([a-z0-9]+\.)+[a-z]{2,6}$/i", $_POST['email']))
			{
				$this->errormsg= $this->errormsg. " Адрес электронной почты введен некорректно. <br>";
				$allcorrect = false;
			}
		}
		return $allcorrect; /* если выполнение функции дошло до этого места, возвращаем true */
	}
	
	function checkRegistrationData()
	{
		$allcorrect=true;
		if (empty($_POST['username']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле логина. <br>";
			$allcorrect = false;
		}
		else
		{
			if (strlen($_POST['username']) < 5 ||strlen($_POST['username']) > 20)
			{
				$this->errormsg= $this->errormsg. "Логин не может быть меньше 5 или больше 20 символов, повторите ввод <br>";
				$allcorrect = false;
			}
			else
			{
				if(!preg_match("/^[a-zA-Z0-9]+$/", $_POST['username'])) /* соответствует ли логин регулярному выражению */
				{
					$this->errormsg= $this->errormsg. " Логин пользователя введен некорректно. <br>";
					$allcorrect = false;
				}
			}
		}
		
		if (empty($_POST['password']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле пароля. <br>";
			$allcorrect = false;
		}
		else
		{
			if (strlen($_POST['password']) < 5 || strlen($_POST['password']) > 12)
			{
				$this->errormsg= $this->errormsg. "Пароль не может быть меньше 5 или больше 12 символов, повторите ввод <br>";
				$allcorrect = false;
			}
		}
		
		if (empty($_POST['password2']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле подтверждения пароля. <br>";
			$allcorrect = false;
		}
		else
		{
			if (!empty($_POST['password']) and !($_POST['password']===$_POST['password2'])) /* равен ли пароль его подтверждению */
			{
				$this->errormsg= $this->errormsg. "Пароль и его подтверждение не свопадают, повторите ввод <br>";
				$allcorrect = false;
			}
		}
		
		if (empty($_POST['email']))
		{
			$this->errormsg= $this->errormsg. " Пусто поле e-mail. <br>";
			$allcorrect = false;
		}
		else
		{
			if(!preg_match("/^[a-z0-9_.-]+@([a-z0-9]+\.)+[a-z]{2,6}$/i", $_POST['email']))
			{
				$this->errormsg= $this->errormsg. " Адрес электронной почты введен некорректно. <br>";
				$allcorrect = false;
			}
		}
		return $allcorrect; /* если выполнение функции дошло до этого места, возвращаем true */
	}
}