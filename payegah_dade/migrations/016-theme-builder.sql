ALTER TABLE block_pages ADD COLUMN name VARCHAR(120) DEFAULT NULL AFTER page_type;
ALTER TABLE block_pages ADD COLUMN condition_type VARCHAR(20) NOT NULL DEFAULT 'single' AFTER name;
ALTER TABLE block_pages ADD COLUMN condition_value VARCHAR(120) NOT NULL DEFAULT '' AFTER condition_type;
