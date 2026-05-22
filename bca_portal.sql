-- BCA Portal Database Schema
-- Import this in phpMyAdmin into a database named `bca_portal`

CREATE DATABASE IF NOT EXISTS `bca_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bca_portal`;

-- ---------- Admins ----------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin: username = admin, password = admin123
INSERT INTO `admins` (`username`,`password`,`name`) VALUES
('admin','$2y$10$7JCLhT5VrxcTtu8jTpPti.8HkkSy6zHdZv43SJzYhK0AlyMwDAOUG','Department Admin');

-- ---------- Students ----------
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `enrollment_no` VARCHAR(30) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100),
  `phone` VARCHAR(20),
  `semester` TINYINT NOT NULL CHECK (`semester` BETWEEN 1 AND 6),
  `batch_year` YEAR NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default students (password = student123)
INSERT INTO `students` (`enrollment_no`,`password`,`name`,`email`,`phone`,`semester`,`batch_year`) VALUES
('BCA2023001','$2y$10$s2wddaI3T4U./8/Fwohj4unkj3/TSTV7FKPuKIT8BEk7c.kfnUUta','Aarav Sharma','aarav@example.com','9876543210',1,2023),
('BCA2023002','$2y$10$s2wddaI3T4U./8/Fwohj4unkj3/TSTV7FKPuKIT8BEk7c.kfnUUta','Priya Patel','priya@example.com','9876543211',3,2022),
('BCA2022015','$2y$10$s2wddaI3T4U./8/Fwohj4unkj3/TSTV7FKPuKIT8BEk7c.kfnUUta','Rohit Verma','rohit@example.com','9876543212',5,2021);

-- ---------- Subjects ----------
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) UNIQUE NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `course_type` VARCHAR(50) NOT NULL DEFAULT 'Theory',
  `semester` TINYINT NOT NULL CHECK (`semester` BETWEEN 1 AND 6),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `subjects` (`code`,`name`,`course_type`,`semester`) VALUES
-- Sem 1
('BCA101','Fundamentals of Computer','Theory',1),
('BCA102','Programming in C','Theory',1),
('BCA103','Mathematics-I','Theory',1),
('BCA104','Communication Skills','Theory',1),
-- Sem 2
('BCA201','Data Structures using C','Theory',2),
('BCA202','Digital Electronics','Theory',2),
('BCA203','Mathematics-II','Theory',2),
('BCA204','Operating System Concepts','Theory',2),
-- Sem 3
('BCA301','Object Oriented Programming with C++','Theory',3),
('BCA302','Database Management System','Theory',3),
('BCA303','Computer Networks','Theory',3),
('BCA304','System Analysis & Design','Theory',3),
-- Sem 4
('BCA401','Java Programming','Theory',4),
('BCA402','Web Technology (HTML, CSS, JS)','Theory',4),
('BCA403','Software Engineering','Theory',4),
('BCA404','Computer Graphics','Theory',4),
-- Sem 5
('BCA501','PHP & MySQL','Theory',5),
('BCA502','Python Programming','Theory',5),
('BCA503','Data Mining & Warehousing','Theory',5),
('BCA504','E-Commerce','Theory',5),
-- Sem 6
('BCA601','Mobile App Development (Android)','Theory',6),
('BCA602','Cloud Computing','Theory',6),
('BCA603','Cyber Security','Theory',6),
('BCA604','Major Project','Project',6);

-- ---------- Materials ----------
CREATE TABLE IF NOT EXISTS `materials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` INT NOT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- Question Papers ----------
CREATE TABLE IF NOT EXISTS `papers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `exam_year` YEAR NOT NULL,
  `exam_type` ENUM('Mid-Term','End-Term','Internal','Other') DEFAULT 'End-Term',
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` INT NOT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
