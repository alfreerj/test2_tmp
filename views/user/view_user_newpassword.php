<?php
class ViewUserNewpassword extends View
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
		Введите новый пароль для Вашего аккаунта на сайте.<br><br><br>
		<form  method="post" />
		<br><br>
			логин: <b><?php echo $this->getProperty("username");?></b>
		<br><br>
			пароль: <input class="input" id="pass" type="password" maxlength="12" size="20" name="password" />
		<br><br>
			подтвердите пароль: <input class="input" id="pass" type="password" maxlength="12" size="20" name="password2" />
		<br><br>
			<input class="button" type="submit" value="сохранить" name="save_password" />
		</form>
		<?php
		$cont=ob_get_contents();
		ob_end_clean();
		return $cont;
	}
}