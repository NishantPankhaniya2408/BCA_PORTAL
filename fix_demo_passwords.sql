USE `bca_portal`;

-- Repairs the default demo credentials in an existing imported database.
UPDATE `admins`
SET `password` = '$2y$10$7JCLhT5VrxcTtu8jTpPti.8HkkSy6zHdZv43SJzYhK0AlyMwDAOUG'
WHERE `username` = 'admin';

UPDATE `students`
SET `password` = '$2y$10$s2wddaI3T4U./8/Fwohj4unkj3/TSTV7FKPuKIT8BEk7c.kfnUUta'
WHERE `enrollment_no` IN ('BCA2023001', 'BCA2023002', 'BCA2022015');
