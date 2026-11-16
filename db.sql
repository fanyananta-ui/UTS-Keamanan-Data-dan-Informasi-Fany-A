CREATE DATABASE IF NOT EXISTS db_enkripsi;
USE db_enkripsi;

CREATE TABLE IF NOT EXISTS pesan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  isi_enkripsi TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS kunci (
  id INT AUTO_INCREMENT PRIMARY KEY,
  public_key TEXT NOT NULL,
  enc_sym TEXT NOT NULL
);

-- username admin disimpan dalam bentuk hash (tidak bisa dibaca)
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
