update va_global_settings set setting_value = 'http://ofo/' where setting_name='site_url';
update va_global_settings set setting_value = 'http://ofo/' where setting_name='secure_url';


SELECT max(id)
FROM aaia_parts; #367735

SELECT *
FROM aaia_parts
WHERE DATE(created_at) = curdate();

SELECT *
FROM (SELECT DISTINCT
        (part),
        aaia
      FROM aaia_parts
      WHERE manufacturer = 'Pentius' AND part LIKE '%*%') pentius INNER JOIN aaia ON pentius.aaia = aaia.id;
# DELETE FROM aaia_parts WHERE manufacturer='Pentius' AND created_at='2016-01-01 00:00:00';

SELECT DISTINCT (part) FROM aaia_parts WHERE manufacturer='Pentius' ORDER BY part ASC ;

# DELETE FROM aaia_parts WHERE manufacturer='Pentius' AND part ='N/R';

SELECT *
FROM aaia
WHERE id = 34007;

#spit out product_import.csv to use in Admin->Products->Top->Import Products
SELECT
  part                                                                    AS item_code,
  @item_name := CONCAT(manufacturer, ' ', part, ' ', PartTerminologyName) AS item_name,
  15                                                                       AS manufacturer_id,
  part                                                                    AS manufacturer_code,
  @desc := IF(description = 'Description  :', @item_name, description)    AS short_description,
  @desc                                                                   AS full_description,
  999                                                                     AS price,
  999 AS sales_price,
  PartTerminologyName                                                     AS category_name,
  REPLACE(REPLACE(REPLACE(REPLACE(@item_name, ' ', '-'),'.','_'),'(','_'),')','_')                                           AS friendly_url
FROM (SELECT DISTINCT
        (part) AS part,
        manufacturer,
        description,
        type
      FROM aaia_parts
      WHERE manufacturer = 'Pentius') Pentius
  INNER JOIN aaia_pcdb.Parts ON type = PartTerminologyID ORDER BY part ASC;


SELECT
  manufacturer_code,
  item_name,
  replace(item_name, ' ', '-')
FROM va_items;

SHOW CREATE VIEW all_applications;


UPDATE va_items
SET is_approved = 0
WHERE price = 999;

DROP VIEW aaia_view;
CREATE ALGORITHM = UNDEFINED
  DEFINER =`root`@`localhost`
  SQL SECURITY DEFINER VIEW `aaia_view` AS
  SELECT
    `parts`.`aaia`               AS `aaia`,
    `parts`.`part`               AS `part`,
    `parts`.`type`               AS `type`,
    `parts`.`manufacturer`       AS `manufacturer`,
    `store`.`is_showing`         AS `Active`,
    `store`.`sales_price`        AS `price`,
    `parts`.`description`        AS `description`,
    `desc`.`content`             AS `content`,
    `store`.`tiny_image`         AS `thumbnail`,
    `store`.`big_image`          AS `bigimage`,
    `store`.`item_name`          AS `store_name`,
    `store`.`price`              AS `retail_price`,
    `store`.`use_stock_level`    AS `use_stock_level`,
    `store`.`stock_level`        AS `stock_level`,
    `store`.`shipping_in_stock`  AS `shipping_in_stock`,
    `store`.`shipping_out_stock` AS `shipping_out_stock`,
    `store`.`friendly_url`       AS `friendly_url`
  FROM ((`oilfiltersonline`.`aaia_parts` `parts` LEFT JOIN `oilfiltersonline`.`aaia_descriptions` `desc`
      ON ((`parts`.`description` = `desc`.`description`))) INNER JOIN (SELECT * FROM `oilfiltersonline_test_store`.`va_items` WHERE is_approved != 0) `store`
      ON ((`parts`.`part` = `store`.`item_code`)));

UPDATE oilfiltersonline_test_store.va_items SET stock_level = 100, shipping_in_stock = 2, shipping_out_stock = 2, use_stock_level = 1, disable_out_of_stock = 0, buying_price = 5.89,  sales_price = 5.89, price = 5.89 WHERE item_code = 'PAB10013' AND manufacturer_id = 15;

SELECT * FROM oilfiltersonline_test_store.va_items WHERE item_code = 'PAB10013';

SELECT * FROM oilfiltersonline_test_store.va_items WHERE item_name like 'Pentius%';

SELECT * FROM aaia_parts WHERE manufacturer='Pentius' AND part LIKE 'PFB20011*%';