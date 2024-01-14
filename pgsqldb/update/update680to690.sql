


 


DROP VIEW IF EXISTS customers_view  ;


CREATE VIEW  customers_view
AS
SELECT
  customers.customer_id AS customer_id,
  customers.customer_name AS customer_name,
  customers.detail AS detail,
  customers.email AS email,
  customers.phone AS phone,
  customers.status AS status,
  customers.city AS city,
  customers.createdon AS createdon,
  customers.leadsource AS leadsource,
  customers.leadstatus AS leadstatus,
  customers.country AS country,
  customers.passw AS passw,
  (SELECT
      COUNT(0)
    FROM messages m
    WHERE ((m.item_id = customers.customer_id)
    AND (m.item_type = 2)))
  AS mcnt,
  (SELECT
      COUNT(0)
    FROM files f
    WHERE ((f.item_id = customers.customer_id)
    AND (f.item_type = 2)))
  AS fcnt,
  (SELECT
      COUNT(0)
    FROM eventlist e
    WHERE ((e.customer_id = customers.customer_id)
    AND (e.eventdate >= NOW())))
  AS ecnt
FROM customers;





ALTER TABLE "equipments" ADD branch_id INTEGER NULL ;
ALTER TABLE "ppo_zformstat" ADD amount4 DECIMAL(11, 2) DEFAULT 0.00;



update  "metadata" set  description ='Програми лояльності' where  meta_name='Discounts';
update  "metadata" set  description ='Отримані послуги' where  meta_name='IncomeService';

DELETE  FROM "options" WHERE  optname='version' ;
INSERT INTO "options" (optname, optvalue) values('version','6.9.0');  