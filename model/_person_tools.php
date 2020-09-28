<?php
	include_once('model/_dat_tools.php');

	function person_get_full_person($lud_id,$adm_id=0)
	{
		global $CONFIG;
		global $db_main;
		$result = array(
			'person' => $CONFIG['BLANK_PERSON'],
			'matka' => $CONFIG['BLANK_PERSON'],
			'ojciec' => $CONFIG['BLANK_PERSON'],
			'dzieci' => array(),
			'rodzenstwo' =>  array(),
			'admin' => $CONFIG['BLANK_ADMIN'],
			'admini' => array()
		);
		$result['person'] = person_get_person($lud_id,$adm_id);
		if ($result['person']['lud_id']>0)
		{
			$result['matka'] = person_get_person($result['person']['lud_lud_matka']);
			$result['ojciec'] = person_get_person($result['person']['lud_lud_ojciec']);
			person_get_dzieci($result['person']['lud_id'],$result['dzieci']);
			person_get_dzieci($result['person']['lud_lud_matka'],$result['rodzenstwo']);
			person_get_dzieci($result['person']['lud_lud_ojciec'],$result['rodzenstwo']);
			unset($result['rodzenstwo'][$result['person']['lud_id']]);
			if ($result['person']['lud_adm_admin']>0)
			{
				$admini = person_adm_list($result['person']['lud_adm_admin']);
				$result['admin'] = $admini['admin'];
				$result['admini'] = $admini['lista'];
			}
		}
		return $result;
	}
	
	function person_get_person($lud_id,$adm_id=0)
	{
		global $CONFIG;
		global $db_main;
		$result = $CONFIG['BLANK_PERSON'];
		$id=$lud_id;
		if ($id>0)
		{
			$sql_per = 'SELECT lud.*,(SELECT COUNT(*) FROM lud_ludzie WHERE lud_activ=1 AND (lud_lud_matka=lud.lud_id OR lud_lud_ojciec=lud.lud_id)) dzieci FROM lud_ludzie lud WHERE lud.lud_activ=1 AND lud.lud_id=:id';
		}
		else
		{
			$id=$adm_id;
			$sql_per = 'SELECT lud.*,(SELECT COUNT(*) FROM lud_ludzie WHERE lud_activ=1 AND (lud_lud_matka=lud.lud_id OR lud_lud_ojciec=lud.lud_id)) dzieci FROM lud_ludzie lud WHERE lud.lud_activ=1 AND lud.lud_id=(SELECT adm_lud_osoba FROM adm_admini WHERE adm_id=:id)';
		}
		$result['portret']='img/ludzie/brak.jpg';
		if ($id>0)
		{
			$pre_per=$db_main->prepare($sql_per);
			$pre_per->execute(array('id' => $id));
			if ($res_per=$pre_per->fetch(PDO::FETCH_ASSOC))
			{
				foreach ($result as $rx => $rv)
				{
					if (isset($res_per[$rx])) $result[$rx]=$res_per[$rx];
				}
			}
			$result['urodzony']=dat_PrepareDate($result['lud_rok_ur'],$result['lud_mie_ur'],$result['lud_dzi_ur']);
			$result['zmarly']=dat_PrepareDate($result['lud_rok_zg'],$result['lud_mie_zg'],$result['lud_dzi_zg']);
			$result['wiek']=dat_Wiek($result['lud_rok_ur'],$result['lud_mie_ur'],$result['lud_dzi_ur'],$result['lud_rok_zg'],$result['lud_mie_zg'],$result['lud_dzi_zg'],$result['lud_zmarl']);
			$result['portret']=person_portret($result['lud_id'],$result['lud_plec'],$result['lud_rok_ur'],$result['lud_rok_zg']);
		}
		return $result;
	}
	
	function person_get_dzieci($id,&$result)
	{
		global $CONFIG;
		global $db_main;
		if ($id>0)
		{
			$sql_per = 'SELECT * FROM lud_ludzie WHERE lud_activ=1 AND (lud_lud_matka=:id OR lud_lud_ojciec=:id)';
			$pre_per=$db_main->prepare($sql_per);
			$pre_per->execute(array('id' => $id));
			while ($res_per=$pre_per->fetch(PDO::FETCH_ASSOC))
			{
				$res_per['urodzony']=dat_PrepareDate($res_per['lud_rok_ur'],$res_per['lud_mie_ur'],$res_per['lud_dzi_ur']);
				$res_per['zmarly']=dat_PrepareDate($res_per['lud_rok_zg'],$res_per['lud_mie_zg'],$res_per['lud_dzi_zg']);
				$res_per['wiek']=dat_Wiek($res_per['lud_rok_ur'],$res_per['lud_mie_ur'],$res_per['lud_dzi_ur'],$res_per['lud_rok_zg'],$res_per['lud_mie_zg'],$res_per['lud_dzi_zg'],$res_per['lud_zmarl']);
				$res_per['portret']=person_portret($res_per['lud_id'],$res_per['lud_plec'],$res_per['lud_rok_ur'],$res_per['lud_rok_zg']);
				$result[$res_per['lud_id']]=$CONFIG['BLANK_PERSON'];
				foreach ($CONFIG['BLANK_PERSON'] as $px => $pv)
				{
					if (isset($res_per[$px])) $result[$res_per['lud_id']][$px]=$res_per[$px];
				}
			}
		}
	}
	
	function person_get_admin($adm)
	{
		global $CONFIG;
		global $db_main;
		$result = $CONFIG['BLANK_ADMIN'];
		if ($adm>0)
		{
			$sql_adm = 'SELECT * FROM adm_admini WHERE adm_activ=1 AND adm_id=:id';
			$pre_adm=$db_main->prepare($sql_adm);
			$pre_adm->execute(array('id' => $adm));
			if ($res_adm=$pre_adm->fetch(PDO::FETCH_ASSOC))
			{
				$result=$res_adm;
			}
		}
		return $result;
	}
	
	function person_adm_list($adm)
	{
		global $CONFIG;
		global $db_main;
		$result = array('admin' => array(), 'lista' => array());
		while ($adm>0)
		{
			$admin=person_get_admin($adm);
			if (count($result['admin'])==0) $result['admin']=$admin;
			if (isset($result[$admin['adm_id']]))
			{
				$adm=0;
			}
			else
			{
				$adm=$admin['adm_adm_opiekun'];
				$result['lista'][$admin['adm_id']] = $admin['adm_adm_opiekun'];
			}
		}
		return $result;
	}
	
	function person_portret($lud_id,$lud_plec,$lud_rok_ur,$lud_rok_zg)
	{
		$result='img/ludzie/photo_'.$lud_id.'.jpg';
		if (!file_exists($result))
		{
			$rok_do=date('Y');
			if ($lud_rok_zg>0) $rok_do=$lud_rok_zg;
			$wiek=30;
			if ($lud_rok_ur>0) $wiek=$rok_do-$lud_rok_ur;
			$result='img/ludzie/dziecko_'.$lud_plec.'.jpg';
			if ($wiek>10) $result='img/ludzie/mlodziez_'.$lud_plec.'.jpg';
			if ($wiek>20)
			{
				mt_srand();
				$rnd=mt_rand(1,5);
				$result='img/ludzie/'.$rnd.'postac_'.$lud_plec.'.jpg';
			}
			if ($wiek>70) $result='img/ludzie/starszy_'.$lud_plec.'.jpg';
		}
		if (!file_exists($result))
		{
			$result='img/ludzie/brak.jpg';
		}
		return ($result);
	}

	function person_prepare_edit($lud_id,$fmt='array')
	{
		$edit=array(
			'lud_id' => 0,
			'lud_activ' => 1,
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
			'min_rok_ur' => 0,
			'max_rok_ur' => 0,
			'min_rok_zg' => 0,
			'tytul' => '',
			'link' => 0,
			'_display_rodzice' => '',
			'_display_plec' => '',
			'prop_osoba' => array(),
			'prop_ojciec' => array(),
			'prop_matka' => array(),
			'prop_dzieci' => array()
		);
		$result=array('osoba' => $edit, 'ojciec' => $edit, 'matka' => $edit, 'rodzenstwo' => $edit, 'potomstwo' => $edit);
		$osoba=person_get_person($lud_id);
		$prop_matka=person_person_list(array(':lud_lud_matka' => $osoba['lud_lud_matka'], ':lud_lud_ojciec' => $osoba['lud_lud_ojciec']),'prop_matka');
		$prop_ojciec=person_person_list(array(':lud_lud_matka' => $osoba['lud_lud_matka'], ':lud_lud_ojciec' => $osoba['lud_lud_ojciec']),'prop_ojciec');
		$prop_dzieci=person_person_list(array(':lud_id' => $osoba['lud_id']),'prop_dzieci');
		$prop_mezowie=person_person_list(array(':lud_id' => $osoba['lud_id']),'prop_mezowie');
		$prop_zony=person_person_list(array(':lud_id' => $osoba['lud_id']),'prop_zony');
		$prop_rodzenstwo=person_person_list(array(':lud_id' => $osoba['lud_id'], ':lud_lud_matka' => $osoba['lud_lud_matka'], ':lud_lud_ojciec' => $osoba['lud_lud_ojciec']),'rodzenstwo');

		$result['osoba']['tytul']=$osoba['lud_imiona'].' '.$osoba['lud_nazwisko'].' - edycja';
		$result['ojciec']['tytul']=$osoba['lud_imiona'].' '.$osoba['lud_nazwisko'].' - dodanie ojca';
		$result['matka']['tytul']=$osoba['lud_imiona'].' '.$osoba['lud_nazwisko'].' - dodanie matki';
		$result['rodzenstwo']['tytul']=$osoba['lud_imiona'].' '.$osoba['lud_nazwisko'].' - dodanie rodzeństwa';
		$result['potomstwo']['tytul']=$osoba['lud_imiona'].' '.$osoba['lud_nazwisko'].' - dodanie dziecka';

		$result['ojciec']['lud_plec']=0;
		$result['ojciec']['_display_plec']='none';

		$result['matka']['lud_plec']=1;
		$result['matka']['_display_plec']='none';

		foreach ($edit as $ex => $ev)
		{
			if (isset($osoba[$ex])) $result['osoba'][$ex]=$osoba[$ex];
		}

		$np['id']='';
		$np['sel']=0;
		if ($osoba['lud_lud_matka']==$np['id']) $np['sel']=1;
		$np['label']='nowa osoba';
		$result['matka']['prop_osoba'][]=$np;
		$np['sel']=0;
		if ($osoba['lud_lud_ojciec']==$np['id']) $np['sel']=1;
		$result['ojciec']['prop_osoba'][]=$np;

		if (count($prop_matka)>0)
		{
			$np['id']=0;
			$np['sel']=0;
			if ($osoba['lud_lud_matka']==$np['id']) $np['sel']=1;
			$np['label']='matką jest inna osoba';
			$result['osoba']['prop_matka'][]=$np;
			if ($osoba['lud_lud_ojciec']>0) $result['rodzenstwo']['prop_matka'][]=$np;
		}

		foreach ($prop_matka as $px => $pv)
		{
			$np['id']=$pv['lud_id'];
			$np['sel']=0;
			if ($osoba['lud_lud_matka']==$np['id']) $np['sel']=1;
			$np['label']=$pv['lud_imiona'].' '.$pv['lud_nazwisko'];
			$result['osoba']['prop_matka'][]=$np;
			$result['rodzenstwo']['prop_matka'][]=$np;
			$result['matka']['prop_osoba'][]=$np;
		}

		if (count($prop_ojciec)>0)
		{
			$np['id']=0;
			if ($osoba['lud_lud_ojciec']==$np['id']) $np['sel']=1;
			$np['sel']=0;
			$np['label']='ojcem jest inna osoba';
			$result['osoba']['prop_ojciec'][]=$np;
			if ($osoba['lud_lud_matka']>0) $result['rodzenstwo']['prop_ojciec'][]=$np;
		}

		foreach ($prop_ojciec as $px => $pv)
		{
			$np['id']=$pv['lud_id'];
			$np['sel']=0;
			if ($osoba['lud_lud_ojciec']==$np['id']) $np['sel']=1;
			$np['label']=$pv['lud_imiona'].' '.$pv['lud_nazwisko'];
			$result['osoba']['prop_ojciec'][]=$np;
			$result['rodzenstwo']['prop_ojciec'][]=$np;
			$result['ojciec']['prop_osoba'][]=$np;
		}

		foreach ($prop_dzieci as $px => $pv)
		{
			$np['id']=$pv['lud_id'];
			$np['sel']=0;
			if ($pv['lud_lud_ojciec']==$osoba['lud_id'] or $pv['lud_lud_matka']==$osoba['lud_id']) $np['sel']=1;
			$np['label']=$pv['lud_imiona'].' '.$pv['lud_nazwisko'];
			$result['osoba']['prop_dzieci'][]=$np;
		}

		foreach ($prop_rodzenstwo as $px => $pv)
		{
			$np['id']=$pv['lud_id'];
			$np['sel']=0;
			if ($osoba['lud_lud_ojciec']==$np['id']) $np['sel']=1;
			$np['label']=$pv['lud_imiona'].' '.$pv['lud_nazwisko'];
			if ($pv['lud_lud_ojciec']==0) $result['ojciec']['prop_dzieci'][]=$np;
			$np['sel']=0;
			if ($osoba['lud_lud_matka']==$np['id']) $np['sel']=1;
			if ($pv['lud_lud_matka']==0) $result['matka']['prop_dzieci'][]=$np;
		}

		if ($osoba['lud_rok_ur']>0)
		{
			$result['ojciec']['max_rok_ur']=$osoba['lud_rok_ur']-13;
			$result['matka']['max_rok_ur']=$osoba['lud_rok_ur']-13;
			$result['ojciec']['min_rok_zg']=$osoba['lud_rok_ur']-1;
			$result['matka']['min_rok_zg']=$osoba['lud_rok_ur'];
			$result['potomstwo']['min_rok_ur']=$osoba['lud_rok_ur']+13;
		}

		$result['potomstwo']['lud_nazwisko']=$osoba['lud_nazwisko'];
		$result['potomstwo']['lud_panienskie']=$osoba['lud_nazwisko'];

		if ($osoba['lud_plec']==0)
		{
			$np['id']=$osoba['lud_id'];
			$np['sel']=1;
			$np['label']=$osoba['lud_imiona'].' '.$osoba['lud_nazwisko'];
			$result['potomstwo']['prop_ojciec'][]=$np;
			if (count($prop_zony)>0)
			{
				$np['id']=0;
				$np['sel']=0;
				$np['label']='matką jest inna osoba';
				$result['potomstwo']['prop_matka'][]=$np;
			}
			foreach ($prop_zony as $px => $pv)
			{
				$np['id']=$pv['lud_id'];
				$np['sel']=0;
				if (count($prop_zony)==1) $np['sel']=1;
				$np['label']=$pv['lud_imiona'].' '.$pv['lud_nazwisko'];
				$result['potomstwo']['prop_matka'][]=$np;
			}
			$result['ojciec']['lud_nazwisko']=$osoba['lud_nazwisko'];
			$result['matka']['lud_nazwisko']=$osoba['lud_nazwisko'];
			$result['rodzenstwo']['lud_nazwisko']=$osoba['lud_nazwisko'];
			if ($osoba['lud_rok_zg']>0)
			{
				$result['potomstwo']['max_rok_ur']=$osoba['lud_rok_zg']+1;
			}
		}
		else
		{
			if (count($prop_mezowie)>0)
			{
				$np['id']=0;
				$np['sel']=0;
				$np['label']='ojcem jest inna osoba';
				$result['potomstwo']['prop_ojciec'][]=$np;
			}
			foreach ($prop_mezowie as $px => $pv)
			{
				$np['id']=$pv['lud_id'];
				$np['sel']=0;
				if (count($prop_mezowie)==1) $np['sel']=1;
				$np['label']=$pv['lud_imiona'].' '.$pv['lud_nazwisko'];
				$result['potomstwo']['prop_ojciec'][]=$np;
			}
			$np['id']=$osoba['lud_id'];
			$np['sel']=1;
			$np['label']=$osoba['lud_imiona'].' '.$osoba['lud_nazwisko'];
			$result['potomstwo']['prop_matka'][]=$np;
			$result['ojciec']['lud_nazwisko']=$osoba['lud_panienskie'];
			$result['matka']['lud_nazwisko']=$osoba['lud_panienskie'];
			$result['rodzenstwo']['lud_nazwisko']=$osoba['lud_panienskie'];
			if ($osoba['lud_rok_zg']>0)
			{
				$result['potomstwo']['max_rok_ur']=$osoba['lud_rok_zg'];
			}
		}

		foreach ($result as $rx => $rv)
		{
			if (count($rv['prop_matka'])==0 and count($rv['prop_ojciec'])==0) $result[$rx]['_display_rodzice']='none';
			if (count($rv['prop_dzieci'])>0) $result[$rx]['_display_plec']='none';
			$result[$rx]['link']=$osoba['lud_id'];
		}

	//    print_r($result);
		
		if ($fmt=='json') $result=json_encode($result);
		return ($result);
	}

	function person_person_list($params,$sql)
	{
		global $db_main;
		$result=array();
		$qr_person=file_get_contents('sql/'.$sql.'.sql');
		$pre_find=$db_main->prepare($qr_person);
		$pre_find->execute($params);
		$result=$pre_find->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $rx => $rv)
		{
			$result[$rx]=person_get_person($rv['lud_id']);
		}
		return ($result);
	}

	function person_adm_rights($adm,$lud)
	{
		global $db_main;
		$result=0;
		if ($adm['adm_id']==0 or $adm['adm_lud_osoba']==$lud['lud_id'] or $adm['adm_id']==$lud['lud_adm_admin'])
		{
			$result=1;
		}
		else
		{
			$adm_list=array(0 => 1);
			$qr_adm=file_get_contents('sql/adm_rights.sql');
			$pre_find=$db_main->prepare($qr_adm);
			$pre_find->execute(array(':adm_id' => $lud['lud_adm_admin']));
			$re_adm=$pre_find->fetch(PDO::FETCH_ASSOC);
			while (!isset($adm_list[$re_adm['adm_id']]))
			{
				$adm_list[$re_adm['adm_id']]=1;
				$pre_find->execute(array(':adm_id' => $re_adm['adm_adm_opiekun']));
				$re_adm=$pre_find->fetch(PDO::FETCH_ASSOC);
				if ($re_adm['adm_id']==$adm['adm_id']) $result=1;
			}
		}
		return ($result);
	}
	
	function person_validate($new_person)
	{
		global $CONFIG;

		$result=array('oper' => 'insert', 'person' => $CONFIG['BLANK_PERSON'], 'dzieci_add' => array(), 'dzieci_del' => array(), 'rights' => 0, 'alerts' => array());
		$old_person=person_get_person($new_person['edit_link']);
//		$old_person=$CONFIG['BLANK_PERSON'];
		$old_person['dzieci_list']=array();
		$result['rights']=person_adm_rights($_SESSION['USER']['adm_id'],$new_person['edit_link']);
		
		foreach ($new_person as $px => $pv)
		{
			if (substr($px,0,13)=='edit_dziecko_')
			{
				$i=substr($px,13);
				$result['dzieci_add'][$i]=person_get_person($i);
				if ($result['dzieci_add'][$i]['lud_id']!=$i) unset ($result['dzieci_add'][$i]);
			}
		}
		
		if (($new_person['edit_typ']=='ojciec' or $new_person['edit_typ']=='matka') and !isset($result['dzieci_add'][$new_person['edit_link']])) $result['dzieci_add'][$new_person['edit_link']]=person_get_person($new_person['edit_link']);
		
		if ($new_person['edit_typ']=='osoba' and $new_person['edit_link']>0) 
		{
			$result['oper']='update';
			$result['person']['lud_adm_admin']=$_SESSION['USER']['adm_id'];
			$result['person']['lud_id']=$new_person['edit_link'];
			person_get_dzieci($new_person['edit_link'],$old_person['dzieci_list']);
			if (count($old_person['dzieci_list']>0)) $result['person']['lud_plec']=$old_person['lud_plec'];
			foreach ($old_person['dzieci_list'] as $dx => $dv)
			{
				if (isset($result['dzieci_add'][$dx])) 
				{
					unset ($result['dzieci_add'][$dx]);
				}
				else 
				{
					$result['dzieci_del'][$dx]=$dv;
				}
			}
		}
		
		if ($result['person']['lud_adm_admin']==0) $result['person']['lud_adm_admin']=$_SESSION['USER']['adm_id'];
		$result['person']['lud_adm_mod']=$_SESSION['USER']['adm_id'];
		$result['person']['lud_activ']=1;

		unset($result['person']['lud_data']);
		unset($result['person']['lud_mod']);
		unset($result['person']['dzieci']);
		unset($result['person']['urodzony']);
		unset($result['person']['zmarly']);
		unset($result['person']['wiek']);
		unset($result['person']['portret']);
		
		foreach ($result['person'] as $px => $pv)
		{
			$idx=substr($px,4);
			if (isset($new_person['edit_'.$idx])) $result['person'][$px]=$new_person['edit_'.$idx];
		}
		
		if (isset($new_person['edit_ojciec']) and $new_person['edit_ojciec']!='') $result['person']['lud_lud_ojciec']=$new_person['edit_ojciec'];
		if (isset($new_person['edit_matka']) and $new_person['edit_matka']!='') $result['person']['lud_lud_matka']=$new_person['edit_matka'];
		
		if (isset($new_person['edit_go']) and $new_person['edit_go']!='') 
		{
			$result['person']['lud_godz_ur']=$new_person['edit_go']+':'+$new_person['edit_mi'];
		}
		
		if ($new_person['edit_typ']=='matka' or $new_person['edit_typ']=='corka' or $new_person['edit_typ']=='siostra') $result['person']['lud_plec']=1;
		if ($new_person['edit_typ']=='ojciec' or $new_person['edit_typ']=='syn' or $new_person['edit_typ']=='brat') $result['person']['lud_plec']=0;

		if ($result['person']['lud_rok_ur']=='') $result['person']['lud_rok_ur']=0;
		if ($result['person']['lud_rok_ur']==0) $result['person']['lud_mie_ur']=0;
		if ($result['person']['lud_mie_ur']==0) $result['person']['lud_dzi_ur']=0;
		if ($result['person']['lud_dzi_ur']==0) $result['person']['lud_godz_ur']='';
		
		if ($result['person']['lud_zmarl']==0) 
		{
			$result['person']['lud_rok_zg']=0;
			$result['person']['lud_miejsce_zg']='';
		}
		if ($result['person']['lud_rok_zg']=='') $result['person']['lud_rok_zg']=0;
		if ($result['person']['lud_rok_zg']==0) $result['person']['lud_mie_zg']=0;
		if ($result['person']['lud_mie_zg']==0) $result['person']['lud_dzi_zg']=0;
		
		$dat_ur=dat_PrepareDate($result['person']['lud_rok_ur'],$result['person']['lud_mie_ur'],$result['person']['lud_dzi_ur']);
		$dat_zg=dat_PrepareDate($result['person']['lud_rok_zg'],$result['person']['lud_mie_zg'],$result['person']['lud_dzi_zg']);
		
		if ($result['person']['lud_zmarl']==1 and $dat_zg['max']<$dat_ur['min'])
		{
			$result['oper']='';
			$result['alerts'][]='Data śmierci nie może być wcześniejsza niż urodzenia';
		}
		
		if ($result['person']['lud_nazwisko']=='')
		{
			$result['oper']='';
			$result['alerts'][]='Nazwisko nie może być puste';
		}
		
		if ($result['person']['lud_lud_ojciec']!=0)
		{
			$rodzic=person_get_person($result['person']['lud_lud_ojciec']);
			$err=person_chk_relative($result['person'],$rodzic,'ojciec');
			foreach ($err as $ex => $ev) $result['alerts'][]=$ev;
		}
		
		if ($result['person']['lud_lud_matka']!=0)
		{
			$rodzic=person_get_person($result['person']['lud_lud_matka']);
			$err=person_chk_relative($result['person'],$rodzic,'matka');
			foreach ($err as $ex => $ev) $result['alerts'][]=$ev;
		}
		
		foreach ($result['dzieci_add'] as $dx => $dv)
		{
			$err=person_chk_relative($result['person'],$dv,'dziecko');
			foreach ($err as $ex => $ev) $result['alerts'][]=$ev;
		}

		if ($result['oper']=='update')
		{
			// update osoby
			if ($result['rights']==0)
			{
				$result['dzieci_del']=array();
				foreach ($result['person'] as $px => $pv)
				{
					if ($px!='lud_plec' and ($pv==0 or $pv=='')) $result[$px]=$old_person[$px];
				}
			}
//			print_r($old_person);
			if ($result['person']['lud_lud_ojciec']==0 and $old_person['lud_lud_ojciec']!=$result['person']['lud_lud_ojciec'])
			{
				$rel=person_find_rel($old_person['lud_id'],$old_person['lud_lud_ojciec'],$path=array(),$step=0,$limiter=0,$lost=$old_person['lud_lud_ojciec']);
//				print_r($rel);
				if (isset($rel[0])) $result['alerts'][]='Zmiana ojca grozi zerwaniem łańcucha rodzinnego';
			}
			if ($result['person']['lud_lud_matka']==0 and $old_person['lud_lud_matka']!=$result['person']['lud_lud_matka'])
			{
				$rel=person_find_rel($old_person['lud_id'],$old_person['lud_lud_matka'],$path=array(),$step=0,$limiter=0,$lost=$old_person['lud_lud_matka']);
//				print_r($rel);
				if (isset($rel[0])) $result['alerts'][]='Zmiana matki grozi zerwaniem łańcucha rodzinnego';
			}
			$rt='';
			$rtd='';
			if ($result['person']['lud_plec']==0)
			{
				$rt='ojciec';
				$rtd='ojca';
			}
			foreach ($result['dzieci_add'] as $dx => $dv)
			{
				if ($dv['lud_'.$rd]>0) $result['alerts'][]=$dv['lud_imiona'].' '.$dv['lud_nazwisko'].' ma już '.$rtd;
			}
			foreach ($result['dzieci_del'] as $dx => $dv)
			{
				$rel=person_find_rel($old_person['lud_id'],$dx,$path=array(),$step=0,$limiter=0,$lost=$dx);
//				print_r($rel);
				if (isset($rel[0])) $result['alerts'][]='Usunięcie dziecka ('.$dv['lud_imiona'].' '.$dv['lud_nazwisko'].') grozi zerwaniem łańcucha rodzinnego';
			}
		}
			
		if ($result['oper']=='insert')
		{
			// nowa osoba
			if ($new_person['edit_typ']=='ojciec' and $old_person['lud_lud_ojciec']!=0) $result['alerts'][]=$old_person['lud_imiona'].' '.$old_person['lud_nazwisko'].' ma już ojca';
			if ($new_person['edit_typ']=='matka' and $old_person['lud_lud_matka']!=0) $result['alerts'][]=$old_person['lud_imiona'].' '.$old_person['lud_nazwisko'].' ma już matkę';
			$rt='';
			$rtd='';
			if ($result['person']['lud_plec']==0)
			{
				$rt='ojciec';
				$rtd='ojca';
			}
			if ($result['person']['lud_plec']==1)
			{
				$rt='matka';
				$rtd='matkę';
			}
			foreach ($result['dzieci_add'] as $dx => $dv)
			{
				if ($dv['lud_lud_'.$rt]!=0) $result['alerts'][]=$dv['lud_imiona'].' '.$dv['lud_nazwisko'].' ma już '.$rtd;
			}
		}
			
		
		return ($result);
	}
	
	function person_insert($new_person)
	{
		$result=false;
		global $db_main;
		$result=true;
		$db_main->beginTransaction();
		unset($new_person['person']['lud_id']);
		unset($new_person['person']['lud_activ']);
		$sql_ins='INSERT INTO lud_ludzie (lud_adm_admin,lud_data,lud_adm_mod,lud_mod,lud_lud_matka,lud_lud_ojciec,lud_plec,lud_nazwisko,lud_panienskie,lud_imiona,lud_rok_ur,lud_mie_ur,lud_dzi_ur,lud_godz_ur,lud_miejsce_ur,lud_rok_zg,lud_mie_zg,lud_dzi_zg,lud_zmarl,lud_miejsce_zg)
								   VALUES(:lud_adm_admin,NOW(),:lud_adm_mod,NOW(),:lud_lud_matka,:lud_lud_ojciec,:lud_plec,:lud_nazwisko,:lud_panienskie,:lud_imiona,:lud_rok_ur,:lud_mie_ur,:lud_dzi_ur,:lud_godz_ur,:lud_miejsce_ur,:lud_rok_zg,:lud_mie_zg,:lud_dzi_zg,:lud_zmarl,:lud_miejsce_zg)';
		$pre_ins=$db_main->prepare($sql_ins);
		if (!$pre_ins->execute($new_person['person'])) $result=false;
		if ($result)
		{
//print($sql_ins."\n");
			$lud_id=0;
			$sql_last='SELECT LAST_INSERT_ID() last';
			$pre_last=$db_main->prepare($sql_last);
			$pre_last->execute(array());
			if ($rec_last=$pre_last->fetchAll(PDO::FETCH_ASSOC)) $lud_id=$rec_last[0]['last'];
			if ($lud_id==0) $result=false;
			$rt='';
			if ($new_person['person']['lud_plec']==0) $rt='ojciec';
			if ($new_person['person']['lud_plec']==1) $rt='matka';
			if ($result)
			{
//print($sql_last."\n".$lud_id."\n");
				$sql_upd='UPDATE lud_ludzie SET lud_lud_'.$rt.'=:rodzic WHERE lud_id=:dziecko';
				$pre_upd=$db_main->prepare($sql_upd);
				foreach ($new_person['dzieci_add'] as $dx => $dv)
				{
					if (!$pre_upd->execute(array('rodzic' => $lud_id, 'dziecko' => $dx))) $result=false;
				}
			}
		}
		if ($result)
		{
			$db_main->commit();
		}
		else
		{
			$db_main->rollBack();
		}
		return ($result);
	}
	
	function person_link($request)
	{
		$result=array();
		global $db_main;
		$person=person_get_person($request['link_link']);
		$relative=person_get_person($request['sel_rodzic']);
//		print_r($person);
//		print_r($relative);
		$result=person_chk_relative($person,$relative,$request['link_typ']);
		if (count($result)==0)
		{
			if ($request['link_typ']=='ojciec' or $request['link_typ']=='matka')
			{
				$sql_upd='UPDATE lud_ludzie SET lud_lud_'.$request['link_typ'].'=:rodzic WHERE lud_id=:dziecko';
				$pre_upd=$db_main->prepare($sql_upd);
				if (!$pre_upd->execute(array('rodzic' => $relative['lud_id'], 'dziecko' => $person['lud_id']))) $result[]='Bład modyfikacji w bazie';
			}
		}
//		print_r($result);
		return ($result);
	}
	
	function person_update($new_person)
	{
		$result=false;
		global $db_main;
		$result=true;
		$db_main->beginTransaction();
		unset($new_person['person']['lud_activ']);
		unset($new_person['person']['lud_adm_admin']);
		$sql_upd='UPDATE lud_ludzie SET lud_adm_mod=:lud_adm_mod, lud_mod=NOW(), lud_lud_matka=:lud_lud_matka, lud_lud_ojciec=:lud_lud_ojciec, lud_plec=:lud_plec, lud_nazwisko=:lud_nazwisko, lud_panienskie=:lud_panienskie, lud_imiona=:lud_imiona, lud_rok_ur=:lud_rok_ur, lud_mie_ur=:lud_mie_ur, lud_dzi_ur=:lud_dzi_ur, lud_godz_ur=:lud_godz_ur, lud_miejsce_ur=:lud_miejsce_ur, lud_rok_zg=:lud_rok_zg, lud_mie_zg=:lud_mie_zg, lud_dzi_zg=:lud_dzi_zg, lud_zmarl=:lud_zmarl, lud_miejsce_zg=:lud_miejsce_zg WHERE lud_id=:lud_id';
//		print($sql_upd);
//		print_r($new_person['person']);
		$pre_upd=$db_main->prepare($sql_upd);
		if (!$pre_upd->execute($new_person['person'])) $result=false;
		if ($result)
		{
			$rt='';
			if ($new_person['person']['lud_plec']==0) $rt='ojciec';
			if ($new_person['person']['lud_plec']==1) $rt='matka';
			if ($result)
			{
				$sql_add='UPDATE lud_ludzie SET lud_'.$rt.'=:rodzic WHERE lud_id=:dziecko';
				$pre_add=$db_main->prepare($sql_add);
				foreach ($new_person['dzieci_add'] as $dx => $dv)
				{
					if (!$pre_add->execute(array('rodzic' => $new_person['person']['lud_id'], 'dziecko' => $dx))) $result=false;
				}
			}
			if ($result)
			{
				$sql_add='UPDATE lud_ludzie SET lud_'.$rt.'=0 WHERE lud_id=:dziecko';
				$pre_add=$db_main->prepare($sql_add);
				foreach ($new_person['dzieci_del'] as $dx => $dv)
				{
					if (!$pre_add->execute(array('dziecko' => $dx))) $result=false;
				}
			}
		}
		if ($result)
		{
			$db_main->commit();
		}
		else
		{
			$db_main->rollBack();
		}
		return ($result);
	}
	
	function person_chk_relative($person,$relative,$type)
	{
		$result=array();
		if (($type=='córka' or $type=='matka') and $relative['lud_plec']==0) $result[]='Płeć nieodpowiednia do relacji: '.$type.' nie może być mężczyzną';
		if (($type=='syn' or $type=='ojciec') and $relative['lud_plec']==1) $result[]='Płeć nieodpowiednia do relacji: '.$type.' nie może być kobietą';
		
		if ($type=='ojciec' or $type=='matka')
		{
			$dat_ur_d=dat_PrepareDate($person['lud_rok_ur'],$person['lud_mie_ur'],$person['lud_dzi_ur']);
			$dat_zg_d=dat_PrepareDate($person['lud_rok_zg'],$person['lud_mie_zg'],$person['lud_dzi_zg']);
			$dat_ur_r=dat_PrepareDate($relative['lud_rok_ur'],$relative['lud_mie_ur'],$relative['lud_dzi_ur']);
			$dat_zg_r=dat_PrepareDate($relative['lud_rok_zg'],$relative['lud_mie_zg'],$relative['lud_dzi_zg']);
			$pr=$relative['lud_plec'];
		}
		if ($type=='syn' or $type=='córka' or $type=='dziecko')
		{
			$dat_ur_d=dat_PrepareDate($relative['lud_rok_ur'],$relative['lud_mie_ur'],$relative['lud_dzi_ur']);
			$dat_zg_d=dat_PrepareDate($relative['lud_rok_zg'],$relative['lud_mie_zg'],$relative['lud_dzi_zg']);
			$dat_ur_r=dat_PrepareDate($person['lud_rok_ur'],$person['lud_mie_ur'],$person['lud_dzi_ur']);
			$dat_zg_r=dat_PrepareDate($person['lud_rok_zg'],$person['lud_mie_zg'],$person['lud_dzi_zg']);
			$pr=$person['lud_plec'];
		}
		if ($dat_ur_d['max']!=0 and $dat_ur_r['min']!=0 and $dat_ur_d['max']<($dat_ur_r['min']+10*365*86400)) $result[]='Rodzic musi być przynajmniej 10 lat starszy od dziecka';
		if ($pr==0 and $dat_ur_d['min']!=0 and $dat_zg_r['max']!=0 and $dat_ur_d['min']>($dat_zg_r['max']+10*30*86400)) $result[]='Dziecko nie może urodzić się później niż 10 miesięcy po śmierci ojca';
		if ($pr==1 and $dat_ur_d['min']!=0 and $dat_zg_r['max']!=0 and $dat_ur_d['min']>($dat_zg_r['max'])) $result[]='Dziecko nie może urodzić się po śmierci matki';
		
		return ($result);
	}
	
	function person_get_relations()
	{
		global $db_main;
		$result=array();
		$sql_per='SELECT 
					  lud_id, 
					  (SELECT lud_id FROM lud_ludzie WHERE lud_id>0 AND lud_activ>0 AND lud_id=lud.lud_lud_matka) matka, 
					  (SELECT lud_id FROM lud_ludzie WHERE lud_id>0 AND lud_activ>0 AND lud_id=lud.lud_lud_ojciec) ojciec,
					  (SELECT GROUP_CONCAT(lud_id) FROM lud_ludzie WHERE lud_activ>0 AND lud_plec=1 AND (lud_lud_matka=lud.lud_id OR lud_lud_ojciec=lud.lud_id)) corki,  
					  (SELECT GROUP_CONCAT(lud_id) FROM lud_ludzie WHERE lud_activ>0 AND lud_plec=0 AND (lud_lud_matka=lud.lud_id OR lud_lud_ojciec=lud.lud_id)) synowie  
					FROM 
					  lud_ludzie lud 
					WHERE 
					  lud_id>0 AND 
					  lud_activ>0';
		$pre_per=$db_main->prepare($sql_per);
		$pre_per->execute(array());
		while ($res_per=$pre_per->fetch(PDO::FETCH_ASSOC))
		{
			if ($res_per['matka']!='') $result[$res_per['lud_id']][$res_per['matka']]='matka';
			if ($res_per['ojciec']!='') $result[$res_per['lud_id']][$res_per['ojciec']]='ojciec';
			if ($res_per['corki']!='')
			{
				$ar=explode(',',$res_per['corki']);
				foreach ($ar as $ax => $av) $result[$res_per['lud_id']][$av]='córka';
			}
			if ($res_per['synowie']!='')
			{
				$ar=explode(',',$res_per['synowie']);
				foreach ($ar as $ax => $av) $result[$res_per['lud_id']][$av]='syn';
			}
		}
		return ($result);
	}
	
	function person_find_rel($person,$relative,$path=array(),$step=0,$limiter=0,$lost=0)
	{
		global $relations;
		$result=array();
//		print($step.' person '.$person."\n");
//		print_r($path);
		if ($person==0 or $relative==0)
		{
			$result[0]='WRONG PERSON';
		}
		if ($person==$relative)
		{
			$result[0]='SAME PERSON';
		}
		if ($limiter>0 and $step>=$limiter)
		{
			$result[0]='OVER LIMIT';
		}
		if (isset($path[$person]))
		{
			$result[0]='LOOP';
		}
		if (!isset($relations[$person]))
		{
			$result[0]='UNNKNOWN';
		}
		if (count($result)==0)
		{
			foreach($relations[$person] as $rx => $rv)
			{
				if (count($result)==0 and $rx==$relative and $rx!=$lost) $result[$rx]=$rv;
			}
			if (count($result)==0)
			{
				$rpa=array();
				$ppa=$path;
				$ppa[$person]='1';
				foreach($relations[$person] as $rx => $rv)
				{
					$p_path=person_find_rel($rx,$relative,$ppa,$step+1,$limiter);
					if (count($p_path)>0 and !isset($p_path[0]))
					{
						if ($limiter==0 or $limiter>$step+count($p_path)) $limiter=$step+count($p_path);
						$rpa=$p_path;
						$result=array($rx => $rv);
					}
					if ($step==0)
					{
//						print($rv."\n");
//						print_r($p_path);
					}
				}
				foreach ($rpa as $rx => $rv) $result[$rx]=$rv;
			}
		}
		if (count($result)==0) $result[0]='END';
		return($result);
	}
	

?>