SELECT * 
FROM lud_ludzie lud 
WHERE 
  lud.lud_activ>0 AND 
  (lud.lud_lud_matka=:lud_id OR lud.lud_lud_ojciec=:lud_id OR 
  ((lud.lud_lud_matka=0 OR lud.lud_lud_matka=:lud_id) AND lud.lud_lud_ojciec IN (SELECT lud_lud_ojciec FROM lud_ludzie WHERE lud_activ>0 AND lud_lud_ojciec<>0 AND lud_lud_matka=:lud_id) OR
  ((lud.lud_lud_ojciec=0 OR lud.lud_lud_ojciec=:lud_id) AND lud.lud_lud_matka IN (SELECT lud_lud_matka FROM lud_ludzie WHERE lud_activ>0 AND lud_lud_matka<>0 AND lud_lud_ojciec=:lud_id))))