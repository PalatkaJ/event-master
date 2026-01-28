SET NAMES utf8mb4;
SET foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS `user` (
    `email` varchar(255) NOT NULL,
    `full_name` varchar(255) NOT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `event` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(64) NOT NULL,
    `description` varchar(1024) NOT NULL,
    `start_date` date NOT NULL,
    `end_date` date NOT NULL,
    `hero_image` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `organizer` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `organizer` (`organizer`),
    CONSTRAINT `event_ibfk_1` FOREIGN KEY (`organizer`) REFERENCES `user` (`email`),
    CONSTRAINT `check_dates` CHECK ((`end_date` >= `start_date`)),
    CONSTRAINT `event_chk_1` CHECK ((`start_date` >= `created_at`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `event_registration` (
    `user_email` varchar(255) NOT NULL,
    `event_id` int NOT NULL,
    PRIMARY KEY (`user_email`,`event_id`),
    KEY `event_id` (`event_id`),
    KEY `user_email` (`user_email`),
    CONSTRAINT `event_registration_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `user` (`email`) ON DELETE CASCADE,
    CONSTRAINT `event_registration_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `workshop` (
    `id` int NOT NULL AUTO_INCREMENT,
    `event_id` int NOT NULL,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `event_id` (`event_id`),
    CONSTRAINT `workshop_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `workshop_registration` (
    `workshop_id` int NOT NULL,
    `user_email` varchar(255) NOT NULL,
    `event_id` int NOT NULL,
    PRIMARY KEY (`workshop_id`,`user_email`),
    KEY `fk_workshop_to_event_reg` (`user_email`,`event_id`),
    CONSTRAINT `fk_workshop_to_event_reg` FOREIGN KEY (`user_email`, `event_id`) REFERENCES `event_registration` (`user_email`, `event_id`) ON DELETE CASCADE,
    CONSTRAINT `workshop_registration_ibfk_3` FOREIGN KEY (`workshop_id`) REFERENCES `workshop` (`id`) ON DELETE CASCADE,
    CONSTRAINT `workshop_registration_ibfk_4` FOREIGN KEY (`user_email`) REFERENCES `user` (`email`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`email`, `full_name`) VALUES
('davidek@gmail.cz', 'Davidek Palatka'),
('jankopalatka@seznam.cz', 'Jan Palatka'),
('new@seznam.cz', 'Antonin Slavicek');

INSERT INTO `event` (`id`, `name`, `description`, `start_date`, `end_date`, `hero_image`, `created_at`, `organizer`) VALUES
(1, 'COOL AI Summit', 'Some very nice description about the future of AI.', '2026-06-29', '2026-06-29', 'event_6977816cc35548.png', '2026-01-26 14:59:57', 'jankopalatka@seznam.cz'),
(2, 'Jan''s Birthday Party', 'The best birthday party ever seen at MFF.', '2026-07-27', '2026-07-28', 'event_6977d809a9a821.png', '2026-01-26 21:09:29', 'davidek@gmail.cz'),
(3, 'SLAVIA vs BARCELONA', 'Best football match ever seen in history.', '2026-08-29', '2026-08-30', 'event_6978fcf59bd1e9.png', '2026-01-27 17:59:17', 'jankopalatka@seznam.cz');

INSERT INTO `event_registration` (`user_email`, `event_id`) VALUES
('jankopalatka@seznam.cz', 1);

INSERT INTO `workshop` (`id`, `event_id`, `name`) VALUES
(1, 1, 'Morning Breakfast'),
(2,2, 'AI Models 101'),
(3, 3,'Goalie Training'),
(4, 1, 'Afternoon Tea');

INSERT INTO `workshop_registration` (`workshop_id`, `user_email`, `event_id`) VALUES
(1, 'jankopalatka@seznam.cz', 1);

SET foreign_key_checks = 1;