-- Me gusta y Vistos en publicaciones y comentarios
-- publicacion_likes: reacciones "me gusta" a publicaciones (borradores)
-- publicacion_vistos: registro de lectura de publicaciones (quién y cuándo)
-- comentario_likes: reacciones "me gusta" a comentarios de la tabla `comentarios`
-- pase_comentario_likes: reacciones a comentarios de pases (pase_punto_comentarios)

CREATE TABLE IF NOT EXISTS `publicacion_likes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `publicacion_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_publicacion_usuario` (`publicacion_id`, `usuario_id`),
    KEY `idx_pl_usuario` (`usuario_id`),
    CONSTRAINT `fk_pl_publicacion` FOREIGN KEY (`publicacion_id`) REFERENCES `borradores` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pl_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `publicacion_vistos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `publicacion_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `visto_en` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_pv_publicacion_usuario` (`publicacion_id`, `usuario_id`),
    KEY `idx_pv_usuario` (`usuario_id`),
    CONSTRAINT `fk_pv_publicacion` FOREIGN KEY (`publicacion_id`) REFERENCES `borradores` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pv_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `comentario_likes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `comentario_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_comentario_usuario` (`comentario_id`, `usuario_id`),
    KEY `idx_cl_usuario` (`usuario_id`),
    CONSTRAINT `fk_cl_comentario` FOREIGN KEY (`comentario_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cl_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pase_comentario_likes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `comentario_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_pcl_comentario_usuario` (`comentario_id`, `usuario_id`),
    KEY `idx_pcl_usuario` (`usuario_id`),
    CONSTRAINT `fk_pcl_comentario` FOREIGN KEY (`comentario_id`) REFERENCES `pase_punto_comentarios` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pcl_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;