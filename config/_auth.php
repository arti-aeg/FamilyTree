<?php

function Login($user,$passwd)
{
	global $CONFIG;
	global $db_main;
	$result=false;
	$sql_usr='SELECT * FROM adm_admini WHERE adm_nick LIKE(:user) or adm_mail LIKE(:user)';
	$pre_usr=$db_main->prepare($sql_usr);
	$pre_usr->execute(array('user' => $user));
	if ($res_usr=$pre_usr->fetch(PDO::FETCH_ASSOC))
	{
		if ($res_usr['adm_haslo']==md5($passwd))
		{
			$result=$res_usr;
		}
	}
	return $result;
}
