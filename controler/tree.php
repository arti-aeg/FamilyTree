<?php
	include_once('model/_tree_tools.php');
	$content=array('file' => $CONTROLER['content'], 'params' => array(), 'views' => array());
	$lud_id=0;
	if (isset($_COOKIE['id'])) $lud_id=$_COOKIE['id'];
	if (isset($_REQUEST['id'])) 
	{
		$lud_id=$_REQUEST['id'];
	}
	setcookie('id',$lud_id);
	$content['params']['marginW']=$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
	$content['params']['marginH']=$CONFIG['GLOBAL_PARAMS']['tree_h_margin'];
	$roots=tree_GetRoots($lud_id);
	$leaves=tree_GetLeaves($lud_id);
	$rowi=$roots['W'];
	$liwi=0;
	$rohi=$roots['H'];
	$lihi=0;
	$content['params']['width']=0;
	foreach ($leaves as $lx => $lv)
	{
		$liwi+=$lv['W']+$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
		if ($lv['H']>$lihi) $lihi=$lv['H'];
	}
	if ($liwi>0) $liwi-=$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];

	$content['params']['height']=$lihi+$rohi+2*$CONFIG['GLOBAL_PARAMS']['tree_h_margin'];
	$content['params']['rootsY']=$lihi+2*$CONFIG['GLOBAL_PARAMS']['tree_h_margin'];
	$content['params']['leavesY']=$content['params']['rootsY'];
	if ($liwi>=$rowi)
	{
		$content['params']['width']=$liwi+2*$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
		$content['params']['leavesX']=0;
		$content['params']['rootsX']=floor(($liwi-$rowi+$roots['width'])/2)+$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
		if ($roots['ojciec']!=false) $content['params']['rootsX']=floor(($liwi-$rowi)/2)+$roots['ojciec']['W']+$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
	}
	else
	{
		$content['params']['width']=$rowi+2*$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
		$content['params']['leavesX']=floor(($rowi-$liwi)/2)+$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
		$content['params']['rootsX']=floor($roots['width']/2)+$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
		if ($roots['ojciec']!=false) $content['params']['rootsX']=$roots['ojciec']['W']+$CONFIG['GLOBAL_PARAMS']['tree_w_margin'];
	}
	$bgTOP=$lihi+$CONFIG['GLOBAL_PARAMS']['tree_h_margin']+floor($roots['height']/2);
	$bgBOT=$content['params']['height']-$bgTOP;
	if ($bgTOP>=$bgBOT)
	{
		$bgPROP=$CONFIG['GLOBAL_PARAMS']['tree_back_h']/(2*$bgTOP);
		$bgBH=$bgTOP*$bgPROP;
		$bgTH=$bgBOT*$bgPROP;
		$bgH=floor($bgBH+$bgTH);
		$bgY=0;
	}
	else
	{
		$bgPROP=$CONFIG['GLOBAL_PARAMS']['tree_back_h']/(2*$bgBOT);
		$bgTH=$bgTOP*$bgPROP;
		$bgBH=$bgBOT*$bgPROP;
		$bgH=floor($bgBH+$bgTH);
		$bgY=$CONFIG['GLOBAL_PARAMS']['tree_back_h']-$bgH;
	}
	$content['params']['bgY']=$bgY;
	$content['params']['bgH']=$bgH;
	$PAGE['params']['json_edit']=json_encode(array());
	$content['params']['json_roots']=json_encode($roots);
	$content['params']['json_leaves']=json_encode($leaves);
	$content['file']='v_tree';
	$content['params']['back_link']='?controler=person&id='.$lud_id;
	$content['views']=array();
//print_r($content);
	$PAGE['params']['content']=LoadView($content);
?>