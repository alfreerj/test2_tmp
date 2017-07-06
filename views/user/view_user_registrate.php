<?php
class ViewUserRegistrate extends View
{
	public function getView($regstatus=null)
	{
		echo $regstatus;
		ob_start();
		?>
			<style type="text/css">
			.nowrapa{color: #235A81;}
			.nowrapb{background: #CCC; font-weight: bold;}
			.data_row2{color: #235A81;}
			.data_row1{background: #CCC;}
			</style>
		<?php
		
		switch ($regstatus)
		{
			CASE "isregistrate":
				/* зарегистрировались, но еще не активировались */
				?>
					<br><br><b>Поздравляем <?php echo $this->getProperty("username");?>!</b> 
					Вы зарегистрированы в системе.
					<br>
					<br>Теперь Вы можете <a href='".APP_DIR_PATH."/authorize'>войти</a> в систему.";
				<?php break;
			CASE "isactivate":
				echo $regstatus; /* уже активировались */
				 break;
			default:
				echo $regstatus; /* только начали регистрацию */
				?>
					<h3>Регистрация пользователя</h3>
					<form name="regform" method="post" onsubmit="validForm(this);return false;"/>
					<font color="red">*</font><b>обязательно укажите:</b><br>
					
					<br> логин: <input id="login" type="text" maxlength="20" size="30" name="username" 
					value="<?php echo $this->getProperty("username");?>"/> 
					<font color="red">*</font>
					<br><font size="-1">(от 5 до 20 символов, только цифры и латинские буквы)</font><br>
					
					<br> пароль: <input id="pass" type="password" maxlength="12" size="20" name="password" />
					<font color="red">*</font>
					<br><font size="-1">(от 5 до 12 символов)</font><br>
					
					<br> повторите пароль: <input id="pass2" type="password" maxlength="12" size="20" name="password2" />
					<font color="red">*</font>
					<br><font size="-1">(пароли должны совпадать)</font><br>
					
					<br>Email: <input id="mail" type="text" name="email" 
					value="<?php echo $this->getProperty("email");?>"/>
					<font color="red">*</font>
					<br>
					<br>
					<input class="button" type="submit" value="зарегистрировать" name="goregistrate" />
					 
					<a href=<?php echo APP_DIR_PATH;?>/>отмена</a>
								</form>
				<?php break;
		}
		$cont=ob_get_contents();
		ob_end_clean();
		return $cont;
		
		/* ________________________ */
		
		
	}
}