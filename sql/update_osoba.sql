UPDATE lud_ludzie SET
  lud_adm_mod = :lud_adm_mod,
  lud_mod = NOW(),
  lud_lud_matka = :lud_lud_matka,
  lud_lud_ojciec = :lud_lud_ojciec,
  lud_plec = :lud_plec,
  lud_nazwisko = :lud_nazwisko,
  lud_panienskie = :lud_panienskie,
  lud_imiona = :lud_imiona,
  lud_rok_ur = :lud_rok_ur,
  lud_mie_ur = :lud_mie_ur,
  lud_dzi_ur = :lud_dzi_ur,
  lud_godz_ur = :lud_godz_ur,
  lud_miejsce_ur = :lud_miejsce_ur,
  lud_rok_zg = :lud_rok_zg,
  lud_mie_zg = :lud_mie_zg,
  lud_dzi_zg = :lud_dzi_zg,
  lud_zmarl = :lud_zmarl,
  lud_miejsce_zg = :lud_miejsce_zg
WHERE lud_id=:lud_id