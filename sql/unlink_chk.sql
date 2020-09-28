SELECT
  (SELECT COUNT(*) FROM lud_ludzie WHERE lud_activ>0 AND (lud_lud_matka=:lud_id OR lud_lud_ojciec=:lud_id)) dzieci,
  (SELECT COUNT(*) FROM lud_ludzie WHERE lud_activ>0 AND lud_id<>0 AND lud_id<>:lud_id AND lud_lud_ojciec=:lud_lud_ojciec AND lud_lud_ojciec<>0) dzieci_ojciec,
  (SELECT COUNT(*) FROM lud_ludzie WHERE lud_activ>0 AND lud_id<>0 AND lud_id<>:lud_id AND lud_lud_matka=:lud_lud_matka AND lud_lud_matka<>0) dzieci_matka,
  (SELECT COUNT(*) FROM lud_ludzie WHERE lud_activ>0 AND lud_id<>0 AND (lud_id=(SELECT lud_lud_ojciec FROM lud_ludzie WHERE lud_id=:lud_lud_ojciec) OR lud_id=(SELECT lud_lud_matka FROM lud_ludzie WHERE lud_id=:lud_lud_ojciec))) dziadkowie_ojciec,
  (SELECT COUNT(*) FROM lud_ludzie WHERE lud_activ>0 AND lud_id<>0 AND (lud_id=(SELECT lud_lud_ojciec FROM lud_ludzie WHERE lud_id=:lud_lud_matka) OR lud_id=(SELECT lud_lud_matka FROM lud_ludzie WHERE lud_id=:lud_lud_matka))) dziadkowie_matka
