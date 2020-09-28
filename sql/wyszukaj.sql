SELECT * FROM lud_ludzie WHERE lud_activ=1 AND 
  (lud_nazwisko LIKE(:nazwisko) AND lud_imiona LIKE(:imie))
UNION
SELECT * FROM lud_ludzie WHERE lud_activ=1 AND 
  (lud_panienskie LIKE(:nazwisko) AND lud_imiona LIKE(:imie))
UNION
SELECT * FROM lud_ludzie WHERE lud_activ=1 AND 
  (lud_nazwisko LIKE(:cale))
UNION
SELECT * FROM lud_ludzie WHERE lud_activ=1 AND 
  (lud_panienskie LIKE(:cale))
UNION
SELECT * FROM lud_ludzie WHERE lud_activ=1 AND 
  (lud_imiona LIKE(:calei))
