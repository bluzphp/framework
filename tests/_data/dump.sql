/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE = '' */;

/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

/*Table structure for table `auth` */
CREATE TABLE auth
(
    userId      BIGINT UNSIGNED NOT NULL,
    provider    VARCHAR(64)                         NOT NULL,
    foreignKey  VARCHAR(255)                        NOT NULL,
    token       VARCHAR(64)                         NOT NULL,
    tokenSecret VARCHAR(64)                         NOT NULL,
    tokenType   CHAR(8)                             NOT NULL,
    created     TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated     TIMESTAMP,
    expired     TIMESTAMP,
    PRIMARY KEY (userId, provider)
);
/*Data for the table `auth` */
INSERT INTO `auth` (`userId`, `provider`, `foreignKey`, `token`, `tokenSecret`, `tokenType`, `created`)
VALUES (1, 'equals', 'admin', 'f9705d72d58b2a305ab6f5913ba60a61', 'secretsalt', 'access', '2012-11-09 07:40:46');

/*Table structure for table `test` */
CREATE TABLE `test`
(
    `id`      INT(11) NOT NULL AUTO_INCREMENT,
    `name`    VARCHAR(255) DEFAULT NULL,
    `email`   VARCHAR(512) DEFAULT NULL,
    `status`  ENUM ('active', 'disable', 'delete') DEFAULT NULL,
    `created` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `updated` TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 51
  DEFAULT CHARSET = utf8;

/*Data for the table `test` */
INSERT INTO `test` (`id`, `name`, `email`, `status`)
VALUES (1, 'Lareina', 'et.rutrum@mi.org', 'delete'),
       (2, 'Aurelia', 'augue.porttitor@vitaevelit.com', 'active'),
       (3, 'Ivor', 'vitae.semper.egestas@egestas.edu', 'disable'),
       (4, 'Mikayla', 'Nunc.ullamcorper@orcisem.com', 'active'),
       (5, 'Nola', 'eget.lacus@tristique.org', 'delete'),
       (6, 'Angela', 'Etiam.imperdiet.dictum@rhoncusProinnisl.com', 'active'),
       (7, 'Dante', 'egestas.Aliquam.fringilla@Curabiturdictum.org', 'active'),
       (8, 'Sybill', 'mauris@sodales.com', 'disable'),
       (9, 'Quentin', 'molestie.in@felisNullatempor.org', 'disable'),
       (10, 'Jonah', 'dictum@pharetra.ca', 'disable'),
       (11, 'Connor', 'congue.In.scelerisque@Integervulputaterisus.ca', 'disable'),
       (12, 'Jessica', 'imperdiet.ornare@iaculisnec.com', 'delete'),
       (13, 'Derek', 'sollicitudin@morbitristique.edu', 'disable'),
       (14, 'Daniel', 'dui@at.com', 'disable'),
       (15, 'Lev', 'id@laciniaSedcongue.ca', 'active'),
       (16, 'Aquila', 'ac@accumsanconvallis.edu', 'disable'),
       (17, 'Morgan', 'facilisis.vitae.orci@felispurusac.edu', 'delete'),
       (18, 'Libby', 'porttitor@Etiamligula.ca', 'disable'),
       (19, 'Brian', 'vitae.aliquam@sollicitudinadipiscingligula.ca', 'delete'),
       (20, 'Uriel', 'ipsum.nunc@ametnulla.org', 'delete'),
       (21, 'Azalia Two', 'at@enimconsequatpurus.ca', 'delete'),
       (22, 'Karina', 'eu.eros@nonummy.org', 'disable'),
       (23, 'Samuel', 'tellus@Seddiamlorem.org', 'delete'),
       (24, 'Urielle', 'mattis.Integer@Donec.com', 'active'),
       (25, 'Jamal', 'adipiscing.elit.Etiam@consectetueradipiscing.ca', 'disable'),
       (26, 'Garrison', 'urna.Nullam@Quisque.org', 'delete'),
       (27, 'Skyler', 'placerat.Cras.dictum@tempor.org', 'disable'),
       (28, 'Alexa', 'Nullam.enim@lacusvariuset.edu', 'delete'),
       (29, 'Zena', 'nec.leo@nislarcuiaculis.com', 'disable'),
       (30, 'Mary', 'sit.amet@vehicularisusNulla.ca', 'active'),
       (31, 'Raven', 'Donec@tellus.ca', 'active'),
       (32, 'Leigh', 'sem@nonfeugiat.ca', 'disable'),
       (33, 'Ginger', 'Integer.mollis.Integer@vitaeorci.edu', 'delete'),
       (34, 'Leonard', 'neque@malesuadafames.ca', 'active'),
       (35, 'Abdul', 'aliquam.arcu@tinciduntorci.org', 'disable'),
       (36, 'Robin', 'lacus.Etiam.bibendum@lectus.com', 'delete'),
       (37, 'Elaine', 'dis.parturient@Aeneansed.ca', 'disable'),
       (38, 'Allistair', 'amet.metus@Mauris.com', 'disable'),
       (39, 'Alika', 'Lorem@velquam.com', 'active'),
       (40, 'Wylie', 'dis.parturient@dolornonummy.edu', 'disable'),
       (41, 'Hyacinth', 'egestas.a@vestibulumnec.org', 'delete'),
       (42, 'Getman', 'getman@gov.ua', 'active');

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;
