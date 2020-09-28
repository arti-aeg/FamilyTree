<?php
	include_once('model/_person_tools.php');
	
	function gallery_get_gallery($lud_id)
	{
		global $CONFIG;
		$result=$CONFIG['BLANK_GALLERY'];
		if ($lud_id>0)
		{
			$person=person_get_person($lud_id);
			$result['portret']['photo']=$person['portret'];
			$result['portret']['label']=$person['lud_nazwisko'];
			if ($person['lud_imiona']!='') $result['portret']['label']=$person['lud_imiona'].' '.$result['portret']['label'];
			$result['portret']['plec']=$person['lud_plec'];
			$result['portret']['zm']=$person['lud_zmarl'];
			if (file_exists('img/ludzie/gallery_'.$lud_id))
			{
				$result['gallery_path']='img/ludzie/gallery_'.$lud_id.'/';
				$dir=glob('img/ludzie/gallery_'.$lud_id.'/*');
				foreach ($dir as $dx => $dv)
				{
					$pi=pathinfo($dv);
					$result['photos'][]=$pi['basename'];
				}
			}
		}
		return ($result);
	}

	function gallery_set_portrait($id,$data,$wys=0)
	{
		$im=imagecreatefromstring(base64_decode($data));
		if ($im !== false)
		{
			$width=imagesx($im);
			$height=imagesy($im);
			$x=$width;
			$y=$height;
			if ($wys>0 and $height>0)
			{
				$x=round($width*$wys/$height);
				$y=$wys;
				//$im=imagescale($im,$x);
			}
			$tm=@imagecreatetruecolor($x, $y);
			imagecopyresampled($tm, $im, 0, 0, 0, 0, $x, $y, $width, $height);
			imagejpeg($tm,'img/ludzie/photo_'.$id.'.jpg',100);
		}
	}
	
	function gallery_prepare_photo($tmp,$img,$max,$qa)
	{
		if (!isset($tmp) or !file_exists($tmp))
		{
			$wynik='';
			return $wynik;
			exit;
		}
		list($width, $height)=getimagesize($tmp);
		if ($height==0)
		{
			$wynik='';
			return $wynik;
			exit;
		}
		$prop=$width/$height;
		if ($prop>=1)
		{
			$szer=$width;
			if ($szer>$max) $szer=$max;
			$wys=round($szer/$prop);
		}
		else
		{
			$wys=$height;
			if ($wys>$max) $wys=$max;
			$szer=round($wys*$prop);
		}
		$tm=imagecreatefromjpeg($tmp);
		$im=@imagecreatetruecolor($szer, $wys);
		imagecopyresampled($im, $tm, 0, 0, 0, 0, $szer, $wys, $width, $height);
		imagejpeg($im,$img,$qa);
		return $img;
	}
?>