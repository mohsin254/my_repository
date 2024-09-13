-- Create the database
CREATE DATABASE IF NOT EXISTS factory_management;

-- Use the created database
USE factory_management;

-- Create the employees table
-- Create the employees table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    monthly_salary DECIMAL(10, 2) NOT NULL,
    per_hour_salary DECIMAL(10, 2) NOT NULL,
    cnic VARCHAR(15) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT
);

-- Create the attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    hours_worked DECIMAL(4, 2) NOT NULL,
    overtime_hours DECIMAL(4, 2) DEFAULT 0,
    money_taken DECIMAL(10, 2) DEFAULT 0,
    is_holiday BOOLEAN DEFAULT 0,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Create the products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL
);

-- Create the customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    shop_name VARCHAR(100),
    address TEXT,
    city VARCHAR(100),
    phone VARCHAR(15),
    category ENUM('motorbike', 'rikshaw', 'truck') NOT NULL
);

-- Create the sales table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL
);

-- Create the sale_details table
CREATE TABLE IF NOT EXISTS sale_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    per_piece_price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create the purchasing_items table
CREATE TABLE IF NOT EXISTS purchasing_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL UNIQUE
);

-- Create the purchase table
CREATE TABLE IF NOT EXISTS purchase (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL
);

-- Create the purchase_details table
CREATE TABLE IF NOT EXISTS purchase_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10, 2),
    per_piece_price DECIMAL(10, 2),
    weight DECIMAL(10, 2),
    per_kg_price DECIMAL(10, 2),
    amount DECIMAL(10, 2),
    FOREIGN KEY (purchase_id) REFERENCES purchase(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES purchasing_items(id) ON DELETE CASCADE
);

-- Create the stock_items table
CREATE TABLE IF NOT EXISTS stock_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) UNIQUE NOT NULL,
    category ENUM('Packing', 'Products') NOT NULL,
    sub_category VARCHAR(255) NOT NULL
);

-- Create the stock table
CREATE TABLE IF NOT EXISTS stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES stock_items(id) ON DELETE CASCADE
);
