/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `test` */

CREATE TABLE `test` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(512) DEFAULT NULL,
  `status` enum('active','disable','delete') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

/*Data for the table `test` */

insert  into `test`(`id`,`name`,`email`,`status`) values
  (10,'Jonah','dictum@pharetra.ca','disable'),
  (11,'Connor','congue.In.scelerisque@Integervulputaterisus.ca','disable'),
  (12,'Jessica','imperdiet.ornare@iaculisnec.com','delete'),
  (13,'Derek','sollicitudin@morbitristique.edu','disable'),
  (14,'Daniel','dui@at.com','disable'),
  (15,'Lev','id@laciniaSedcongue.ca','active'),
  (16,'Aquila','ac@accumsanconvallis.edu','disable'),
  (17,'Morgan','facilisis.vitae.orci@felispurusac.edu','delete'),
  (18,'Libby','porttitor@Etiamligula.ca','disable'),
  (19,'Brian','vitae.aliquam@sollicitudinadipiscingligula.ca','delete'),
  (20,'Uriel','ipsum.nunc@ametnulla.org','delete'),
  (21,'Azalia Two','at@enimconsequatpurus.ca','delete'),
  (22,'Karina','eu.eros@nonummy.org','disable'),
  (23,'Samuel','tellus@Seddiamlorem.org','delete'),
  (24,'Urielle','mattis.Integer@Donec.com','active'),
  (25,'Jamal','adipiscing.elit.Etiam@consectetueradipiscing.ca','disable'),
  (26,'Garrison','urna.Nullam@Quisque.org','delete'),
  (27,'Skyler','placerat.Cras.dictum@tempor.org','disable'),
  (28,'Alexa','Nullam.enim@lacusvariuset.edu','delete'),
  (29,'Zena','nec.leo@nislarcuiaculis.com','disable'),
  (30,'Mary','sit.amet@vehicularisusNulla.ca','active'),
  (31,'Raven','Donec@tellus.ca','active'),
  (32,'Leigh','sem@nonfeugiat.ca','disable'),
  (33,'Ginger','Integer.mollis.Integer@vitaeorci.edu','delete'),
  (34,'Leonard','neque@malesuadafames.ca','active'),
  (35,'Abdul','aliquam.arcu@tinciduntorci.org','disable'),
  (36,'Robin','lacus.Etiam.bibendum@lectus.com','delete'),
  (37,'Elaine','dis.parturient@Aeneansed.ca','disable'),
  (38,'Allistair','amet.metus@Mauris.com','disable'),
  (39,'Alika','Lorem@velquam.com','active'),
  (40,'Wylie','dis.parturient@dolornonummy.edu','disable'),
  (41,'Lareina','et.rutrum@mi.org','delete'),
  (42,'Aurelia','augue.porttitor@vitaevelit.com','active'),
  (43,'Ivor','vitae.semper.egestas@egestas.edu','disable'),
  (44,'Mikayla','Nunc.ullamcorper@orcisem.com','active'),
  (45,'Nola','eget.lacus@tristique.org','delete'),
  (46,'Angela','Etiam.imperdiet.dictum@rhoncusProinnisl.com','active'),
  (47,'Dante','egestas.Aliquam.fringilla@Curabiturdictum.org','active'),
  (48,'Sybill','mauris@sodales.com','disable'),
  (49,'Quentin','molestie.in@felisNullatempor.org','disable'),
  (50,'Hyacinth','egestas.a@vestibulumnec.org','delete')
  ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
