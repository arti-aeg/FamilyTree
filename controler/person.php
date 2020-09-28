<?php
	include_once('model/_person_tools.php');
	include_once('model/_dat_tools.php');
	global $relations;
	$content=array('file' => $CONTROLER['content'], 'params' => array(), 'views' => array());
	$alerts=array();
	$lud_id=0;
	if (isset($_COOKIE['id'])) $lud_id=$_COOKIE['id'];
	if (isset($_REQUEST['id'])) 
	{
		$lud_id=$_REQUEST['id'];
	}
	if (isset($_REQUEST['link_button']) and $_REQUEST['link_button']=='ZAPISZ')
	{
		$relations=person_get_relations();
		$wyn=person_link($_REQUEST);
		foreach ($wyn as $wx => $wv) $alerts[]=$wv;
//		print_r($_REQUEST);
	}
	if (isset($_REQUEST['edit_button']) and $_REQUEST['edit_button']=='ZAPISZ')
	{
		$relations=person_get_relations();
		print_r($_REQUEST);
		$ver=person_validate($_REQUEST);
		if (count($ver['alerts'])==0)
		{
			if ($ver['oper']=='insert') person_insert($ver);
			if ($ver['oper']=='update') person_update($ver);
		}
		else
		{
			print_r($ver['alerts']);
		}
		print_r($ver);
	}
	$content['params']['person']=person_get_full_person($lud_id,$_SESSION['USER']['adm_id']);
	$lud_id=$content['params']['person']['person']['lud_id'];
	$rel=person_find_rel($_SESSION['USER']['adm_lud_osoba'],$lud_id);
//	print('wynik'."\n");
//	print_r($rel);
	setcookie('id',$lud_id);
	$content['params']['adm_rights']=person_adm_rights($_SESSION['USER'],$content['params']['person']['person']);
//	$content['params']['adm_rights']=0;
	$content['params']['json_miesiace']=dat_miesiace('json');
	$content['params']['json_edit']=person_prepare_edit($lud_id,'json');
//	print_r(person_prepare_edit($lud_id,'array'));
	$content['params']['json_alert']=json_encode($alerts);
//	print_r($content['params']['person']);
	$PAGE['params']['content']=LoadView($content);
?>