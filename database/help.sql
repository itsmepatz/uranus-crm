------- generate hak akses
-- admin
INSERT INTO `sso_role_access` (`role_id`,`module_id`,`menu_id`,`feature_id`,`feature_name`,`status`)
    SELECT
    1, a.module_id, b.menu_id, c.feature_id, c.name, 1
FROM modules a
LEFT JOIN module_menu b ON a.module_id = b.module_id AND b.status = 1
LEFT JOIN module_feature c ON b.menu_id = c.menu_id AND b.status = 1
WHERE a.status = 1;
-- manager
INSERT INTO `sso_role_access` (`role_id`,`module_id`,`menu_id`,`feature_id`,`feature_name`,`status`)
    SELECT
    2, a.module_id, b.menu_id, c.feature_id, c.name,
    CASE WHEN
        c.name NOT IN (
            'management_network',
            'management_network_list',
            'management_network_add',
            'management_network_upd',
            'management_network_del',
            'management_network_postback',
            'management_network_postback_add',
            'management_network_postback_upd',
            'management_network_postback_del',
            'management_network_postback_list'
        )
    THEN 1 ELSE 0 END AS flag
FROM modules a
LEFT JOIN module_menu b ON a.module_id = b.module_id AND b.status = 1
LEFT JOIN module_feature c ON b.menu_id = c.menu_id AND b.status = 1
WHERE a.status = 1;
-- leader tim cs
INSERT INTO `sso_role_access` (`role_id`,`module_id`,`menu_id`,`feature_id`,`feature_name`,`status`)
SELECT
    6, a.module_id, b.menu_id, c.feature_id, c.name,
    CASE WHEN
        (
            b.module_id = 2 AND
            c.name NOT IN (
                'penjualan_orders_action_sale',
                'penjualan_orders_to_trash',
                'penjualan_orders_trash',
                'penjualan_orders_trash_list',
                'penjualan_orders_trash_delete',
                'penjualan_orders_trash_pulihkan'
            )
        ) OR
        c.name IN (
            'report_simple',
            'report_simple_list',
            'sso_user_profile',
            'sso_user_profile_save'
        )
    THEN 1 ELSE 0 END AS flag
FROM modules a
LEFT JOIN module_menu b ON a.module_id = b.module_id AND b.status = 1
LEFT JOIN module_feature c ON b.menu_id = c.menu_id AND b.status = 1
WHERE a.status = 1;
-- cs
INSERT INTO `sso_role_access` (`role_id`,`module_id`,`menu_id`,`feature_id`,`feature_name`,`status`)
SELECT
    5, a.module_id, b.menu_id, c.feature_id, c.name,
    CASE WHEN
        (
            b.module_id = 2 AND
            c.name NOT IN (
                'penjualan_orders_action_sale',
                'penjualan_orders_view_modifier',
                'penjualan_orders_to_trash',
                'penjualan_orders_trash',
                'penjualan_orders_trash_list',
                'penjualan_orders_trash_delete',
                'penjualan_orders_trash_pulihkan'
            )
        )
        OR
        c.name IN (
            'sso_user_profile',
            'sso_user_profile_save'
        )
    THEN 1 ELSE 0 END AS flag
FROM modules a
LEFT JOIN module_menu b ON a.module_id = b.module_id AND b.status = 1
LEFT JOIN module_feature c ON b.menu_id = c.menu_id AND b.status = 1
WHERE a.status = 1;
-- finannce
INSERT INTO `sso_role_access` (`role_id`,`module_id`,`menu_id`,`feature_id`,`feature_name`,`status`)
SELECT
    3, a.module_id, b.menu_id, c.feature_id, c.name,
    CASE WHEN
    b.module_id = 4 OR
    c.name IN (
        'penjualan_orders_detail',
        'penjualan_orders_list',
        'penjualan_orders_sale',
        'penjualan_orders_verify_payment',
        'penjualan_orders_action_sale',
        'penjualan_orders_update_shopping_info',
        'penjualan_orders_to_trash',
        'penjualan_orders_trash',
        'penjualan_orders_trash_list',
        'penjualan_orders_trash_delete',
        'penjualan_orders_trash_pulihkan',
        'sso_user_profile',
        'sso_user_profile_save'
    ) THEN 1 ELSE 0 END AS flag
FROM modules a
LEFT JOIN module_menu b ON a.module_id = b.module_id AND b.status = 1
LEFT JOIN module_feature c ON b.menu_id = c.menu_id AND b.status = 1
WHERE a.status = 1;
-- logistik
INSERT INTO `sso_role_access` (`role_id`,`module_id`,`menu_id`,`feature_id`,`feature_name`,`status`)
SELECT
    4, a.module_id, b.menu_id, c.feature_id, c.name,
    CASE WHEN
        b.module_id = 3 OR
        c.name IN (
            'penjualan_orders_detail',
            'penjualan_orders_list',
            'penjualan_orders_sale',
            'penjualan_orders_update_customer_info',
            'sso_user_profile',
            'sso_user_profile_save'
        )
    THEN 1 ELSE 0 END AS flag
FROM modules a
LEFT JOIN module_menu b ON a.module_id = b.module_id AND b.status = 1
LEFT JOIN module_feature c ON b.menu_id = c.menu_id AND b.status = 1
WHERE a.status = 1;

// dummy order
SET @rand = FLOOR(RAND() * 401) + 100;

