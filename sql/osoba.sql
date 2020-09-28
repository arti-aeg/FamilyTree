SELECT 
  lud.*, 
  ada.adm_id ada_id, 
  ada.adm_nick ada_nick, 
  ada.adm_mail ada_mail, 
  adm.adm_id adm_id, 
  adm.adm_nick adm_nick, 
  adm.adm_mail adm_mail,
  (SELECT MAX(lud_rok_ur+13) FROM lud_ludzie WHERE lud_activ>0 AND lud_rok_ur>0 AND (lud.lud_lud_ojciec=lud_id OR lud.lud_lud_matka=lud_id)) min_rok_ur,
  LEAST((SELECT MIN(lud_rok_zg+1-lud.lud_plec) FROM lud_ludzie WHERE lud_activ>0 AND lud_rok_zg>0 AND (lud.lud_lud_ojciec=lud_id OR lud.lud_lud_matka=lud_id)),
  (SELECT MIN(lud_rok_ur)-13 FROM lud_ludzie WHERE lud_activ>0 AND lud_rok_ur>0 AND (lud_lud_ojciec=lud.lud_id OR lud_lud_matka=lud.lud_id))) max_rok_ur,
  (SELECT MAX(lud_rok_ur)-1+lud.lud_plec FROM lud_ludzie WHERE lud_activ>0 AND lud_rok_ur>0 AND (lud_lud_ojciec=lud.lud_id OR lud_lud_matka=lud.lud_id)) min_rok_zg
FROM 
  lud_ludzie lud, 
  adm_admini ada, 
  adm_admini adm 
WHERE 
  lud.lud_adm_admin=ada.adm_id AND 
  lud.lud_adm_mod=adm.adm_id AND 
  lud.lud_activ=1 AND 
  lud.lud_id=:lud_id