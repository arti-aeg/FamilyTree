<?php
	include_once('model/_gallery_tools.php');
	$content=array('file' => $CONTROLER['content'], 'params' => array(), 'views' => array());
	$content['params']['edit']='';
	$lud_id=0;
	if (isset($_COOKIE['id'])) $lud_id=$_COOKIE['id'];
	if (isset($_REQUEST['id'])) 
	{
		$lud_id=$_REQUEST['id'];
	}
	
	if (isset($_REQUEST['edit']))
	{
		$content['params']['edit']=$_REQUEST['edit'];
	}

	if (isset($_REQUEST['button_add']) and $_REQUEST['button_add']=='DODAJ' and file_exists($_FILES['new_photo']['tmp_name']))
	{
		if (!file_exists('img/ludzie/gallery_'.$lud_id))
		{
			mkdir('img/ludzie/gallery_'.$lud_id);
			if (file_exists('img/ludzie/photo_'.$lud_id.'.jpg'))
			{
			  copy('img/ludzie/photo_'.$lud_id.'.jpg','img/ludzie/gallery_'.$lud_id.'/photo_'.$lud_id.'.jpg');
			}
		}
		$dest_file='img/ludzie/gallery_'.$lud_id.'/'.$_FILES['new_photo']['name'];
		while (file_exists($dest_file))
		{
			$pi=pathinfo($dest_file);
			$i=1;
			$dest_file='img/ludzie/gallery_'.$lud_id.'/'.$pi['filename'].'_'.$i.'.'.$pi['extension'];
			$i++;
		}
		gallery_prepare_photo($_FILES['new_photo']['tmp_name'],$dest_file,800,100);
	}

    if (isset($_REQUEST['portret_button']) and $_REQUEST['portret_button']=='ZAPISZ')
    {
//		$lud=PersonAr($lud_id);
		$adm_rights=1;
		if ($adm_rights>0)
		{
			$pos=strpos($_REQUEST['photo'],'base64');
			if (!($pos===FALSE))
			{
				$img=substr($_REQUEST['photo'],$pos+7);
				gallery_set_portrait($lud_id,$img,400);
			}
		}
		else
		{
			$ALERTS[]='Nie masz wystarczających praw, by zmienić zdjęcie portretowe.';
		}
    }
	$content['params']['lud_id']=$lud_id;
	$content['params']['gallery']=gallery_get_gallery($lud_id);
	$content['file']='v_gallery';
	$content['params']['back_link']='?controler=person&id='.$lud_id;
	$content['views']=array();
//print_r($content);
	$PAGE['params']['content']=LoadView($content);
?>