INSERT INTO customer (`full_name`,`telephone`,`created_at`,`status`)
VALUES (CONCAT('Ade Pangestu ',@rand), CONCAT('6282322254', @rand), NOW(), 1);

SET @customer_id = LAST_INSERT_ID();

INSERT INTO customer_address (`customer_id`, `address`,`desa_kelurahan`,`kecamatan`,`kabupaten`,`provinsi`,`postal_code`,`created_at`,`status`)
VALUES (
  @customer_id,
  CONCAT('Batik Giri Alam RT 01 / 04, Blok ', @rand),
  'Gumelem Wetan',
  'Susukan',
  'Banjarnegara',
  'Jawa Tengah',
  '53475',
  NOW(),
  1
);

SET @id_address_customer = LAST_INSERT_ID();

INSERT INTO orders (
  `customer_id`,
    `customer_address_id`,
    `payment_method_id`,
    `logistic_id`,
    `order_status_id`,
    `logistics_status_id`,
    `call_method_id`,
    `order_status`,
    `logistics_status`,
    `shipping_code`,
    `call_method`,
    `order_code`,
    `customer_info`,
    `customer_address`,
    `total_price`,
    `created_at`,
    `version`
)
values
(
    @customer_id,
    @id_address_customer,
    2, -- Payment: BCA
    1, -- Logistik: TIKI
    1, -- Status order: New Order
    1, -- none
    1, -- call method : telepon
    'New Order',
    'none',
    '',
    'Telepon',
    concat(@rand,'-',NOW()),
    JSON_OBJECT(
        'full_name', CONCAT('Ade Pangestu ',@rand),
        'telephone', CONCAT('6282322254', @rand)
    ),
    JSON_OBJECT(
        'address',CONCAT('Batik Giri Alam RT 01 / 04, Blok ', @rand),
        'desa_kelurahan','Gumelem Wetan',
        'kecamatan','Susukan',
        'kabupaten','Banjarnegara',
        'provinsi','Jawa Tengah',
        'postal_code','53475'
    ),
    370000.00,
    NOW(),
    1
);

SET @id_orders = LAST_INSERT_ID();

INSERT INTO `orders_cart` ( order_id,product_id,product_package_id,product_merk,product_name,package_name,price,qty,weight,is_package,price_type,package_price)
SELECT @id_orders, a.product_id, a.product_package_id, a.merk, a.name, b.name, a.price, a.qty, a.weight, 1, b.price_type, b.price
FROM `product_package` b
LEFT JOIN `product_package_list` a ON b.product_package_id = a.product_package_id
WHERE b.product_package_id = 1;

-- clear orders
truncate `orders_network`;
truncate `orders_process`;
truncate `orders_logistics`;
truncate `orders_invoices`;
truncate `orders_cart`;
truncate `orders`;
truncate `customer_address`;
truncate `customer`;

-- check size db
SELECT table_schema "DB Name",
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) "DB Size in MB"
FROM information_schema.tables
GROUP BY table_schema;

-- chesk size table
SELECT
    table_name AS `Table`,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) `Size in MB`
FROM information_schema.TABLES
WHERE table_schema = "newhb_db"
-- check sessin created at
SELECT
  FROM_UNIXTIME(`timestamp`) AS created_at
FROM
  sso_session_web


-- query statistik
--  sampple user 57
SELECT
    CONCAT(a.day,'/',a.month) AS periode,
    (
        SELECT COUNT(DISTINCT z.order_id) AS total
        FROM orders_process z
        WHERE
            order_status_id = 2 AND
            user_id = 57 AND
            DATE(created_at) = a.db_date
    ) AS total_fu,
    (
        SELECT COUNT(DISTINCT z.order_id) AS total
        FROM orders_process z
        WHERE
            z.order_status_id = 3 AND
            z.user_id = 57 AND
            DATE(z.created_at) = a.db_date
    ) AS total_pending,
    (
        SELECT COUNT(DISTINCT z.order_id) AS total
        FROM orders_process z
        WHERE
            z.order_status_id = 4 AND
            z.user_id = 57 AND
            DATE(z.created_at) = a.db_date
    ) AS total_cancel,
    (
        SELECT COUNT(DISTINCT z.order_id) AS total
        FROM orders_process z
        WHERE
            z.order_status_id = 5 AND
            z.user_id = 57 AND
            DATE(z.created_at) = a.db_date
    ) AS total_confirm,
    (
        SELECT COUNT(DISTINCT z.order_id) AS total
        FROM orders_process z
        WHERE
            z.order_status_id = 6 AND
            z.user_id = 57 AND
            DATE(z.created_at) = a.db_date
    ) AS total_verify,
    (
        SELECT COUNT(DISTINCT z.order_id) AS total
        FROM orders_process z
        WHERE
            z.order_status_id = 6 AND
            z.user_id = 57 AND
            (
                SELECT process_id
                FROM orders_process
                WHERE
                    order_id = z.order_id AND
                    order_status_id = 7 AND
                    DATE(created_at) = DATE(z.created_at)
                LIMIT 1
            ) IS NOT NULL AND
            DATE(z.created_at) = a.db_date
    ) AS total_sale

FROM time_dimension a
WHERE a.db_date BETWEEN '2018-05-01 00:00:00' AND '2018-05-08 23:59:59'


-- query insert primary no Telepon
INSERT INTO `customer_phonenumber` (`customer_id`,`phonenumber`,`is_primary`)
SELECT `customer_id`,`telephone` AS phonenumber, 1 AS is_primary FROM `customer`
WHERE telephone IS NOT NULL
