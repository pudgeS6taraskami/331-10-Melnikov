CREATE TABLE partners (
    id SERIAL PRIMARY KEY,
    type VARCHAR(20),
    name VARCHAR(100),
    director VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    inn VARCHAR(12),
    rating INTEGER
);

CREATE TABLE product_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50),
    coefficient FLOAT
);
