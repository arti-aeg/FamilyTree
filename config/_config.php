<?php
	ini_set("display_errors","1");
	set_error_handler("warning_handler", E_WARNING);
	header("Cache-Control: no-cache");
	global $CONFIG;
	global $SCR_TI;
	global $OLD_SESSION;
	$SCR_TI=microtime(true);
	ini_set('session.gc_maxlifetime', 3600);
	session_set_cookie_params(3600);
	session_start();
	$OLD_SESSION=$_SESSION;
	$CONFIG = array(
		'GLOBAL_PARAMS' => array(								// parametry globalne w mechaniźmie LoadView
			'AppPath' => '',
			'LogPath' => 'log/',
			'AppUrl' => 'http://'.$_SERVER['SERVER_NAME'].'/',
			'service_title' => 'rodzina',
			'default_controler' => 'person',
			'dafault_database' => 'local_rodzina',
			'tree_w_margin' => 10,
			'tree_h_margin' => 40,
			'tree_w_levels' => array(150, 120, 90),
			'tree_f_levels' => array(10, 8, 7),
			'tree_ratio' => 13/8,
			'tree_back_h' => 1000,
			'tree_back_w' => 1000,
		),
		'DATABASES' => array(
			'local_rodzina' => array(
				'dsn' => 'mysql:dbname=rodzina;host=192.168.1.108;port=3306;charset=UTF8',
				'user' => '',
				'passwd' => '',
				'script' => '_DBConnectRodzina.php',
			),
			'fajnenet_rodzina' => array(
				'dsn' => 'mysql:dbname=;host=;port=3306;charset=UTF8',
				'user' => '',
				'passwd' => '',
				'script' => '_DBConnectRodzina.php',
			),
		),
		'CONTROLERS' => array(									// kontrolery
			'login' => array(									// logowanie
				'script' => 'login',
				'login' => false,
				'page_title' =>'Logon',
				'view' => 'v_index',
				'content' => 'v_login',
			),
			'person' => array(									
				'script' =>'person',
				'login' => true,
				'page_title' =>'Osoba',
				'view' => 'v_index',
				'content' => 'v_person',
				'css' => 'person',
			),
			'tree' => array(									
				'script' =>'tree',
				'login' => true,
				'page_title' =>'Drzewo rodzinne',
				'view' => 'v_index',
				'content' => 'v_tree',
				'css' => 'tree',
				'js' => 'tree',
			),
			'gallery' => array(									
				'script' =>'gallery',
				'login' => true,
				'page_title' =>'Galeria',
				'view' => 'v_index',
				'content' => 'v_gallery',
				'css' => array('gallery', 'darkroom'),
				'js' => 'gallery',
			),
		),
		'BLANK_USER' => array(
			'name' => 'adm_nick',
			'mail' => 'adm_mail',
		),
		'BLANK_PERSON' => array(
			'lud_id' => 0,
			'lud_activ' => 0,
			'lud_adm_admin' => 0,
			'lud_data' => 0,
			'lud_adm_mod' => 0,
			'lud_mod' => 0,
			'lud_lud_matka' => 0,
			'lud_lud_ojciec' => 0,
			'lud_plec' => 0,
			'lud_nazwisko' => '',
			'lud_panienskie' => '',
			'lud_imiona' => '',
			'lud_rok_ur' => 0,
			'lud_mie_ur' => 0,
			'lud_dzi_ur' => 0,
			'lud_godz_ur' => '',
			'lud_miejsce_ur' => '',
			'lud_rok_zg' => 0,
			'lud_mie_zg' => 0,
			'lud_dzi_zg' => 0,
			'lud_zmarl' => 0,
			'lud_miejsce_zg' => '',
			'dzieci' => 0,
			'urodzony' => array('short' => '', 'long' => ''),
			'zmarly' => array('short' => '', 'long' => ''),
			'wiek' => '',
			'portret' => '',
		),
		'BLANK_ADMIN' => array(
			'adm_id' => 0,
			'adm_activ' => 0,
			'adm_adm_opiekun' => 0,
			'adm_lud_osoba' => 0,
			'adm_nick' => '',
			'adm_haslo' => '',
			'adm_mail' => '',
		),
		'BLANK_GALLERY' => array(
			'portret' => array(
				'label' => '',
				'photo' => '',
				'plec' => 0,
				'zm' => 0,
			),
			'gallery_path' => '',
			'photos' => array(),
		),
	);

	function warning_handler($errno, $errstr) 
	{ 
	// do something
	}
	
	function ExitError($err)	// funkcja wyjścia z zapisaniem błędu do loga
	{
		global $SCR_TI;
		global $CONFIG;
		$trwalo=microtime(true)-$SCR_TI;
		$server='127.0.0.1';
		if (isset($_SERVER['REMOTE_ADDR'])) $server=$_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$s=explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
			$server=trim($s[0]);
		}
		if (isset($CONFIG['GLOBAL_PARAMS']['LogPath']))
		{
			$log=fopen($CONFIG['GLOBAL_PARAMS']['LogPath'].'err/'.date('Ymd').'.log','a');
			fwrite($log,date('Y-m-d H:i:s')."\t".$server."\t".number_format($trwalo,3)."\t".$err."\n");
			fclose($log);
		}
		exit ($err);
	}
?>