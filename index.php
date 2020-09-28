<?php
	include ('config/_config.php');
//	print_r($CONFIG);
	if (isset($CONFIG['GLOBAL_PARAMS']['dafault_database']))
	{
		$DATABASE=$CONFIG['DATABASES'][$CONFIG['GLOBAL_PARAMS']['dafault_database']];
		include ('connections/'.$CONFIG['DATABASES'][$CONFIG['GLOBAL_PARAMS']['dafault_database']]['script']);
	}
	chdir($CONFIG['GLOBAL_PARAMS']['AppPath']);
	global $CONTROLER;
	header("Cache-Control: no-cache");
	if (isset($argv))
	foreach ($argv as $ax => $av)
	{
		$a=explode('=',$av);
		if (count($a)==2 and !isset($_REQUEST[trim($a[0])]))
		{
			$_REQUEST[trim($a[0])]=trim($a[1]);
		}
		
	}
	if (isset($_REQUEST['logout']))
	{
		unset($_SESSION['USER']);
	}
	$controler_name='';
	if (isset($_REQUEST['controler'])) $controler_name=strtolower($_REQUEST['controler']);
	if ($controler_name=='' and isset($_SESSION['REQUEST']['controler'])) $controler_name=$_SESSION['REQUEST']['controler'];
	if ($controler_name=='' and isset($CONFIG['GLOBAL_PARAMS']['default_controler'])) $controler_name=$CONFIG['GLOBAL_PARAMS']['default_controler'];
	if (!isset($CONFIG)) ExitError ('Brak konfiguracji'."\n");
	if (!isset($CONFIG['CONTROLERS'][$controler_name])) ExitError ('Nieznany kontroler'."\n");
	$CONTROLER=$CONFIG['CONTROLERS'][$controler_name];
	$CONTROLER['name']=$controler_name;
	if (!isset($CONTROLER['script'])) $CONTROLER['script']=$controler_name;
	if (isset($CONTROLER['view']) and !isset($CONTROLER['content'])) $CONTROLER['content']='v_'.$controler_name;
	if (!file_exists($CONFIG['GLOBAL_PARAMS']['AppPath'].'controler/'.$CONTROLER['script'].'.php')) ExitError ('Brak pliku kontrolera'."\n");
	if ($CONTROLER['login'] and !isset($_SESSION['USER']))
	{
		include('config/_auth.php');
		if (isset($_REQUEST['user_name']) and isset($_REQUEST['user_passwd']))
		{
			$user=Login($_REQUEST['user_name'],$_REQUEST['user_passwd']);
			if ($user==='FALSE')
			{
			}
			else
			{
				$_SESSION['USER']=$user;
				if (isset($_SESSION['REQUEST']))
				{
					$_REQUEST=$_SESSION['REQUEST'];
					unset ($_SESSION['REQUEST']);
				}
			}
		}
		if (!isset($_SESSION['USER']))
		{
			if (!isset($_SESSION['REQUEST'])) $_SESSION['REQUEST']=$_REQUEST; 
			if (isset($CONFIG['CONTROLERS']['login']['view']))
			{
//				$CONTROLER=$CONFIG['CONTROLERS']['login'];
				include_once ('config/_view.php');
			}
			include ($CONFIG['GLOBAL_PARAMS']['AppPath'].'controler/'.$CONFIG['CONTROLERS']['login']['script'].'.php');
		}
	}
	if (!$CONTROLER['login'] or isset($_SESSION['USER']))
	{
		if (isset($CONTROLER['view']))
		{
			include_once ('config/_view.php');
		}
		include ($CONFIG['GLOBAL_PARAMS']['AppPath'].'controler/'.$CONTROLER['script'].'.php');
	}
	if (isset($PAGE)) // a $PAGE to się bierze z _view_tools.php głąbie
	{
//		print_r($PAGE);
		$string=LoadView($PAGE);
		print($string);
	}
?>