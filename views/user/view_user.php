<?php
class ViewUser extends View
{
	public function getView()
	{
		ob_start();
		?>
		<style type="text/css">
		.nowrapa{color: #235A81;}
		.nowrapb{background: #CCC; font-weight: bold;}
		.rdonl{background: #CCC;}
		</style>



		<br>
		Данные пользователя
		<br>

		<form id="catalog-form" name="catalog" method="post" >
			<input name="id" readonly type="hidden" value="<?php echo isset($this->data) ? $this->data["id"] : "";?>" >
			<br>
			имя пользователя (логин) <input name="username" maxlength="20" size="30" value="<?php echo isset($this->data) ? $this->data["username"] : "";?>">
			<br><font size="-1">(от 5 до 20 символов, только цифры и латинские буквы)</font><br>
			<br><br>
			e-mail <input name="email" size="20" value="<?php echo isset($this->data) ? $this->data["email"] : "";?>">
			<br><br>
			профиль <input class="rdonl" name="profile" size="20" readonly 
			<?php if(isset($this->data)and($this->data["userprofile"]==1))
				echo ' value="пользователь"';
				else echo ' value="администратор"';?>">
<!--			isset($this->data)and($this->data>1)-->
			статус <input class="rdonl" name="status_name" size="20" readonly 
			<?php if(isset($this->data)and($this->data["status"]==1))
				echo ' value="активирован"';
				else echo ' value="не активирован"';?>">
			<br><br>
			<br>
			<?php if (MyApp::isMyAccount($this->data["id"]) or (MyApp::checkAccess('user','edit_save')))
			echo '<input type="submit" value="сохранить" name="save_user">';
			?>
			<a href=<?php echo APP_DIR_PATH."/user/listusers";?>/>к списку</a>
		</form>
		<?php
		$cont=ob_get_contents();
		ob_end_clean();
		return $cont;
	}
}