CREATE USER IF NOT EXISTS 'password_manager_user'@'localhost' IDENTIFIED BY 'secure_password123';
GRANT ALL PRIVILEGES ON password_manager.* TO 'password_manager_user'@'localhost';
FLUSH PRIVILEGES; 