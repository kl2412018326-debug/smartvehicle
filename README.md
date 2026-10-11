# AutoCare — Smart Vehicle Service Maintenance

A PHP and MySQL prototype based on the proposal. It includes individual customer, mechanic, and administrator accounts with editable personal profiles; vehicle garage; service booking and status tracking; administrator mechanic assignment; mechanic-only assigned-job and findings views; workshop inventory; rule based health snapshots; appointment cost estimates; invoices; QR linked service records; and expiring password reset links.

## Requirements

- PHP 8.1+ with PDO MySQL enabled
- MySQL 8+ or MariaDB
- A local web server such as XAMPP, WAMP, or PHP's development server
- Internet access for Google Fonts and the QR rendering library

## Setup with XAMPP

1. Copy the complete `smart-vehicle-service` folder into `xampp/htdocs/` (or rename it to `autocare`).
2. Start Apache and MySQL in the XAMPP control panel.
3. Open phpMyAdmin, import `database.sql`.
4. If your MySQL credentials differ from the defaults, edit `config.php` (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
5. Visit `http://localhost/autocare/` (or use the folder name you kept) and create an account. Public registration creates customer accounts.
6. Promote your first account to admin in phpMyAdmin: `UPDATE users SET role='admin' WHERE email='your@email.example';` Sign out and back in. Admins can create mechanic and additional administrator accounts from **Team accounts**.
7. For an existing database, run `upgrade_password_resets.sql` once in phpMyAdmin to add the reset-token table.

## Password reset email setup

Set `APP_URL` in `config.php` to the full site URL, including the folder path if installed in a subfolder (for example `https://your-site.example/autocare`). Configure SMTP host, port, username, password, encryption, and an authorized `MAIL_FROM` sender in the same file. Run `composer install` in this folder locally, then upload the generated `vendor/` directory. InfinityFree free hosting restricts PHP `mail()`, so SMTP and PHPMailer are required there. Reset tokens are random, stored hashed, expire after one hour, and are single-use.

Never commit or share real SMTP credentials. If credentials are placed directly in `config.php`, keep that file private and do not include the filled version in a public source repository.

## Roles

- **Customer:** own vehicles, book appointments, see service records and vehicle health.
- **Mechanic:** view only assigned jobs and their status, update assigned work, and review service findings and parts used.
- **Administrator:** workshop visibility, staff accounts, service workflows, and parts inventory.

## Database

`database.sql` creates users, vehicles, appointments, service records, invoices, parts, and password reset token tables. Admins can assign appointment vehicles to mechanic accounts. Users in every role can edit their name, email, and phone, and can change their password after entering their current password. Passwords are stored with PHP's `password_hash`; all application database operations use PDO prepared statements. Customer registration cannot select a staff role.

## Prototype notes

The health score uses recorded service age, mileage, and next-service mileage as explainable signals. It is not a trained AI diagnostic model and does not replace a mechanic's inspection. Cost estimates are customer-entered planning amounts until the workshop confirms a quote. QR codes link to a service-record page that requires the vehicle owner's account or workshop staff access. Invoices have a database schema; payment processing and invoice management screens are future extensions.

For production deployment, configure HTTPS, strong database credentials, backups, mail delivery for reminders, and appropriate privacy/security review.
