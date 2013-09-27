-- phpMyAdmin SQL Dump
-- version 3.3.10.4
-- http://www.phpmyadmin.net
--
-- Host: mysql.maxmahem.net
-- Generation Time: Sep 27, 2013 at 08:58 AM
-- Server version: 5.1.56
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cvh_maxmahem_net`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(1024) NOT NULL,
  `source_id` int(10) unsigned NOT NULL COMMENT 'FK sources.id',
  `NSFW` tinyint(1) NOT NULL DEFAULT '0',
  `validated` tinyint(1) NOT NULL DEFAULT '0',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `source_id` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(1024) NOT NULL,
  `NSFW` tinyint(1) NOT NULL DEFAULT '0',
  `source_id` int(10) unsigned NOT NULL DEFAULT '3' COMMENT 'FK sources.id',
  `validated` tinyint(1) NOT NULL DEFAULT '0',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `source_id` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

-- --------------------------------------------------------

--
-- Table structure for table `questions_answers_votes`
--

CREATE TABLE IF NOT EXISTS `questions_answers_votes` (
  `question_id` int(10) unsigned NOT NULL COMMENT 'FK questions.id',
  `answer_id` int(10) unsigned NOT NULL COMMENT 'FK answers.id',
  `vote_tally` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`question_id`,`answer_id`),
  KEY `answer_id` (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

CREATE TABLE IF NOT EXISTS `sources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source` (`source`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `questions_answers_votes`
--
ALTER TABLE `questions_answers_votes`
  ADD CONSTRAINT `questions_answers_votes_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `questions_answers_votes_ibfk_2` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
