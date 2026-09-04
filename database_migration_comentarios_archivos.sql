-- Adjuntos de archivos en comentarios (PDF, Word, imágenes)
-- Añade columna `archivos` (JSON) a las dos tablas de comentarios.

ALTER TABLE `comentarios`
    ADD COLUMN `archivos` TEXT NULL DEFAULT NULL AFTER `comentario`;

ALTER TABLE `pase_punto_comentarios`
    ADD COLUMN `archivos` TEXT NULL DEFAULT NULL AFTER `comentario`,
    MODIFY `comentario` TEXT NULL DEFAULT NULL;
