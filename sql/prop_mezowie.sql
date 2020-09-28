SELECT * 
FROM lud_ludzie lud 
WHERE 
  lud.lud_activ>0 AND 
  lud.lud_id IN (SELECT lud_lud_ojciec FROM lud_ludzie WHERE lud_activ>0 AND lud.lud_id<>0 AND lud_lud_matka<>0 AND lud_lud_matka=:lud_id)