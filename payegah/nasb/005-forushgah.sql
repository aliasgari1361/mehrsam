CREATE TABLE IF NOT EXISTS mahsul_dasteh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    onvan VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    tartib INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mahsulat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    onvan VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    dasteh_id INT,
    gheymat DECIMAL(12,0) NOT NULL DEFAULT 0,
    gheymat_takhfif DECIMAL(12,0) DEFAULT NULL,
    tozih TEXT,
    virayesh TEXT,
    tasvir VARCHAR(500) DEFAULT NULL,
    mojood INT NOT NULL DEFAULT 0,
    vaziat TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dasteh_id) REFERENCES mahsul_dasteh(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sabad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    karbar_id INT DEFAULT NULL,
    session_id VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_karbar (karbar_id),
    INDEX idx_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sabad_mahsul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sabad_id INT NOT NULL,
    mahsul_id INT NOT NULL,
    tedad INT NOT NULL DEFAULT 1,
    gheymat_vahed DECIMAL(12,0) NOT NULL,
    FOREIGN KEY (sabad_id) REFERENCES sabad(id) ON DELETE CASCADE,
    FOREIGN KEY (mahsul_id) REFERENCES mahsulat(id) ON DELETE CASCADE,
    UNIQUE KEY unique_sabad_mahsul (sabad_id, mahsul_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sefaresh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    karbar_id INT DEFAULT NULL,
    onvan_girande VARCHAR(200) NOT NULL,
    telefon_girande VARCHAR(50) NOT NULL,
    ostan VARCHAR(100) NOT NULL,
    shahr VARCHAR(100) NOT NULL,
    adres TEXT NOT NULL,
    kode_posty VARCHAR(20) DEFAULT NULL,
    post_type VARCHAR(50) DEFAULT 'pishaz',
    post_hazine DECIMAL(12,0) DEFAULT 0,
    tozih TEXT,
    majmoo_gheymat DECIMAL(12,0) NOT NULL,
    vaziat ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    pardakht_vaziat ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
    pardakht_ref_id VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_karbar (karbar_id),
    INDEX idx_vaziat (vaziat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sefaresh_mahsul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sefaresh_id INT NOT NULL,
    mahsul_id INT NOT NULL,
    tedad INT NOT NULL,
    gheymat_vahed DECIMAL(12,0) NOT NULL,
    FOREIGN KEY (sefaresh_id) REFERENCES sefaresh(id) ON DELETE CASCADE,
    FOREIGN KEY (mahsul_id) REFERENCES mahsulat(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
