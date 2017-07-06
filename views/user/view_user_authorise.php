<?php
class ViewUserAuthorise extends View
{
	public function getView()
	{
		ob_start();
		?>
		<style type="text/css">
		.nowrapa{color: #235A81;}
		.nowrapb{background: #CCC; font-weight: bold;}
		.data_row2{color: #235A81;}
		.data_row1{background: #CCC;}
		</style>

		
		<br><br><b>Пользователь</b><br>
		Вход на сайт.<br>
		<br><font size="-1">(введите данные, указанные при регистрации)</font><br><br>
		<form  method="post" />
		<br><br>
			логин: <input class="input" id="login" type="text" maxlength="20" size="30" name="username" 
			value="<?php echo $this->getProperty("username");?>"/> 
		<br><br>
			пароль: <input class="input" id="pass" type="password" maxlength="12" size="20" name="password" />
		<br><br>
			<input class="button" type="submit" value="Войти" name="enter" />
			<br><a href=<?php echo APP_DIR_PATH;?>/user/registrate>регистрация</a>
			<br><a href=<?php echo APP_DIR_PATH;?>/user/reminde>забыли пароль</a>
		</form>
		<?php
		$cont=ob_get_contents();
		ob_end_clean();
		return $cont;
	}
}