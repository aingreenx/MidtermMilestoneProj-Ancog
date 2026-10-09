USE greek_recipe_hub;
UPDATE categories
SET name = CASE id
    WHEN 1 THEN 'Meze (Appetizers)'
    WHEN 2 THEN 'Salads'
    WHEN 3 THEN 'Seafood'
    WHEN 4 THEN 'Sweets'
END
WHERE id IN (1, 2, 3, 4);