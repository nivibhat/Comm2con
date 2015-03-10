SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `hci573` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `hci573` ;

-- -----------------------------------------------------
-- Table `hci573`.`community_connect_location`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_location` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_location` (
  `e_loc_id` BIGINT NOT NULL AUTO_INCREMENT,
  `city` VARCHAR(100) NOT NULL DEFAULT '',
  `zipcode` VARCHAR(45) NOT NULL DEFAULT '',
  `state` VARCHAR(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`e_loc_id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `zipcode_UNIQUE` ON `hci573`.`community_connect_location` (`zipcode` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_community`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_community` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_community` (
  `community_id` BIGINT NOT NULL AUTO_INCREMENT,
  `community_name` VARCHAR(200) NOT NULL DEFAULT '',
  `Community_desc` VARCHAR(256) NULL,
  PRIMARY KEY (`community_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_user` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_user` (
  `user_id` BIGINT NOT NULL AUTO_INCREMENT,
  `md5_id` VARCHAR(200) NOT NULL,
  `first_name` VARCHAR(50) NULL,
  `last_name` VARCHAR(50) NULL,
  `profile_picture` VARCHAR(256) NULL,
  `email` LONGBLOB NOT NULL,
  `phone` VARCHAR(220) NULL,
  `username` VARCHAR(45) NOT NULL,
  `user_password` VARCHAR(45) NOT NULL,
  `user_level` TINYINT NOT NULL DEFAULT '1',
  `registration_date` DATE NOT NULL,
  `user_ip` VARCHAR(200) NOT NULL,
  `approved` TINYINT NOT NULL DEFAULT '0',
  `activation_code` INT(10) NOT NULL DEFAULT '0',
  `ckey` VARCHAR(250) NOT NULL,
  `ctime` VARCHAR(250) NOT NULL,
  `num_logins` INT(11) NOT NULL DEFAULT '0',
  `last_login` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `e_loc_id` BIGINT NULL,
  `community_id` BIGINT NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_user_location`
    FOREIGN KEY (`e_loc_id`)
    REFERENCES `hci573`.`community_connect_location` (`e_loc_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_community`
    FOREIGN KEY (`community_id`)
    REFERENCES `hci573`.`community_connect_community` (`community_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `e_loc_id_idx` ON `hci573`.`community_connect_user` (`e_loc_id` ASC);

CREATE INDEX `fk_community_id_idx` ON `hci573`.`community_connect_user` (`community_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_venue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_venue` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_venue` (
  `venue_id` BIGINT NOT NULL AUTO_INCREMENT,
  `venue_name` VARCHAR(45) NOT NULL DEFAULT '',
  `venue_address` VARCHAR(200) NOT NULL DEFAULT '',
  `venue_phone` VARCHAR(45) NOT NULL DEFAULT '',
  `venue_email` LONGBLOB NULL,
  `venue_owner` VARCHAR(45) NULL,
  `e_loc_id` BIGINT NOT NULL,
  PRIMARY KEY (`venue_id`),
  CONSTRAINT `fk_venue_location`
    FOREIGN KEY (`e_loc_id`)
    REFERENCES `hci573`.`community_connect_location` (`e_loc_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
COMMENT = '				';

CREATE INDEX `fk_venue_location_idx` ON `hci573`.`community_connect_venue` (`e_loc_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_event_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_event_type` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_event_type` (
  `e_type_id` BIGINT NOT NULL AUTO_INCREMENT,
  `event_type` VARCHAR(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`e_type_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_event_recurrence`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_event_recurrence` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_event_recurrence` (
  `e_recurring_id` BIGINT NOT NULL AUTO_INCREMENT,
  `event_frequency` VARCHAR(45) NULL,
  `recurrence_end` VARCHAR(45) NULL,
  `day_of_week` VARCHAR(45) NULL,
  PRIMARY KEY (`e_recurring_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_event`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_event` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_event` (
  `event_id` BIGINT NOT NULL AUTO_INCREMENT,
  `event_name` VARCHAR(200) NOT NULL DEFAULT '',
  `event_date` DATE NOT NULL,
  `event_desc` VARCHAR(256) NOT NULL DEFAULT '',
  `event_status` TINYINT NOT NULL DEFAULT '0',
  `event_scope` VARCHAR(200) NOT NULL DEFAULT 'public',
  `e_type_id` BIGINT NOT NULL,
  `user_id` BIGINT NOT NULL,
  `venue_id` BIGINT NOT NULL,
  `community_id` BIGINT NOT NULL,
  `e_recurring_id` BIGINT NULL,
  PRIMARY KEY (`event_id`),
  CONSTRAINT `fk_event_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `hci573`.`community_connect_user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_event_venue`
    FOREIGN KEY (`venue_id`)
    REFERENCES `hci573`.`community_connect_venue` (`venue_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_event_type`
    FOREIGN KEY (`e_type_id`)
    REFERENCES `hci573`.`community_connect_event_type` (`e_type_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_event_community`
    FOREIGN KEY (`community_id`)
    REFERENCES `hci573`.`community_connect_community` (`community_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_event_recurrence_id`
    FOREIGN KEY (`e_recurring_id`)
    REFERENCES `hci573`.`community_connect_event_recurrence` (`e_recurring_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `user_id_idx` ON `hci573`.`community_connect_event` (`user_id` ASC);

CREATE INDEX `venue_id_idx` ON `hci573`.`community_connect_event` (`venue_id` ASC);

CREATE INDEX `e_type_id_idx` ON `hci573`.`community_connect_event` (`e_type_id` ASC);

CREATE INDEX `fk_community_id_idx` ON `hci573`.`community_connect_event` (`community_id` ASC);

CREATE INDEX `fk_event_recurrence_id_idx` ON `hci573`.`community_connect_event` (`e_recurring_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_chef`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_chef` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_chef` (
  `chef_id` BIGINT NOT NULL AUTO_INCREMENT,
  `about_chef` VARCHAR(255) NULL,
  `contact_time_preference` VARCHAR(45) NULL,
  `payments_accepted` VARCHAR(45) NULL,
  `delivery_available` VARCHAR(45) NULL,
  `pickup_available` VARCHAR(45) NULL,
  `taking_offline_order` VARCHAR(45) NULL,
  `user_id` BIGINT NOT NULL,
  `community_id` BIGINT NOT NULL,
  PRIMARY KEY (`chef_id`),
  CONSTRAINT `fk_chef_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `hci573`.`community_connect_user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_chef_community`
    FOREIGN KEY (`community_id`)
    REFERENCES `hci573`.`community_connect_community` (`community_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_chef_user_idx` ON `hci573`.`community_connect_chef` (`user_id` ASC);

CREATE INDEX `fk_community_id_idx` ON `hci573`.`community_connect_chef` (`community_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_food`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_food` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_food` (
  `food_id` BIGINT NOT NULL AUTO_INCREMENT,
  `food_name` VARCHAR(45) NOT NULL DEFAULT '',
  `food_description` VARCHAR(256) NOT NULL DEFAULT '',
  `availability` VARCHAR(45) NULL,
  `food_picture` VARCHAR(256) NULL,
  `community_id` BIGINT NOT NULL,
  PRIMARY KEY (`food_id`),
  CONSTRAINT `fk_food_community`
    FOREIGN KEY (`community_id`)
    REFERENCES `hci573`.`community_connect_community` (`community_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_community_type_id_idx` ON `hci573`.`community_connect_food` (`community_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_food_chef_details`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_food_chef_details` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_food_chef_details` (
  `F_C_det_id` INT NOT NULL AUTO_INCREMENT,
  `chef_id` BIGINT NULL,
  `food_id` BIGINT NULL,
  `food_price` VARCHAR(45) NULL,
  PRIMARY KEY (`F_C_det_id`),
  CONSTRAINT `fk_food_chef_details_chef`
    FOREIGN KEY (`chef_id`)
    REFERENCES `hci573`.`community_connect_chef` (`chef_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_food_chef_details_food`
    FOREIGN KEY (`food_id`)
    REFERENCES `hci573`.`community_connect_food` (`food_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `chef_id_idx` ON `hci573`.`community_connect_food_chef_details` (`chef_id` ASC);

CREATE INDEX `food_id_idx` ON `hci573`.`community_connect_food_chef_details` (`food_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_event_picture`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_event_picture` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_event_picture` (
  `e_pic_id` BIGINT NOT NULL AUTO_INCREMENT,
  `image_location` VARCHAR(256) NOT NULL DEFAULT '',
  `event_id` BIGINT NOT NULL,
  PRIMARY KEY (`e_pic_id`),
  CONSTRAINT `fk_event_picture_event`
    FOREIGN KEY (`event_id`)
    REFERENCES `hci573`.`community_connect_event` (`event_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `event_id_idx` ON `hci573`.`community_connect_event_picture` (`event_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_user_saved_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_user_saved_info` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_user_saved_info` (
  `saved_info` BIGINT NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT NOT NULL,
  `event_id` BIGINT NULL,
  `chef_id` BIGINT NULL,
  `contact_id` BIGINT NULL,
  PRIMARY KEY (`saved_info`),
  CONSTRAINT `fk_user_saved_info_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `hci573`.`community_connect_user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_saved_info_event`
    FOREIGN KEY (`event_id`)
    REFERENCES `hci573`.`community_connect_event` (`event_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_saved_info_chef`
    FOREIGN KEY (`chef_id`)
    REFERENCES `hci573`.`community_connect_chef` (`chef_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_saved_info_contact`
    FOREIGN KEY (`contact_id`)
    REFERENCES `hci573`.`community_connect_user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `user_id_idx` ON `hci573`.`community_connect_user_saved_info` (`user_id` ASC);

CREATE INDEX `event_id_idx` ON `hci573`.`community_connect_user_saved_info` (`event_id` ASC);

CREATE INDEX `chef_id_idx` ON `hci573`.`community_connect_user_saved_info` (`chef_id` ASC);

CREATE INDEX `contact_id_idx` ON `hci573`.`community_connect_user_saved_info` (`contact_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_event_attendance`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_event_attendance` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_event_attendance` (
  `event_attendance_id` BIGINT NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT NOT NULL,
  `event_id` BIGINT NOT NULL,
  PRIMARY KEY (`event_attendance_id`),
  CONSTRAINT `fk_event_attendance_event`
    FOREIGN KEY (`event_id`)
    REFERENCES `hci573`.`community_connect_event` (`event_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_event_attendance_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `hci573`.`community_connect_user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_event_attendance_event_idx` ON `hci573`.`community_connect_event_attendance` (`event_id` ASC);

CREATE INDEX `fk_event_attendance_user_idx` ON `hci573`.`community_connect_event_attendance` (`user_id` ASC);


-- -----------------------------------------------------
-- Table `hci573`.`community_connect_pstore`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hci573`.`community_connect_pstore` ;

CREATE TABLE IF NOT EXISTS `hci573`.`community_connect_pstore` (
  `p_id` INT NOT NULL,
  `p_email` LONGBLOB NOT NULL,
  `p_pass` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`p_id`));


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
