CREATE TABLE IF NOT EXISTS `users` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`password` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`permissions` TEXT NULL COLLATE 'utf8_unicode_ci',
	`last_login` TIMESTAMP NULL DEFAULT NULL,
	`first_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`last_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `users_email_unique` (`email`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
CREATE TABLE IF NOT EXISTS `roles` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`slug` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`name` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`permissions` TEXT NULL COLLATE 'utf8_unicode_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `roles_slug_unique` (`slug`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `role_users` (
	`user_id` INT(10) UNSIGNED NOT NULL,
	`role_id` INT(10) UNSIGNED NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`user_id`, `role_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `persistences` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`code` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `persistences_code_unique` (`code`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `reminders` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`code` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`completed` TINYINT(1) NOT NULL DEFAULT '0',
	`completed_at` TIMESTAMP NULL DEFAULT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `activations` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`code` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`completed` TINYINT(1) NOT NULL DEFAULT '0',
	`completed_at` TIMESTAMP NULL DEFAULT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

 
INSERT INTO `activations` (`id`, `user_id`, `code`, `completed`, `completed_at`, `created_at`, `updated_at`) VALUES
	(1, 1, 'gZgKRQhqMLy2VS1Kv9S1Lt391VlRRImU', 1, NULL, '2016-12-02 00:10:36', '2016-12-02 00:10:36');

INSERT INTO `roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'Admin', '{"users.create":true,"users.delete":true,"users.view":true,"users.update":true,"trucks.create":true,"trucks.update":true,"trucks.delete":true,"trucks.view":true,"checklist.create":true,"checklist.update":true,"checklist.delete":true,"checklist.view":true,"checkout_messages.create":true,"checkout_messages.update":true,"checkout_messages.delete":true,"checkout_messages.view":true,"departments.create":true,"departments.update":true,"departments.delete":true,"departments.view":true,"fuel_record.create":true,"fuel_record.update":true,"fuel_record.delete":true,"fuel_record.view":true,"fuel_types.create":true,"fuel_types.update":true,"fuel_types.delete":true,"fuel_types.view":true,"mechanics.create":true,"mechanics.update":true,"mechanics.delete":true,"mechanics.view":true,"operational_checks.create":true,"operational_checks.update":true,"operational_checks.delete":true,"operational_checks.view":true,"operators.create":true,"operators.update":true,"operators.delete":true,"operators.view":true,"operator_trucks.create":true,"operator_trucks.update":true,"operator_trucks.delete":true,"operator_trucks.view":true,"shifts.create":true,"shifts.update":true,"shifts.delete":true,"shifts.view":true}', '2016-11-30 02:43:50', '2016-11-30 02:43:50');

INSERT INTO `role_users` (`user_id`, `role_id`, `created_at`, `updated_at`) VALUES
	(1, 1, '2016-12-02 00:10:36', '2016-12-02 00:10:36');

INSERT INTO `users` (`id`, `email`, `password`, `permissions`, `last_login`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', '$2y$10$FaaF6jrCS4MZQ6iwkJlyq.Lt2WFEN0k18hnw3WJroQhMGw5UFuzW2', '{"users.create":true,"users.delete":true,"users.view":true,"users.update":true,"trucks.create":true,"trucks.update":true,"trucks.delete":true,"trucks.view":true,"checklist.create":true,"checklist.update":true,"checklist.delete":true,"checklist.view":true,"checkout_messages.create":true,"checkout_messages.update":true,"checkout_messages.delete":true,"checkout_messages.view":true,"departments.create":true,"departments.update":true,"departments.delete":true,"departments.view":true,"fuel_record.create":true,"fuel_record.update":true,"fuel_record.delete":true,"fuel_record.view":true,"fuel_types.create":true,"fuel_types.update":true,"fuel_types.delete":true,"fuel_types.view":true,"mechanics.create":true,"mechanics.update":true,"mechanics.delete":true,"mechanics.view":true,"operational_checks.create":true,"operational_checks.update":true,"operational_checks.delete":true,"operational_checks.view":true,"operators.create":true,"operators.update":true,"operators.delete":true,"operators.view":true,"operator_trucks.create":true,"operator_trucks.update":true,"operator_trucks.delete":true,"operator_trucks.view":true,"shifts.create":true,"shifts.update":true,"shifts.delete":true,"shifts.view":true}', '2016-12-11 09:18:14', 'Super', 'Admin', '2016-12-02 00:10:36', '2016-12-11 09:18:14');
