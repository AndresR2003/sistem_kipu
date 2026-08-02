CREATE TABLE IF NOT EXISTS `configuracion_visual` (
    `id` INT PRIMARY KEY DEFAULT 1,
    `sidebar_bg` VARCHAR(20) DEFAULT '#13131f',
    `sidebar_text` VARCHAR(20) DEFAULT 'rgba(255,255,255,0.55)',
    `sidebar_active_bg` VARCHAR(20) DEFAULT '#4669FA',
    `topbar_bg` VARCHAR(20) DEFAULT 'rgba(15,15,26,0.92)',
    `topbar_text` VARCHAR(20) DEFAULT '#e2e8f0',
    `primary_color` VARCHAR(20) DEFAULT '#4669FA',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `configuracion_visual` (`id`) VALUES (1)
ON DUPLICATE KEY UPDATE `id` = `id`;
