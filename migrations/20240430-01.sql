CREATE TABLE _users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    password VARCHAR(255),
    project_name VARCHAR(255),
    token VARCHAR(24) NULL,
    token_expires_at TIMESTAMP NULL,
    email_validation_token VARCHAR(255) NULL,
    reset_password_token VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);