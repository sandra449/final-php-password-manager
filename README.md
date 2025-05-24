# PHP Password Manager

A secure password management system built with PHP, implementing Object-Oriented Programming principles and modern security practices.

## Features

- User Authentication
- Secure Password Storage with AES Encryption
- Custom Password Generation
- Web-based Interface
- MySQL Database Integration

## Requirements

- PHP 8.3+
- MySQL/MariaDB
- PDO PHP Extension
- OpenSSL PHP Extension

## Installation

1. Clone the repository:
```bash
git clone https://github.com/sandra449/final-php-password-manager.git
```

2. Import the database schema:
```bash
mysql -u root -p < database.sql
```

3. Configure database connection in `config/database.php`

4. Start the PHP development server:
```bash
php -S localhost:8000
```

5. Access the application at `http://localhost:8000`

## Project Structure

- `classes/` - Contains PHP classes (User, PasswordGenerator, PasswordStorage)
- `config/` - Configuration files
- `database/` - Database schema and setup files
- `documentation/` - Project documentation and diagrams

## Security Features

- Password hashing using bcrypt
- AES encryption for stored passwords
- Secure session management
- SQL injection prevention using PDO
- XSS protection

## License

This project is created as part of an Object-Oriented Programming course. 