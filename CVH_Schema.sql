--
-- Table structure for table `answers`
--

CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `answer` varchar(1024) NOT NULL,
  `source_id` int(10) unsigned NOT NULL COMMENT 'FK sources.id',
  `NSFW` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `source_id` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(1024) NOT NULL,
  `number_of_answers` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `NSFW` tinyint(1) NOT NULL DEFAULT '0',
  `source_id` int(10) unsigned NOT NULL DEFAULT '3' COMMENT 'FK sources.id',
  PRIMARY KEY (`id`),
  KEY `source_id` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Table structure for table `questions_answers_votes`
--

CREATE TABLE IF NOT EXISTS `questions_answers_votes` (
  `question_id` int(10) unsigned NOT NULL COMMENT 'FK questions.id',
  `answer_id` int(10) unsigned NOT NULL COMMENT 'FK answers.id',
  `vote_tally` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`,`answer_id`),
  KEY `answer_id` (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sources`
--

CREATE TABLE IF NOT EXISTS `sources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(256) NOT NULL,
  `url` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`);

--
-- Constraints for table `questions_answers_votes`
--
ALTER TABLE `questions_answers_votes`
  ADD CONSTRAINT `questions_answers_votes_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
  ADD CONSTRAINT `questions_answers_votes_ibfk_2` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`);
