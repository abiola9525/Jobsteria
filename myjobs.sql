
CREATE DATABASE IF NOT EXISTS sys_freelance;
USE sys_freelance;


DROP TABLE IF EXISTS `user_project_award`;
DROP TABLE IF EXISTS `user_project`;
DROP TABLE IF EXISTS `bid`;
DROP TABLE IF EXISTS `project`;
DROP TABLE IF EXISTS `private_message`;
DROP TABLE IF EXISTS `credit_card`;
DROP TABLE IF EXISTS `transaction`;
DROP TABLE IF EXISTS `county_city_info`;
DROP TABLE IF EXISTS `state_table`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `category`;
DROP TABLE IF EXISTS `project_category`;

/**
 * Below is the data definition for a bidding system.
 */


CREATE TABLE `user` (
    `uid` int not null auto_increment primary key,
    `name` varchar(64),
    `username` varchar(64),
    `password` varchar(64),
    `add_ts` timestamp default current_timestamp,
    `update_ts` timestamp default current_timestamp,
    `address` varchar(64),
    `birth_date` date,
    `zipcode` varchar(12),
    `active` varchar(1),
    `email` varchar(64),
    `state_prov` varchar(64),
    `city` varchar(64),
    `country` varchar(64),
    `avitar` varchar(64),
    `phone` varchar(64),
    `gender` char,
    `resume_file_loc` varchar(64),
    `locked` char,
    `banned` char,
    `ip` varchar(32),
    `failed_login_count` int default 0,
    `accepted_terms` char,
    `customer_id` varchar(256),
	`recipient_id` varchar(255)
) ENGINE=InnoDB;



CREATE TABLE `project` (
    `pid` int not null auto_increment primary key,
    `name` varchar(64),
    `description` varchar(1000),
    `budget_upper_bound` int,
    `budget_lower_bound` int,
    `currency_type` varchar(12),
    `status` varchar(12),
    `add_ts` timestamp default current_timestamp,
    `update_ts` timestamp default current_timestamp,
    `job_start_date` date,
    `job_projected_end_date` date,
    `end_bid_date` timestamp,
    `deleted` char,
    `remote` char,
    `project_country` varchar(64),
    `project_state` varchar(64),
    `project_city` varchar(64),
    `project_address` varchar(64),
    `hourly` char
) ENGINE=InnoDB;

CREATE TABLE `bid` (
    `uid` int not null,
    `pid` int not null,
    `message` varchar(1000),
    `start_date` date,
    `end_date` date,
    `add_ts` timestamp default current_timestamp,
    `update_ts` timestamp default current_timestamp,
    `amount` double not null,
    `charge` double,
    `milestone` int,
    foreign key (`uid`)
        references `user` (`uid`),
    foreign key (`pid`)
        references `project` (`pid`)
) ENGINE=InnoDB;

CREATE TABLE `user_project` (
    `uid` int not null,
    `pid` int not null,
    `add_ts` timestamp default current_timestamp,
    `update_ts` timestamp default current_timestamp,
    foreign key (`uid`)
        references `user` (`uid`),
    foreign key (`pid`)
        references `project` (`pid`)
) ENGINE=InnoDB;

CREATE TABLE `user_project_award` (
    `uid` int not null,
    `pid` int not null,
    `add_ts` timestamp default current_timestamp,
    `update_ts` timestamp default current_timestamp,
    `accepted` char,
    `milestone_request_reject_count` int,
    `milestone_request` char,
    `milestone_request_accepted` char,
    `project_complete_request` char,
    `project_complete_request_reject_count` int,
    foreign key (`uid`)
        references `user` (`uid`),
    foreign key (`pid`)
        references `project` (`pid`)
) ENGINE=InnoDB;


CREATE TABLE `private_message` (
    `from_uid` int not null,
    `to_uid` int not null,
    `subject` varchar(64),
    `message` varchar(1024),
    `date` date,
    `add_ts` timestamp default current_timestamp,
    `update_ts` timestamp default current_timestamp,
    `deleted` char
) ENGINE=InnoDB;

CREATE TABLE `state_table` (
    `state_code` varchar(2) not null primary key,
    `state_name` varchar(64),
    `add_ts` timestamp default current_timestamp,
    `udpate_ts` timestamp default current_timestamp
) ENGINE=InnoDB;

CREATE TABLE `county_city_info` (
    `state_code` varchar(2),
    `zipcode` varchar(12),
    `zipclass` varchar(12),
    `longitude` double,
    `latitude` double,
    `city` varchar(64),
    `county` varchar(64),
    `add_ts` timestamp default current_timestamp,
    `update_ts` timestamp default current_timestamp,
    foreign key (`state_code`)
        references `state_table` (`state_code`)
) ENGINE=InnoDB;


CREATE TABLE `credit_card` (
    `uid` int not null,
    `merchant_id` varchar(24),
    `merchant_ref_code` varchar(32),
    `card_account_number` varchar(24),
    `card_expiration_month` int,
    `card_expiration_year` int,
    `card_last_four_digits` varchar(4),
    `card_name` varchar(128),
    `card_security_id` varchar(3),
    foreign key (`uid`)
        references `user` (`uid`)
) ENGINE=InnoDB;


CREATE TABLE `transaction` (
    `tid` bigint not null auto_increment primary key,
    `amount` double,
    `type` varchar(20),
    `status` varchar(24),
    `date` date,
    `pid` int not null,
    `from_uid` int not null,
    `to_uid` int,
    `add_ts` timestamp default current_timestamp,
    `update_ts` timestamp default current_timestamp
) ENGINE=InnoDB;


create table `category` (
    `cid` int not null primary key auto_increment,
    `parent_cid` int,
    `name` varchar(64),
    `description` varchar(256),
    `add_ts` timestamp,
    `update_ts` timestamp
) ENGINE=InnoDB;

create table `project_category` (
    `pid` int not null,
    `cid` int not null,
    `add_ts` timestamp,
    `update_ts` timestamp,
    foreign key (`pid`)
        references `project` (`pid`),
    foreign key (`cid`)
        references `category` (`cid`)
) ENGINE=InnoDB;

create table `rating` (
    rid int not null primary key auto_increment,
    from_uid int not null,
    to_uid int not null,
    pid int not null,
    rating_type varchar(24),
    professional_rate smallint,
    professional_comments varchar(256),
    experienced_rate smallint,
    experienced_comments varchar(256),
    communication_rate smallint,
    communication_comments varchar(256),
    skill_rate smallint,
    skill_comments varchar(256),
    quality_rate smallint,
    quality_comments varchar(256),
    foreign key (`from_uid`)
        references `user` (`uid`),
    foreign key (`to_uid`)
        references `user` (`uid`),
    foreign key (`pid`)
        references `project` (`pid`)
) ENGINE=InnoDB;

INSERT INTO category VALUES (default, NULL, "Software engineering", "A job where one must use a programming language to create software", default, default),
							(default, NULL, "Blogging", "Creating a weblog of a particular subject", default, default);


USE sys_freelance;
describe user;
UPDATE `user_project_award` SET `accepted`='N' WHERE `pid`=4;

ALTER TABLE `user` ADD COLUMN `recipient_id` varchar(255);
