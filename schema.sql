CREATE DATABASE IF NOT EXISTS greek_recipe_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE greek_recipe_hub;

-- 1. Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(80) NOT NULL,
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Recipes Table
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    category_id INT NOT NULL, 
    title VARCHAR(120) NOT NULL, 
    description TEXT NOT NULL, 
    steps TEXT NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Ingredients Table
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    recipe_id INT NOT NULL, 
    name VARCHAR(100) NOT NULL, 
    amount DECIMAL(8,2) NOT NULL DEFAULT 0, 
    unit VARCHAR(30) NOT NULL DEFAULT '',
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Comments Table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    recipe_id INT NOT NULL, 
    user_id INT NOT NULL, 
    body TEXT NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Favorites Table
CREATE TABLE favorites (
    user_id INT NOT NULL, 
    recipe_id INT NOT NULL, 
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Initial Data
INSERT IGNORE INTO categories (name) VALUES
    ('Meze (Appetizers)'),
    ('Salads'),
    ('Seafood'),
    ('Sweets');