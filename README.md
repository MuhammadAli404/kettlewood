# Kettlewood Coffee Co. — Full-Stack Ecommerce Store (PHP + MySQL)

A complete, tested ecommerce store: product catalog, cart, checkout, order
storage, customer accounts, and an admin panel — all in plain PHP + MySQL
(no framework needed).

## What's included
- **Storefront**: home page, shop with category filters, product detail pages, cart, checkout, order confirmation
- **Accounts**: register / login / logout (guest checkout also works — no account required)
- **Admin panel**: dashboard with revenue/order stats, order status management, add/delete products
- **10 dummy products** already loaded across 4 categories (Single Origin, Blends, Subscriptions, Equipment)
- Session-based cart, stock auto-decrements on order, free-shipping threshold logic

This was built and **tested end-to-end** (add to cart → checkout → order saved
to database → stock reduced) before being handed to you.

## Setup (XAMPP / Laragon / any local PHP+MySQL stack)

1. Copy the whole `kettlewood` folder into your server's web root
   (e.g. `htdocs/kettlewood` for XAMPP).
2. Create the database by importing the schema:
   ```
   mysql -u root -p < schema.sql
   ```
   or open phpMyAdmin → Import → select `schema.sql`.
3. Open `config.php` and set your DB username/password if they aren't the
   default XAMPP values (`root` / empty password).
4. Visit `http://localhost/kettlewood/index.php` in your browser.

## Logins
- **Admin panel**: `http://localhost/kettlewood/admin/login.php`
  Email: `admin@kettlewood.test` — Password: `admin123`
- **Customer account**: use "Create account" on the login page, or just
  check out as a guest — both work.

## Folder structure
```
kettlewood/
├── config.php              # DB credentials — edit this first
├── schema.sql               # Import this into MySQL to create tables + dummy data
├── index.php                 # Homepage
├── shop.php                  # Product listing + category filter
├── product.php                # Product detail + add to cart
├── cart.php                    # View/update/remove cart items
├── checkout.php                 # Shipping form → places order
├── order_success.php             # Order confirmation
├── login.php / register.php / logout.php
├── admin/
│   ├── login.php              # Admin login
│   ├── index.php               # Dashboard (orders, revenue, stock)
│   └── products.php             # Add / delete products
├── includes/
│   ├── header.php, footer.php   # Shared layout
│   └── functions.php            # Cart logic, auth helpers, formatting
└── css/style.css               # All styling — one file, no build step
```

## Adding your own products
Easiest way: log into `/admin/products.php` and use the "Add new product"
form. For bulk edits, edit the `INSERT INTO products` block in `schema.sql`
directly, or add rows via phpMyAdmin.

## Adding real product photos
Drop image files into the `uploads/` folder and set the `image` column for
that product to the filename (e.g. `yirgacheffe.jpg`). Until you do, every
product automatically shows a clean placeholder so the site never looks broken.

## Notes on going to production
This is a learning/demo-grade build — solid for local use, a portfolio, or
a starting point. Before taking real payments or going live, you'd want to:
- Add a real payment gateway (currently: Cash on Delivery / bank transfer only)
- Move DB credentials out of the repo (environment variables)
- Add CSRF tokens to forms and rate-limit login attempts
- Switch from mysqli to PDO with prepared statements throughout (currently
  uses `mysqli_real_escape_string` for input sanitization, which is safe but
  older style)
