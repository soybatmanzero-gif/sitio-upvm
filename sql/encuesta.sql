DROP DATABASE IF EXISTS encuesta;
CREATE DATABASE encuesta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE encuesta;

CREATE TABLE respuestas (
    id_respuesta  INT          NOT NULL AUTO_INCREMENT,
    carrera       VARCHAR(80)  NOT NULL,   -- P1 (select)
    cuatrimestre  TINYINT      NOT NULL,   -- P2 (select)
    profesores    TINYINT      NOT NULL,   -- P3 (select 1-5)
    instalaciones VARCHAR(30)  NOT NULL,   -- P4 (radio)
    mejorar       VARCHAR(80)  NOT NULL,   -- P5 (select)
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_respuestas PRIMARY KEY (id_respuesta)
);

-- Registro de visitantes (login)
CREATE TABLE registros (
    id_registro INT          NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(100) NOT NULL,
    edad        TINYINT UNSIGNED NOT NULL,
    correo      VARCHAR(150) NOT NULL UNIQUE,
    sexo        VARCHAR(20)  NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_registros PRIMARY KEY (id_registro)
);
