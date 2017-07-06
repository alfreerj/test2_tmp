<?php
class AboutController extends Controller
{
	protected $content=null;
	protected $errormsg="";
	
	function __construct()
	{
		
	}
	
	public function getSimplePage()
	{
		/* $view=new ViewAbout();
		$this->content=$view->getView();
		return true; */
		ob_start();
		?>
		
		<div align="center">
			<b>О проекте</b>
		</div>
			<br>(страница доступна только для зарегистрированных пользователей)
		<div>
			Это тестовый проект, с минимальным функционалом: 
			<ul>
			<li>просмотр страниц свободного доступа</li>
			<li>регистрация на сайте и активация через электронную почту</li>
			<li>восстановление пароля через электронную почту</li>
			<li>активация пользователей (только для администратора)</li>
			</ul>
		</div>
		
		<?php
		$cont=ob_get_contents();
		ob_end_clean();
		return $cont;
	}
}