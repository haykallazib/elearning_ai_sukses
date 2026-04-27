CREATE DATABASE IF NOT EXISTS elearning_ai_final;
USE elearning_ai_final;

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    learning_style VARCHAR(20) DEFAULT NULL,
    visual_score FLOAT DEFAULT 0,
    auditory_score FLOAT DEFAULT 0,
    reading_score FLOAT DEFAULT 0,
    kinesthetic_score FLOAT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel hasil kuisioner
CREATE TABLE user_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    learning_style VARCHAR(20),
    scores JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel aktivitas
CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    style_target VARCHAR(20),
    type ENUM('video','teks','praktik'),
    content_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel log aktivitas user
CREATE TABLE user_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_id INT,
    style_before VARCHAR(20),
    score_performance INT CHECK (score_performance BETWEEN 0 AND 100),
    summary TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL
);

-- Tabel aturan klasifikasi (admin)
CREATE TABLE classification_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rule_name VARCHAR(50),
    visual_weight FLOAT DEFAULT 1,
    auditory_weight FLOAT DEFAULT 1,
    reading_weight FLOAT DEFAULT 1,
    kinesthetic_weight FLOAT DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel kuis AI yang dihasilkan
CREATE TABLE ai_generated_quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    topic VARCHAR(255) NOT NULL,
    num_questions INT NOT NULL,
    questions JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert data aktivitas contoh
INSERT INTO activities (title, style_target, type, content_url) VALUES
('Mind Mapping untuk Pemula', 'visual', 'video', 'https://www.youtube.com/embed/6Nn8Xp2O5mE'),
('Podcast Belajar Efektif', 'auditory', 'video', 'https://www.youtube.com/embed/9BtRr8wYFeQ'),
('Membaca Jurnal Interaktif', 'reading', 'teks', 'https://id.wikipedia.org/wiki/Belajar'),
('Praktik Simulasi Peran', 'kinesthetic', 'praktik', 'Lakukan role-play dengan temanmu. Catat hasilnya.');

-- Aturan default
INSERT INTO classification_rules (rule_name, visual_weight, auditory_weight, reading_weight, kinesthetic_weight)
VALUES ('default', 1, 1, 1, 1);