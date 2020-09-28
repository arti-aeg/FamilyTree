<?php
	
	$PAGE=array('file' => $CONTROLER['view'], 'params' => array(), 'views' => array());
	global $PAGE;
	global $OLD_SESSION;
	$PAGE['params']['page_title']='';
	if (isset($CONTROLER['page_title'])) $PAGE['params']['page_title']=$CONTROLER['page_title'];
	if (isset($CONFIG['BLANK_USER']));
	{
		foreach ($CONFIG['BLANK_USER'] as $ux => $uv)
		{
			$PAGE['params']['user'][$ux]='';
			if (isset($_SESSION['USER'][$uv]))
			{
				$PAGE['params']['user'][$ux]=$_SESSION['USER'][$uv];
			}
		}
	}
	$PAGE['params']['menu']='';
	$PAGE['params']['content']='';
	$PAGE['params']['last_visit']='nigdy';
	$PAGE['params']['last_session']='nigdy';
	$PAGE['params']['old_session']=$OLD_SESSION;
	if (isset($_COOKIE['last_visit'])) $PAGE['params']['last_visit']=$_COOKIE['last_visit'];
	if (isset($_SESSION['last_session'])) $PAGE['params']['last_session']=$_SESSION['last_session'];
	setcookie('last_visit',date('Y-m-d H:i:s'));
	$_SESSION['last_session']=date('Y-m-d H:i:s');
	if (isset($CONTROLER['menu']))
	{
		$menu=array('file' => $CONTROLER['menu'], 'params' => array(), 'views' => array());
		$menu['params']['buttons']=array();
		foreach ($CONFIG['CONTROLERS'] as $cx => $cv)
		{
			if (isset($cv['menu']) and $cv['menu'])
			{
				$menu['params']['buttons'][$cx]['title']=$cv['page_title'];
				if ($cx==$CONTROLER['name'])
				{
					$menu['params']['buttons'][$cx]['link']='';
				}
				else
				{
					$menu['params']['buttons'][$cx]['link']='?controler='.$cx;
				}
			}
		}
		$PAGE['params']['menu']=LoadView($menu);
	}
	if (isset($CONTROLER['css'])) $PAGE['params']['content_css']=$CONTROLER['css'];
	if (isset($CONTROLER['js'])) $PAGE['params']['content_js']=$CONTROLER['js'];
	
	function LoadView($view)
	{
		global $CONFIG;
		$result='';
		if (isset($view['views']))
		{
			foreach ($view['views'] as $vx => $vv)
			{
				$view['params'][$vx]=LoadView($vv);
			}
		}
		if (file_exists($CONFIG['GLOBAL_PARAMS']['AppPath'].'view/'.$view['file'].'.html'))
		{
			extract($view['params']);
			extract($CONFIG['GLOBAL_PARAMS']);
			ob_start();
			include($CONFIG['GLOBAL_PARAMS']['AppPath'].'view/'.$view['file'].'.html');
			$result = ob_get_contents();
			ob_end_clean();
		}
		return ($result);
	}
	
	function Pagination($params)
	{
		global $CONFIG;
		$result='';
		if ($params['max']>=$params['current'])
		{
			$np=$CONFIG['pagination_limit'];
			$fp=$params['current']-floor($np/2);
			if ($fp<1) $fp=1;
			$lp=$fp+$np-1;
			if ($lp>$params['max']) $lp=$params['max'];
			$params['pages']=array();
			for ($p=$fp;$p<=$lp;$p++)
			{
				$pp['href']=$params['href'].'page='.$p;
				$pp['caption']=$p;
				$params['pages'][]=$pp;
			}
			$params['fp_href']=$params['href'].'page=1';
			$params['lp_href']=$params['href'].'page='.$params['max'];

			$params['pp_href']=$params['href'].'page='.($params['current']-1);
			if ($params['current']<=1) $params['pp_href']=$params['href'].'page=1';

			$params['np_href']=$params['href'].'page='.($params['current']+1);
			if ($params['current']>=$params['max']) $params['np_href']=$params['href'].'page='.$params['max'];

			$result=LoadView(array('file' => 'v_pages', 'params' => $params, 'views' => array()));
		}
		return ($result);
	}

?>