<?php
class ViewUserReminde extends View
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

		
		<br><br><b>Забыли пароль?</b><br><br>
		- Укажите Ваш логин на <?phpAPP_SITE_PATH?><br>
		- На электронной адрес, указанный Вами при регистрации на этом сайте будет выслано письмо.<br>
		- Перейдите по ссылке в этом письме и введите новый пароль и его подтверждение.<br>
		<br><br>
		<form  method="post" />
		<br><br>
			логин: <input class="input" id="login" type="text" maxlength="20" size="30" name="username" 
			value="<?php echo $this->getProperty("username");?>"/> 
		<br><br>
			
			<input class="button" type="submit" value="напомнить" name="reminde_me" />
		</form>
		<?php
		$cont=ob_get_contents();
		ob_end_clean();
		return $cont;
	}
}