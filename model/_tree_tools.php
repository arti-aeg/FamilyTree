<?php

	include_once('model/_person_tools.php');

	function tree_GetRoots($lud_id,$level=0)
	{
		global $CONFIG;
		$result=false;
		if ($lud_id>0)
		{
			$lud=person_get_person($lud_id);
			if ($lud['lud_id']==$lud_id)
			{
				$mw=$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
				$mh=$CONFIG['GLOBAL_PARAMS']['tree_h_margin'];
				for ($n=0;($n<=$level and $n<count($CONFIG['GLOBAL_PARAMS']['tree_w_levels']));$n++) $lw=$CONFIG['GLOBAL_PARAMS']['tree_w_levels'][$n];
				for ($n=0;($n<=$level and $n<count($CONFIG['GLOBAL_PARAMS']['tree_f_levels']));$n++) $fh=$CONFIG['GLOBAL_PARAMS']['tree_f_levels'][$n];
				$lh=round($lw*$CONFIG['GLOBAL_PARAMS']['tree_ratio']);

				$result=array('id' => $lud['lud_id'], 'imiona' => $lud['lud_imiona'], 'nazwisko' => $lud['lud_nazwisko'], 'plec' => $lud['lud_plec'], 'photo' => '', 'X' => 0, 'width' => $lw, 'height' => $lh, 'Y' => 0, 'W' => $lw, 'H' => $lh, 'font' => $fh, 'ojciec' => array(), 'matka' => array());
				$result['photo']=person_portret($lud['lud_id'],$lud['lud_plec'],$lud['lud_rok_ur'],$lud['lud_rok_zg']);
				$result['ojciec']=tree_GetRoots($lud['lud_lud_ojciec'],$level+1);
				$result['matka']=tree_GetRoots($lud['lud_lud_matka'],$level+1);
				$wo=ceil($lw/2);
				$wm=ceil($lw/2);
				$ho=0;
				$hm=0;
				if ($result['ojciec']!==false)
				{
					$wo=$result['ojciec']['W'];
					$ho=$result['ojciec']['H'];
					if ($result['ojciec']['matka']==false)
					{
						$result['ojciec']['X']=-ceil($result['ojciec']['width']/2)-$mw;
					}
					else
					{
						$result['ojciec']['X']=-$result['ojciec']['matka']['W']-$mw;
					}
					$result['ojciec']['Y']=$lh+$mh;
				}
				if ($result['matka']!==false)
				{
					$wm=$result['matka']['W'];
					$hm=$result['matka']['H'];
					if ($result['matka']['ojciec']==false)
					{
						$result['matka']['X']=ceil($result['matka']['width']/2)+$mw;
					}
					else
					{
						$result['matka']['X']=$result['matka']['ojciec']['W']+$mw;
					}
					$result['matka']['Y']=$lh+$mh;
				}
				$result['W']=$wo+$wm+$mw;
				$result['H']=$lh+$mh+$ho;
				if ($hm>$ho) $result['H']=$lh+$mh+$hm;
			}
		}
		return ($result);
	}

	function tree_GetLeaves($lud_id, $level=0)
	{
		global $CONFIG;
		$mw=$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
		$mh=$CONFIG['GLOBAL_PARAMS']['tree_h_margin'];
		for ($n=0;($n<=$level and $n<count($CONFIG['GLOBAL_PARAMS']['tree_w_levels'])-1);$n++) $lw=$CONFIG['GLOBAL_PARAMS']['tree_w_levels'][$n+1];
		for ($n=0;($n<=$level and $n<count($CONFIG['GLOBAL_PARAMS']['tree_f_levels'])-1);$n++) $fh=$CONFIG['GLOBAL_PARAMS']['tree_f_levels'][$n+1];
		$lh=round($lw*$CONFIG['GLOBAL_PARAMS']['tree_ratio']);
		$result=array();
		$x=0;
		person_get_dzieci($lud_id,$lista);
//		$lista=PersonList(array(':lud_id' => $lud_id),'dzieci');
		foreach ($lista as $lx => $lv)
		{
			$new=array('id' => $lv['lud_id'], 'imiona' => $lv['lud_imiona'], 'nazwisko' => $lv['lud_nazwisko'], 'plec' => $lv['lud_plec'], 'photo' => '', 'X' => 0, 'Y' => 0, 'width' => $lw, 'height' => $lh, 'W' => $lw, 'H' => $lh, 'font' => $fh, 'dzieci' => array());
			$new['photo']=person_portret($lv['lud_id'],$lv['lud_plec'],$lv['lud_rok_ur'],$lv['lud_rok_zg']);
			$new['dzieci']=tree_GetLeaves($lv['lud_id'],$level+1);
			$w=0;
			$h=0;
			foreach ($new['dzieci'] as $dx => $dv)
			{
				if ($dv['H']>$h) $h=$dv['H'];
				$w+=$dv['W']+$mw;
			}
			$w-=$mw;
			if ($new['W']<$w) $new['W']=$w;
			if ($h>0) $new['H']=$lh+$h+$mh;
			$new['X']=$x+round($new['W']/2)+$mw;
			$new['Y']=-$new['height']-$mh;
			$x+=$new['W']+$mw;
			$poz=-ceil($w/2);
			foreach ($new['dzieci'] as $dx => $dv)
			{
				$new['dzieci'][$dx]['X']=$poz+ceil($new['dzieci'][$dx]['W']/2);
				$poz+=$new['dzieci'][$dx]['W']+$mw;
			}
			$result[]=$new;
		}
		return ($result);
	}

?>