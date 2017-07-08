<?php
class ContactController extends Controller
{
	protected $content=null;
	protected $errormsg="";
	
	function __construct()
	{
		/* новая строка, после добавления пользователя */
        /* изменений не показывает, т.к. коммит уже прошел, но пуш не сделан */
	}
	
	public function getSimplePage()
	{
		/* $view=new ViewAbout();
		$this->content=$view->getView();
		return true; */
		ob_start();
		?>
		<style type="text/css">
		.align-left{text-align: left;float: left;padding: 3px;}
		.align-right{text-align: right; padding: 3px;}
		.align-center{text-align: center; padding: 3px;}
		
		</style>
		
		<div >
		<div class="align-center">
			<b>контакты</b>
		</div>
			<br>(страница доступна только для зарегистрированных пользователей)
		<div class="align-center" style="width: 550px;">
			контактная информация: <br><br><br>
			<div style="width: 350px;">
			<div class="align-center"><div class="align-left">электронная почта: </div><div width="50px"></div><div class="align-right">alfreerj@gmail.com</div></div>
			<div class="align-center"><div class="align-left">скайп: </div><div width="50px"></div><div class="align-right">alfreerj</div></div>
			<div class="align-center"><div class="align-left">телефон: </div><div width="50px"></div><div class="align-right"> * * * ****** </div></div>
			</div>
		</div>
		</div>
		
		<?php
		$cont=ob_get_contents();
		ob_end_clean();
		return $cont;
	}
}