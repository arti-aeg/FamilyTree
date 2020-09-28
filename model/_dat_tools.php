<?php
function dat_PrepareDate($r,$m,$d)
{
	$miesiace=array(
		'mian' => array(1 => 'styczeń', 2 => 'luty', 3 => 'marzec', 4 => 'kwiecień', 5 => 'maj', 6 => 'czerwiec', 7 => 'lipiec', 8 => 'sierpień', 9 => 'wrzesień', 10 => 'październik', 11 => 'listopad', 12 => 'grudzień'),
		'dop' => array(1 => 'stycznia', 2 => 'lutego', 3 => 'marca', 4 => 'kwietnia', 5 => 'maja', 6 => 'czerwca', 7 => 'lipca', 8 => 'sierpnia', 9 => 'września', 10 => 'października', 11 => 'listopada', 12 => 'grudnia')
	);
	$miedni=dat_miesiace();
	$result=array('short' => '', 'long' => '', 'min' => 0, 'max' => 0);
	if (is_numeric($r) and $r>0)
	{
		$result['short']=''.$r;
		$result['long']=''.$r;
		$result['min']=mktime(0,0,0,1,1,$r);
		$result['max']=mktime(23,59,59,12,31,$r);
		if (is_numeric($m) and $m>0 and $m<=12)
		{
			$mie=$m.'';
			if ($m<10) $mie='0'.$mie;
			$result['short'].='-'.$mie;
			$result['long']=$miesiace['mian'][$m].' '.$r;
			$result['min']=mktime(0,0,0,$m,1,$r);
			$maxd=$miedni[$m]['dni'];
			if ($m==2) $maxd+=dat_przestepny($r);
			$result['max']=mktime(23,59,59,$m,$maxd,$r);
			if (is_numeric($d) and $d>0 and $d<=$maxd)
			{
				$dzi=$d.'';
				if ($d<10) $dzi='0'.$dzi;
				$result['short'].='-'.$dzi;
				$result['long']=$d.' '.$miesiace['dop'][$m].' '.$r;
				$result['min']=mktime(0,0,0,$m,$d,$r);
				$result['max']=mktime(23,59,59,$m,$d,$r);
			}
		}
	}
	return ($result);
}

function dat_Deklinacja($num)
{
	$result='poj';
	if ($num>1)
	{
		$result='mian';
		if (($num>4 and $num<22) or (($num % 10)<2) or (($num % 10)>4)) $result='dop';
	}
	return ($result);
}

function dat_Wiek($ur, $um, $ud, $zr, $zm, $zd, $zmarl)
{
	$result='';
	$jedn=array(
		'R' => array('poj' => 'rok', 'mian' => 'lata', 'dop' => 'lat'),
		'M' => array('poj' => 'miesiąc', 'mian' => 'miesiące', 'dop' => 'miesięcy'),
		'D' => array('poj' => 'dzień', 'mian' => 'dni', 'dop' => 'dni')
	);
	if ($zmarl==0 or $zr>0)
	{
		if ($zr==0)
		{
			$zr=date('Y');
			$zm=date('n');
			$zd=date('j');
		}
		if ($zm==0)
		{
			$zm=$um;
			$zd=$ud;
		}
		if ($zd==0) $zd=$ud;
		if ($ur>0)
		{
			$ddif=0;
			$mdif=0;
			$rdif=$zr-$ur;
			if ($um>0)
			{
				$mdif=$zm-$um;
				if ($ud>0)
				{
					$ddif=$zd-$ud;
					if ($ddif<0)
					{
						$mdif--;
						$ddif+=28;
						if ($um==1 or $um==3 or $um==5 or $um==7 or $um==8 or $um==10 or $um==12) $ddif+=3;
						if ($um==4 or $um==6 or $um==9 or $um==11) $ddif+=2;
						if ($um==2 and (($ur % 4)==0 and ($ur % 100)!=0 or ($ur % 400)==0)) $ddif+=1;
					}
				}
				if ($mdif<0)
				{
					$rdif--;
					$mdif+=12;
				}
			}
			if ($rdif>0)
			{
				$result=$rdif.' '.$jedn['R'][dat_Deklinacja($rdif)];
			}
			if ($rdif==0)
			{
				if ($mdif>0)
				{
					$result=$mdif.' '.$jedn['M'][dat_Deklinacja($mdif)];
				}
				if ($mdif==0)
				{
					if ($ddif>0)
					{
						$result=$ddif.' '.$jedn['D'][dat_Deklinacja($ddif)];
					}
					if ($ddif==0) $result='niemowle';
				}
			}
		}
	}
	return ($result);
}

function dat_miesiace($fmt='array')
{
	$result=array(
		0 => array('label' => '--nieznany--', 'dni' => 0),
		1 => array('label' => 'styczeń', 'dni' => 31),
		2 => array('label' => 'luty', 'dni' => 28),
		3 => array('label' => 'marzec', 'dni' => 31),
		4 => array('label' => 'kwiecień', 'dni' => 30),
		5 => array('label' => 'maj', 'dni' => 31),
		6 => array('label' => 'czerwiec', 'dni' => 30),
		7 => array('label' => 'lipiec', 'dni' => 31),
		8 => array('label' => 'sierpień', 'dni' => 31),
		9 => array('label' => 'wrzesień', 'dni' => 30),
		10 => array('label' => 'październik', 'dni' => 31),
		11 => array('label' => 'listopad', 'dni' => 30),
		12 => array('label' => 'grudzień', 'dni' => 31)
	);
	if ($fmt=='json') $result=json_encode($result);
	return ($result);
}

function dat_przestepny($rok)
{
	$result=0;
	if (4*floor($rok/4)==$rok)
	{
		$result=1;
		if (100*floor($rok/100)==$rok)
		{
			$result=0;
			if (1000*floor($rok/1000)==$rok) $result=1;
		}
	}
	return ($result);
}

?>