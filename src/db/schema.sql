DROP TABLE IF EXISTS pr1orders;

CREATE TABLE `pr1orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` varchar(50) DEFAULT NULL,
  `payment_provider` varchar(20) DEFAULT NULL,
  `intent` varchar(20) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `amount` varchar(20) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
