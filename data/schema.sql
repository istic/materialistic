
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pronoun` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar_id` int(11) DEFAULT NULL,
  `validated_stamp` datetime DEFAULT NULL,
  `validate_code` varchar(127) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_stamp` datetime DEFAULT NULL,
  `updated_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) DEFAULT '0',
  `reset_code` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_stamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mass_id` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seen` tinyint(4) DEFAULT '0',
  `type` varchar(15) COLLATE utf8_unicode_ci DEFAULT 'msg',
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_index` (`user_id`),
  CONSTRAINT `user_name` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;



create table `campaign` (
  `id` int auto_increment,
  `name` varchar(255) not null,
  `URL` varchar(255) not null,
  `target` double,
  `pledged` double,
  `date_start` datetime,
  `date_end` datetime null,
  `backer_count` int,
  `site` varchar(63),
  `date_checked` datetime,
  `status` ENUM('live', 'successful', 'failed', 'suspended', 'deleted', 'canceled'),
  `vitality` ENUM('Alive', 'Failed' ),
  `creator` varchar(255),
  `currency` varchar(3),
  `date_created` datetime,
  `date_modified` datetime,
  `category` varchar(63),
  `photo` varchar(255),
  `country` varchar(2),
  primary key (id)
);

create table `campaign_log` (
  `id` int auto_increment,
  `crowdfund_id` int,
  `date_created` datetime,
  `entry` varchar(255),
  primary key (id)
);


  

create table `pledge` (
  `id` int auto_increment,
  `campaign_id` int,
  `user_id` int,
  `backing_tier` varchar(255),
  `description` varchar(1023),
  `value` float,
  `is_delivered` enum("No", "Partially", "Yes"),
  `date_created` datetime,
  `date_modified` datetime,
  `date_promised` date,
  `date_reasonable` date,
  `date_delivered` date,
  primary key (id)
);


create table `pledge_log` (
  `id` int auto_increment,
  `pledge_id` int,
  `date_created` datetime,
  `entry` varchar(255),
  primary key (id)
);


create table `currency_conversion` (
  `id` int auto_increment,
  `date` date,
  `conversions` mediumtext,
  primary key (id)
)