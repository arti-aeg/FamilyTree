SELECT
  lud.*, 
  ada.adm_id ada_id, 
  ada.adm_nick ada_nick, 
  ada.adm_mail ada_mail, 
  adm.adm_id adm_id, 
  adm.adm_nick adm_nick, 
  adm.adm_mail adm_mail 
FROM 
  lud_ludzie lud, 
  adm_admini ada, 
  adm_admini adm 
WHERE 
  lud.lud_adm_admin=ada.adm_id AND 
  lud.lud_adm_mod=adm.adm_id AND 
  lud.lud_activ=1 AND 
  (((lud.lud_lud_matka=:lud_id) AND (lud.lud_lud_matka<>0)) OR ((lud.lud_lud_ojciec=:lud_id) AND (lud.lud_lud_ojciec<>0)))