<?php
/* устанавливаем флаг, что вошли через главный файл */
define( '_APPGO', 1 );

/* git test */
/* change 1 */

/* ******* begin settings ******* */
/* определяем константы, настройки и т.п. - можно перенести в отдельный файл - setting.php */
/* постоянная - имя пользвоателя - define('USER_NAME',?) - определяется после открытия сессии в MyApp::sessionStart() */

	define('APP_DIR_PATH', 	'/test2' ); /* относительный, от доменного имени до каталога сайта */

	const ACCESS_GUEST = 0;
	const ACCESS_USER = 1;
	const ACCESS_ADMIN = 10;

	define( 'DS', DIRECTORY_SEPARATOR );
	define('DEBUG_DB_MODE',true);
	define('MAIL_ADMIN',"admin@mydomen.com");

	define('APP_PATH_ROOT', dirname(__FILE__) ); /* определяем основную дирректорию */
	define('APP_PATH_SYSTEM', APP_PATH_ROOT.DS.'system' );
	define('APP_PATH_BASE', APP_PATH_ROOT.DS.'base' );
	define('APP_PATH_IMAGE', APP_PATH_ROOT.DS.'images' );

	define('APP_HOST_NAME', 	$_SERVER['HTTP_HOST']);
	define('APP_SITE_PATH', 	'http://'.APP_HOST_NAME.APP_DIR_PATH ); // сайт - адрес сайта - доменное имя 

/* определяем установки отображения вывода */
	ini_set( "display_errors", true );
	date_default_timezone_set('Europe/Moscow');
	setlocale(LC_TIME, "ru_RU.utf8");

/* ******* end settings ******* */

	require_once ( APP_PATH_SYSTEM .DS.'MyApp.php' ); /* подключаем определение класса - приложение */
/* создаем объект-Приложение, запускаем маршрутизатор */
	$oApplication = MyApp::getInstance(APP_PATH_SYSTEM.DS.'config.php', APP_PATH_SYSTEM.DS.'classmap.php', APP_PATH_SYSTEM.DS.'access.php');
	if (is_object($oApplication))
	{
		$retRoute=$oApplication->route(); /* false или контент */
		$sitecontent = $oApplication->CheckError()? $oApplication->getError() : $retRoute;
		define('SITE_NAME', MyApp::getConfig("sitename"));
	}
	else
	{
		$sitecontent="Ошибка содания приложения";
	}

/* некоторые константы, определенные в MyApp, в случае ошибки выполнения могут не инициализироваться 
	проверим их и, если они не определены - назначим умолчания */
	defined('USER_NAME') or define( 'USER_NAME', "гость");
	defined('SITE_NAME') or define('SITE_NAME', 'Тестовый сайт "Пользователи"');
	
 /* подключаем контент шапки */
	ob_start();
	require_once ( APP_PATH_BASE .DS.'header.php' );
	$header_content=ob_get_contents();
	ob_end_clean();


/* подключаем контент подвала */
	ob_start();
	require_once ( APP_PATH_BASE .DS.'footer.php' );
	$footer_content=ob_get_contents();
	ob_end_clean();

/* подключаем контент меню */
	ob_start();
	require_once ( APP_PATH_BASE .DS.'menu.php' );
	$menu_content=ob_get_contents();
	ob_end_clean();
	
	/* подключаем файл вывода информации */
	require_once ( APP_PATH_BASE .DS.'layout.php' );
exit;

