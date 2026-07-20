-- Kettlewood Coffee Co. — Database Schema
-- Import this first: mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS kettlewood CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE kettlewood;

-- ---------------------------------------------------------
-- USERS
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(20) DEFAULT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin login -> email: admin@kettlewood.test / password: admin123
INSERT INTO
    users (
        name,
        email,
        password,
        is_admin
    )
VALUES (
        'Admin',
        'admin@kettlewood.test',
        '$2y$10$nxHJIIBfCnt1WzvMmeUjf.xnqtl2zqkixcv9LTFZgcqLjlwprbrT6',
        1
    );
-- password: admin123

-- ---------------------------------------------------------
-- CATEGORIES
-- ---------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO
    categories (name, slug)
VALUES (
        'Single Origin',
        'single-origin'
    ),
    ('Signature Blends', 'blends'),
    (
        'Subscriptions',
        'subscriptions'
    ),
    ('Brew Equipment', 'equipment');

-- ---------------------------------------------------------
-- PRODUCTS
-- ---------------------------------------------------------
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    sku VARCHAR(30) NOT NULL UNIQUE,
    price DECIMAL(10, 2) NOT NULL,
    compare_price DECIMAL(10, 2) DEFAULT NULL,
    origin VARCHAR(100) DEFAULT NULL,
    altitude VARCHAR(50) DEFAULT NULL,
    roast_level VARCHAR(30) DEFAULT NULL,
    tasting_notes VARCHAR(255) DEFAULT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT NULL,
    stock INT NOT NULL DEFAULT 0,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories (id)
);

INSERT INTO
    products (
        category_id,
        name,
        slug,
        sku,
        price,
        compare_price,
        origin,
        altitude,
        roast_level,
        tasting_notes,
        description,
        image,
        stock,
        is_featured
    )
VALUES (
        1,
        'Yirgacheffe Sundried',
        'yirgacheffe-sundried',
        'KW-ETH-01',
        1450.00,
        1650.00,
        'Yirgacheffe, Ethiopia',
        '1900–2200m',
        'Light',
        'Blueberry, jasmine, brown sugar',
        'A washed-then-sundried lot from smallholder farms around Yirgacheffe. Floral on the nose, syrupy in the cup, with a long blueberry finish.',
        'yirgacheffe.jpg',
        24,
        1
    ),
    (
        1,
        'Huila Reserve',
        'huila-reserve',
        'KW-COL-02',
        1350.00,
        NULL,
        'Huila, Colombia',
        '1700–1950m',
        'Medium',
        'Red apple, caramel, cocoa',
        'Grown on the volcanic slopes of Huila and fully washed. Balanced sweetness with a clean, cocoa-tinted finish that holds up well to milk.',
        'huila.jpg',
        30,
        1
    ),
    (
        1,
        'Yunnan Sunlight',
        'yunnan-sunlight',
        'KW-CHN-03',
        1500.00,
        NULL,
        'Pu''er, Yunnan, China',
        '1500–1800m',
        'Medium-Light',
        'Stone fruit, honey, oolong-like florality',
        'One of the rarer lots we carry — small-batch, honey-processed, with a tea-like body that surprises people expecting a "typical" coffee cup.',
        'yunnan.jpg',
        14,
        0
    ),
    (
        1,
        'Nyeri Peaberry',
        'nyeri-peaberry',
        'KW-KEN-04',
        1600.00,
        NULL,
        'Nyeri, Kenya',
        '1750–2000m',
        'Light-Medium',
        'Blackcurrant, tomato, bright acidity',
        'Peaberry-sorted lot from the Nyeri highlands. Vivid acidity up front that settles into a savoury, blackcurrant-jam sweetness.',
        'nyeri.jpg',
        18,
        1
    ),
    (
        2,
        'Kettlewood House Blend',
        'house-blend',
        'KW-BLD-05',
        1100.00,
        NULL,
        'Ethiopia + Brazil',
        '—',
        'Medium',
        'Milk chocolate, hazelnut, orange zest',
        'Our everyday blend — built for espresso and filter alike. Consistent, low-acid, and forgiving of a rushed morning grind.',
        'house-blend.jpg',
        60,
        1
    ),
    (
        2,
        'Midnight Roast',
        'midnight-roast',
        'KW-BLD-06',
        1150.00,
        NULL,
        'Brazil + India',
        '—',
        'Dark',
        'Dark chocolate, toasted walnut, molasses',
        'For the people who like their coffee to taste like coffee. A slow dark roast that holds body without turning bitter.',
        'midnight.jpg',
        45,
        0
    ),
    (
        3,
        'Explorer''s Subscription — Monthly',
        'explorer-subscription',
        'KW-SUB-07',
        2600.00,
        3200.00,
        'Rotating',
        '—',
        'Varies',
        'A new single origin every month',
        'One bag of a rotating single-origin lot delivered monthly, with a tasting card explaining the farm, process, and flavour notes.',
        'subscription.jpg',
        999,
        1
    ),
    (
        4,
        'Pour-Over Dripper (Ceramic)',
        'pour-over-dripper',
        'KW-EQ-08',
        2200.00,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        'A single-cup ceramic dripper with a flat bed for even extraction. Pairs with any 12–14cm paper filter.',
        'dripper.jpg',
        20,
        0
    ),
    (
        4,
        'Precision Gooseneck Kettle',
        'gooseneck-kettle',
        'KW-EQ-09',
        4200.00,
        4800.00,
        NULL,
        NULL,
        NULL,
        NULL,
        'Thin-spout stainless kettle for controlled pour-over pouring. 900ml capacity, stovetop and induction safe.',
        'kettle.jpg',
        12,
        1
    ),
    (
        4,
        'Hand Grinder — Burr Mill',
        'hand-grinder',
        'KW-EQ-10',
        5800.00,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        'Conical steel burrs with click-stop adjustment, covering everything from espresso to French press.',
        'grinder.jpg',
        9,
        0
    );

-- ---------------------------------------------------------
-- ORDERS
-- ---------------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) DEFAULT NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'cod',
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id)
);