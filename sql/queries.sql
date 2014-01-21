-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 18, 2014 at 12:59 AM
-- Server version: 5.6.11
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cs519`
--
CREATE DATABASE IF NOT EXISTS `cs519` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `cs519`;

-- --------------------------------------------------------

--
-- Table structure for table `queries`
--

CREATE TABLE IF NOT EXISTS `queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `query` text NOT NULL,
  `skip` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Dumping data for table `queries`
--

INSERT INTO `queries` (`id`, `name`, `query`, `skip`) VALUES
(1, 'General stats', 'select ''table'', ''count'' from dual\r\nunion\r\nSELECT ''authors'', count(*)  from authors\r\nunion\r\nSELECT ''scripts'', count(*) from scripts\r\nunion\r\nSELECT ''features'', count(*) from features\r\nunion\r\nSELECT ''tutorials'', count(*) from tutorials\r\nunion\r\nSELECT ''interactive tutorials'', count(*) from tutorials where is_interactive = true\r\nunion\r\nSELECT ''non-interactive tutorials'', count(*) from tutorials where is_interactive = false\r\nunion\r\nSELECT ''tutorial completions'', count(*) from scripts_tutorials', 0),
(2, 'duplicate values (scripts)', 'SELECT name, COUNT(*) c FROM scripts GROUP BY name HAVING c > 1 ORDER BY c DESC', 0),
(3, 'duplicate values (tutorials)', 'SELECT name, COUNT(*) c FROM tutorials GROUP BY name HAVING c > 1 ORDER BY c DESC', NULL),
(4, 'duplicate values (features)', 'SELECT name, COUNT(*) c FROM features GROUP BY name HAVING c > 1 ORDER BY c DESC', NULL),
(5, 'duplicate values (chunks)', 'SELECT content, COUNT(*) c FROM chunks GROUP BY content HAVING c > 1 ORDER BY c DESC', NULL),
(6, 'duplicate values (authors)', 'SELECT name, COUNT(*) c FROM authors GROUP BY name HAVING c > 1 ORDER BY c DESC', NULL),
(7, 'duplicate values (scripts_chunks)', 'SELECT c.content, group_concat(s.script_id) script_ids, COUNT(*) N FROM chunks c JOIN scripts_chunks sc on sc.chunk_id = c.id join scripts s on s.id = sc.script_id GROUP BY content, sc.script_id, sc.seq HAVING N > 1 ORDER BY N DESC', NULL),
(8, 'Number of completed tutorials by author', 'SELECT count(ta.id) number_of_completed_tutorials, group_concat(s.script_id) script_ids, a.name author_name FROM `tutorials_by_author` ta join authors a on ta.author_id = a.id join tutorials t on t.id = ta.tutorial_id join scripts s on s.id = t.script_id where ta.is_completed  = TRUE group by ta.author_id  order by number_of_completed_tutorials desc', NULL),
(9, 'Number of started tutorials by author', 'SELECT count(ta.id) number_of_completed_tutorials, group_concat(s.script_id) script_ids, a.name author_name FROM `tutorials_by_author` ta join authors a on ta.author_id = a.id join tutorials t on t.id = ta.tutorial_id join scripts s on s.id = t.script_id where ta.is_completed  = FALSE group by ta.author_id  order by number_of_completed_tutorials desc', NULL),
(10, 'Number of published scripts by author (excluding tutorials)', 'select \r\ncount(s.id) number_of_scripts, \r\ncount(distinct ta.tutorial_id) number_of_completed_tutorials,\r\na.name author_name,\r\na.join_date,\r\na.features,\r\na.activedays,\r\na.receivedpositivereviews,\r\na.subscribers,\r\na.score\r\nfrom authors a \r\njoin scripts s \r\non s.author_id = a.id \r\nleft join tutorials_by_author ta\r\non a.id = ta.author_id and ta.is_completed = true\r\nwhere s.id not in (\r\n    select script_id \r\n    from tutorials\r\n)\r\nand a.name not in (''peli@touchdevelop'', ''Nikolai@TouchDevelop'', ''Michał Moskal'')\r\nand a.name not like ''TouchDevelop%''\r\nand a.name not like ''%@touchdevelop''\r\ngroup by s.author_id order by number_of_scripts desc', NULL),
(11, 'Number of published scripts by author (interactive)', 'select \r\nt.is_interactive,\r\ncount(s.id) number_of_scripts, \r\ncount(distinct ta.tutorial_id) number_of_completed_tutorials,\r\na.name author_name,\r\na.join_date,\r\na.features,\r\na.activedays,\r\na.receivedpositivereviews,\r\na.subscribers,\r\na.score\r\nfrom authors a \r\njoin scripts s \r\non s.author_id = a.id \r\njoin tutorials_by_author ta\r\non a.id = ta.author_id and ta.is_completed = true\r\njoin tutorials t \r\non t.id = ta.tutorial_id\r\nwhere s.id not in (\r\n    select script_id \r\n    from tutorials\r\n)\r\nand t.is_interactive = true\r\n\r\nand a.name not in (''peli@touchdevelop'', ''Nikolai@TouchDevelop'', ''Michał Moskal'')\r\nand a.name not like ''TouchDevelop%''\r\nand a.name not like ''%@touchdevelop''\r\ngroup by s.author_id order by number_of_scripts desc', NULL),
(12, 'Number of published scripts by author (non-interactive)', 'select \r\nt.is_interactive,\r\ncount(s.id) number_of_scripts, \r\ncount(distinct ta.tutorial_id) number_of_completed_tutorials,\r\na.name author_name,\r\na.join_date,\r\na.features,\r\na.activedays,\r\na.receivedpositivereviews,\r\na.subscribers,\r\na.score\r\nfrom authors a \r\njoin scripts s \r\non s.author_id = a.id \r\njoin tutorials_by_author ta\r\non a.id = ta.author_id and ta.is_completed = true\r\njoin tutorials t \r\non t.id = ta.tutorial_id\r\nwhere s.id not in (\r\n    select script_id \r\n    from tutorials\r\n)\r\nand t.is_interactive = false\r\n\r\nand a.name not in (''peli@touchdevelop'', ''Nikolai@TouchDevelop'', ''Michał Moskal'')\r\nand a.name not like ''TouchDevelop%''\r\nand a.name not like ''%@touchdevelop''\r\ngroup by s.author_id order by number_of_scripts desc', NULL),
(13, 'number of scripts by author', 'SELECT count(*) number_of_scripts, a.name author FROM `scripts` s join authors a on s.author_id = a.id GROUP BY author order by number_of_scripts desc', NULL),
(14, 'authors who have more than 5 published scripts', 'SELECT count(*) number_of_scripts, a.name author FROM `scripts` s join authors a on s.author_id = a.id GROUP BY author HAVING number_of_scripts > 5 order by number_of_scripts desc', NULL),
(15, 'authors who have <= 5 published scripts', 'SELECT count(*) number_of_scripts, a.name author FROM `scripts` s join authors a on s.author_id = a.id GROUP BY author HAVING number_of_scripts <= 5 order by number_of_scripts desc', NULL),
(16, 'authors who have one published script', 'SELECT count(*) number_of_scripts, a.name author FROM `scripts` s join authors a on s.author_id = a.id GROUP BY author HAVING number_of_scripts = 1', NULL),
(17, 'frequency of features usage in scripts', 'SELECT f.name feature_name, count(sf.id) feature_frequency FROM scripts_features sf JOIN features f ON sf.feature_id = f.id GROUP by f.id ORDER BY feature_frequency DESC', NULL),
(18, 'frequency of features coverage in tutorials', 'SELECT f.name feature_name, count(tf.id) feature_frequency FROM tutorials_features tf JOIN features f ON tf.feature_id = f.id GROUP by f.id ORDER BY feature_frequency DESC', NULL),
(19, 'Number of features covered by each tutorial', 'SELECT t.name tutorial_name, s.script_id, count(*) number_of_feature_usages FROM `tutorials` t join tutorials_features tf on t.id = tf.tutorial_id join features f on f.id = tf.feature_id join scripts s on t.script_id = s.id group by t.id order by number_of_feature_usages desc', NULL),
(20, 'Features covered by tutorials', 'select f.name feature_name from features f where f.id in (select feature_id from tutorials_features) order by f.name', NULL),
(21, 'Features that are not covered by tutorials', 'select f.name feature_name from features f where f.id not in (select feature_id from tutorials_features) order by f.name', NULL),
(22, 'Features used in scripts', 'select f.name feature_name from features f where f.id in (select feature_id from scripts_features) order by f.name', NULL),
(23, 'Features not used in scripts', 'select f.name feature_name from features f where f.id not in (select feature_id from scripts_features) order by f.name', NULL),
(24, 'Libraries covered by tutorials', 'select l.name library_name from libraries l where l.id in (select library_id from tutorials_libraries) order by l.name', NULL),
(25, 'Libraries not covered by tutorials', 'select l.name library_name from libraries l where l.id not in (select library_id from tutorials_libraries) order by l.name', NULL),
(26, 'Number of scripts based on specific tutorials', '(SELECT t.name, s.script_id, s.date, count(st.script_id) `number of successors`, t.is_interactive FROM tutorials t \r\njoin `scripts_tutorials` st  on st.tutorial_id = t.id \r\njoin scripts s on s.id = t.script_id\r\nwhere t.is_interactive = true\r\ngroup by s.id\r\norder by N desc)\r\n\r\nunion all \r\n\r\n(SELECT t.name, s.script_id, s.date, count(st.script_id) `number of successors`, t.is_interactive FROM tutorials t \r\njoin `scripts_tutorials` st  on st.tutorial_id = t.id \r\njoin scripts s on s.id = t.script_id\r\nwhere t.is_interactive = false\r\ngroup by s.id\r\norder by N desc)', 1),
(27, 'Number of features appearance in tutorials', 'SELECT distinct f.name, count(tf.tutorial_id) N FROM `tutorials_features` tf join features f on tf.feature_id = f.id group by f.id order by N desc', 1),
(28, 'Number of libraries appearance in tutorials', 'SELECT distinct l.name, count(tl.library_id) N FROM `tutorials_libraries` tl join libraries l on tl.library_id = l.id group by l.id order by N desc', NULL),
(29, 'Number of features appearance in scripts', 'SELECT distinct f.name, count(sf.script_id) N FROM `scripts_features` sf join features f on sf.feature_id = f.id group by f.id order by N desc', 1),
(30, 'Number of libraries appearance in scripts', 'SELECT distinct l.name, count(sl.library_id) N FROM `scripts_libraries` sl join libraries l on sl.library_id = l.id group by l.id order by N desc', NULL),
(31, 'Number of tutorial successors and completions', 'SELECT t.name, s.script_id, s.date, count(st.script_id) successors, \r\n( select count(ta.id) from tutorials_by_author ta where ta.tutorial_id = t.id \r\n-- and ta.is_completed = true\r\n) completions FROM tutorials t \r\njoin `scripts_tutorials` st  on st.tutorial_id = t.id \r\n-- join scripts s on s.id = st.script_id \r\njoin scripts s on s.id = t.script_id\r\n\r\ngroup by s.id\r\norder by successors desc', NULL),
(32, 'Number of tutorial successors and completions (interactive)', 'SELECT t.name, s.script_id, s.date, count(st.script_id) successors, \r\n( select count(ta.id) from tutorials_by_author ta where ta.tutorial_id = t.id \r\n-- and ta.is_completed = true\r\n) completions FROM tutorials t \r\njoin `scripts_tutorials` st  on st.tutorial_id = t.id \r\n-- join scripts s on s.id = st.script_id \r\njoin scripts s on s.id = t.script_id\r\nwhere t.is_interactive = true\r\ngroup by s.id\r\norder by successors desc', NULL),
(33, 'Number of tutorial successors and completions (non-interactive)', 'SELECT t.name, s.script_id, s.date, count(st.script_id) successors, \r\n( select count(ta.id) from tutorials_by_author ta where ta.tutorial_id = t.id \r\n-- and ta.is_completed = true\r\n) completions FROM tutorials t \r\njoin `scripts_tutorials` st  on st.tutorial_id = t.id \r\n-- join scripts s on s.id = st.script_id \r\njoin scripts s on s.id = t.script_id\r\nwhere t.is_interactive = false\r\ngroup by s.id\r\norder by successors desc', NULL),
(34, 'Number of tutorial with and without successors', 'select ''WITHOUT'', count(t.id) N from tutorials t where t.id not in (select tutorial_id from scripts_tutorials st)\r\nunion\r\nselect ''WITH'', count(t.id) N from tutorials t where t.id in (select tutorial_id from scripts_tutorials st)\r\nunion \r\nselect ''TOTAL'', count(t.id) N from tutorials t', NULL),
(35, 'Number of tutorial with and without successors (interactive)', 'select ''WITHOUT'', count(t.id) N from tutorials t where t.id not in (select tutorial_id from scripts_tutorials st) and t.is_interactive = true\r\nunion\r\nselect ''WITH'', count(t.id) N from tutorials t where t.id in (select tutorial_id from scripts_tutorials st) and t.is_interactive = true\r\nunion \r\nselect ''TOTAL'', count(t.id) N from tutorials t where t.is_interactive = true', NULL),
(36, 'Number of tutorial with and without successors (non-interactive)', 'select ''WITHOUT'', count(t.id) N from tutorials t where t.id not in (select tutorial_id from scripts_tutorials st) and t.is_interactive = false\r\nunion\r\nselect ''WITH'', count(t.id) N from tutorials t where t.id in (select tutorial_id from scripts_tutorials st) and t.is_interactive = false\r\nunion \r\nselect ''TOTAL'', count(t.id) N from tutorials t where t.is_interactive = false', NULL),
(37, 'Statistics of tutorial completions', 'SELECT number_of_completed_tutorials, COUNT( * ) N FROM `AUTHORS_STATS` GROUP BY number_of_completed_tutorials ORDER BY number_of_completed_tutorials DESC', NULL),
(38, 'Statistics of tutorial completions (interactive)', 'SELECT number_of_completed_tutorials, COUNT( * ) N FROM `AUTHORS_STATS_INTERACTIVE` GROUP BY number_of_completed_tutorials ORDER BY number_of_completed_tutorials DESC', NULL),
(39, 'Statistics of tutorial completions (non-interactive)', 'SELECT number_of_completed_tutorials, COUNT( * ) N FROM `AUTHORS_STATS_NONINTERACTIVE` GROUP BY number_of_completed_tutorials ORDER BY number_of_completed_tutorials DESC', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
