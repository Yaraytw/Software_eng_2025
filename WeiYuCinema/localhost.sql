-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2025 年 11 月 30 日 17:36
-- 伺服器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- 資料表結構 `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(11) NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- 傾印資料表的資料 `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"WeiYuCinema\",\"table\":\"ticketClass\"},{\"db\":\"WeiYuCinema\",\"table\":\"showing\"},{\"db\":\"WeiYuCinema\",\"table\":\"seatCondition\"},{\"db\":\"WeiYuCinema\",\"table\":\"movie\"},{\"db\":\"WeiYuCinema\",\"table\":\"memberPwdQuestion\"},{\"db\":\"WeiYuCinema\",\"table\":\"memberProfile\"},{\"db\":\"WeiYuCinema\",\"table\":\"memberCashCard\"},{\"db\":\"WeiYuCinema\",\"table\":\"meals\"},{\"db\":\"WeiYuCinema\",\"table\":\"cinema\"},{\"db\":\"WeiYuCinema\",\"table\":\"bookingRecord\"}]');

-- --------------------------------------------------------

--
-- 資料表結構 `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- 傾印資料表的資料 `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-11-29 18:42:21', '{\"Console\\/Mode\":\"collapse\",\"lang\":\"zh_TW\"}');

-- --------------------------------------------------------

--
-- 資料表結構 `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- 資料表結構 `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- 資料表索引 `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- 資料表索引 `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- 資料表索引 `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- 資料表索引 `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- 資料表索引 `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- 資料表索引 `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- 資料表索引 `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- 資料表索引 `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- 資料表索引 `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- 資料表索引 `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- 資料表索引 `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- 資料表索引 `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- 資料表索引 `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- 資料表索引 `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- 資料表索引 `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- 資料表索引 `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- 資料表索引 `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 資料庫： `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `test`;
--
-- 資料庫： `WeiYuCinema`
--
CREATE DATABASE IF NOT EXISTS `WeiYuCinema` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `WeiYuCinema`;

-- --------------------------------------------------------

--
-- 資料表結構 `bookingRecord`
--

CREATE TABLE `bookingRecord` (
  `orderNumber` varchar(20) NOT NULL,
  `memberId` varchar(10) DEFAULT NULL,
  `showingId` int(11) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `seat` varchar(100) DEFAULT NULL,
  `chooseMeal` varchar(100) DEFAULT NULL,
  `ticketTypeId` int(11) DEFAULT NULL,
  `ticketNums` int(11) DEFAULT NULL,
  `totalPrice` int(11) DEFAULT NULL,
  `orderStatusId` int(11) DEFAULT NULL,
  `getTicketNum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `cinema`
--

CREATE TABLE `cinema` (
  `cinemaId` varchar(2) NOT NULL,
  `cinemaName` varchar(20) DEFAULT NULL,
  `cinemaAddress` varchar(80) DEFAULT NULL,
  `cinemaTele` varchar(15) DEFAULT NULL,
  `cinemaImg` varchar(50) DEFAULT NULL,
  `cinemaInfo` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `cinema`
--

INSERT INTO `cinema` (`cinemaId`, `cinemaName`, `cinemaAddress`, `cinemaTele`, `cinemaImg`, `cinemaInfo`) VALUES
('01', '台北信義威宇影城', '台北市信義區松壽路20號', '02-8780-5566', 'cinema_xinyi.jpg', '位於信義商圈核心，提供4DX與IMAX頂級影廳體驗。'),
('02', '台北京站威宇影城', '台北市大同區承德路一段1號', '02-2552-5511', 'cinema_qsquare.jpg', '位於台北車站旁，交通最便利的電影院。'),
('03', '板橋大遠百威宇', '新北市板橋區新站路28號', '02-7738-6608', 'cinema_banqiao.jpg', '結合IMAX與數位影廳，新北首選。');

-- --------------------------------------------------------

--
-- 資料表結構 `meals`
--

CREATE TABLE `meals` (
  `mealsId` int(11) NOT NULL,
  `mealsName` varchar(20) DEFAULT NULL,
  `mealsPrice` int(11) DEFAULT NULL,
  `mealsPhoto` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `meals`
--

INSERT INTO `meals` (`mealsId`, `mealsName`, `mealsPrice`, `mealsPhoto`) VALUES
(1, '巧納棒', 99, 'churros.jpg'),
(2, '中杯可樂', 60, 'coke_m.jpg'),
(3, '小爆米花(甜)', 120, 'popcorn_s.jpg'),
(4, '熱狗堡', 110, 'hotdog.jpg');

-- --------------------------------------------------------

--
-- 資料表結構 `memberCashCard`
--

CREATE TABLE `memberCashCard` (
  `memberId` varchar(10) NOT NULL,
  `balance` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `memberProfile`
--

CREATE TABLE `memberProfile` (
  `memberId` varchar(10) NOT NULL,
  `memberName` varchar(20) DEFAULT NULL,
  `memberEmail` varchar(50) DEFAULT NULL,
  `memberPwd` varchar(255) DEFAULT NULL,
  `memberPhone` varchar(10) DEFAULT NULL,
  `memberBirth` varchar(10) DEFAULT NULL,
  `memberPwdHintId` int(11) DEFAULT NULL,
  `memberPwdHintAns` varchar(50) DEFAULT NULL,
  `memberPayAccount` varchar(20) DEFAULT NULL,
  `memberConfirm` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `memberPwdQuestion`
--

CREATE TABLE `memberPwdQuestion` (
  `memberPwdHintId` int(11) NOT NULL,
  `memberPwdHintContent` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `memberPwdQuestion`
--

INSERT INTO `memberPwdQuestion` (`memberPwdHintId`, `memberPwdHintContent`) VALUES
(1, '您國小班導師的名字？'),
(2, '您第一隻寵物的名字？'),
(3, '您的出生地是哪裡？');

-- --------------------------------------------------------

--
-- 資料表結構 `movie`
--

CREATE TABLE `movie` (
  `movieId` int(11) NOT NULL,
  `movieName` varchar(35) DEFAULT NULL,
  `movieTime` int(11) DEFAULT NULL,
  `movieStart` varchar(10) DEFAULT NULL,
  `movieImg` varchar(50) DEFAULT NULL,
  `gradeId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `movie`
--

INSERT INTO `movie` (`movieId`, `movieName`, `movieTime`, `movieStart`, `movieImg`, `gradeId`) VALUES
(1, '復仇者聯盟：終局之戰', 181, '2025-04-24', 'avengers.jpg', 2),
(2, '阿凡達：水之道', 192, '2025-12-16', 'avatar2.jpg', 1),
(3, '鈴芽之旅', 122, '2025-03-02', 'suzume.jpg', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `seatCondition`
--

CREATE TABLE `seatCondition` (
  `showingId` int(11) NOT NULL,
  `seatNumber` varchar(10) NOT NULL,
  `seatEmpty` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `seatCondition`
--

INSERT INTO `seatCondition` (`showingId`, `seatNumber`, `seatEmpty`) VALUES
(101, '1', 1),
(101, '10', 1),
(101, '11', 1),
(101, '12', 1),
(101, '13', 1),
(101, '14', 1),
(101, '15', 1),
(101, '16', 1),
(101, '17', 1),
(101, '18', 1),
(101, '19', 1),
(101, '2', 1),
(101, '20', 1),
(101, '3', 0),
(101, '4', 1),
(101, '5', 1),
(101, '6', 1),
(101, '7', 1),
(101, '8', 0),
(101, '9', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `showing`
--

CREATE TABLE `showing` (
  `showingId` int(11) NOT NULL,
  `movieId` int(11) DEFAULT NULL,
  `cinemaId` varchar(2) DEFAULT NULL,
  `theaterId` varchar(6) DEFAULT NULL,
  `showingDate` varchar(10) DEFAULT NULL,
  `startTime` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `showing`
--

INSERT INTO `showing` (`showingId`, `movieId`, `cinemaId`, `theaterId`, `showingDate`, `startTime`) VALUES
(101, 1, '01', 'A', '2025-12-01', '10:30'),
(102, 1, '01', 'A', '2025-12-01', '14:30'),
(103, 2, '02', 'B', '2025-12-01', '18:00');

-- --------------------------------------------------------

--
-- 資料表結構 `ticketClass`
--

CREATE TABLE `ticketClass` (
  `ticketClassId` int(11) NOT NULL,
  `ticketClassName` varchar(20) DEFAULT NULL,
  `ticketClassPrice` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `ticketClass`
--

INSERT INTO `ticketClass` (`ticketClassId`, `ticketClassName`, `ticketClassPrice`) VALUES
(1, '全票', 330),
(2, '學生票', 300),
(3, '優待票', 280),
(4, '愛心票', 165);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `bookingRecord`
--
ALTER TABLE `bookingRecord`
  ADD PRIMARY KEY (`orderNumber`),
  ADD KEY `memberId` (`memberId`),
  ADD KEY `showingId` (`showingId`);

--
-- 資料表索引 `cinema`
--
ALTER TABLE `cinema`
  ADD PRIMARY KEY (`cinemaId`);

--
-- 資料表索引 `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`mealsId`);

--
-- 資料表索引 `memberCashCard`
--
ALTER TABLE `memberCashCard`
  ADD PRIMARY KEY (`memberId`);

--
-- 資料表索引 `memberProfile`
--
ALTER TABLE `memberProfile`
  ADD PRIMARY KEY (`memberId`),
  ADD KEY `memberPwdHintId` (`memberPwdHintId`);

--
-- 資料表索引 `memberPwdQuestion`
--
ALTER TABLE `memberPwdQuestion`
  ADD PRIMARY KEY (`memberPwdHintId`);

--
-- 資料表索引 `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`movieId`);

--
-- 資料表索引 `seatCondition`
--
ALTER TABLE `seatCondition`
  ADD PRIMARY KEY (`showingId`,`seatNumber`);

--
-- 資料表索引 `showing`
--
ALTER TABLE `showing`
  ADD PRIMARY KEY (`showingId`),
  ADD KEY `movieId` (`movieId`),
  ADD KEY `cinemaId` (`cinemaId`);

--
-- 資料表索引 `ticketClass`
--
ALTER TABLE `ticketClass`
  ADD PRIMARY KEY (`ticketClassId`);

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `bookingRecord`
--
ALTER TABLE `bookingRecord`
  ADD CONSTRAINT `bookingrecord_ibfk_1` FOREIGN KEY (`memberId`) REFERENCES `memberProfile` (`memberId`),
  ADD CONSTRAINT `bookingrecord_ibfk_2` FOREIGN KEY (`showingId`) REFERENCES `showing` (`showingId`);

--
-- 資料表的限制式 `memberCashCard`
--
ALTER TABLE `memberCashCard`
  ADD CONSTRAINT `membercashcard_ibfk_1` FOREIGN KEY (`memberId`) REFERENCES `memberProfile` (`memberId`);

--
-- 資料表的限制式 `memberProfile`
--
ALTER TABLE `memberProfile`
  ADD CONSTRAINT `memberprofile_ibfk_1` FOREIGN KEY (`memberPwdHintId`) REFERENCES `memberPwdQuestion` (`memberPwdHintId`);

--
-- 資料表的限制式 `seatCondition`
--
ALTER TABLE `seatCondition`
  ADD CONSTRAINT `seatcondition_ibfk_1` FOREIGN KEY (`showingId`) REFERENCES `showing` (`showingId`);

--
-- 資料表的限制式 `showing`
--
ALTER TABLE `showing`
  ADD CONSTRAINT `showing_ibfk_1` FOREIGN KEY (`movieId`) REFERENCES `movie` (`movieId`),
  ADD CONSTRAINT `showing_ibfk_2` FOREIGN KEY (`cinemaId`) REFERENCES `cinema` (`cinemaId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
