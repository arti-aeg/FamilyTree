SELECT * 
FROM lud_ludzie lud 
WHERE 
  lud.lud_activ>0 AND
  lud.lud_id>0 AND  
  (lud.lud_id=:lud_lud_matka OR
  lud.lud_id IN (SELECT lud_lud_matka FROM lud_ludzie WHERE lud_activ>0 AND lud.lud_id<>0 AND lud_lud_ojciec<>0 AND lud_lud_ojciec=:lud_lud_ojciec))