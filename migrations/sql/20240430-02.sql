CREATE TABLE _collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    chain VARCHAR(255),
    config JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES _users(id)
);