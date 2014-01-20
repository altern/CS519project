CREATE OR REPLACE VIEW `normal_authors`
AS SELECT
   `a`.`id` AS `id`,
   `a`.`name` AS `name`,
   `a`.`author_id` AS `author_id`,
   `a`.`join_date` AS `join_date`,
   `a`.`features` AS `features`,
   `a`.`activedays` AS `activedays`,
   `a`.`receivedpositivereviews` AS `receivedpositivereviews`,
   `a`.`subscribers` AS `subscribers`,
   `a`.`score` AS `score`
FROM `authors` `a`
where
((`a`.`name` not in ('peli@touchdevelop','Nikolai@TouchDevelop'))
and (not((`a`.`name` like 'TouchDevelop%')))
and (not((`a`.`name` like '%@touchdevelop')))
and (not((`a`.`name` like '%@Moskal')))
);

CREATE OR REPLACE VIEW `tutorials_by_normalauthor`
AS SELECT
   `ta`.`id` AS `id`,
   `ta`.`author_id` AS `author_id`,
   `ta`.`tutorial_id` AS `tutorial_id`,
   `ta`.`is_completed` AS `is_completed`
FROM `tutorials_by_author` `ta` where `ta`.`author_id` in (select `normal_authors`.`id` from `normal_authors`);

CREATE OR REPLACE VIEW `normalauthor_scripts`
AS SELECT
   `s`.`id` AS `id`,
   `s`.`name` AS `name`,
   `s`.`content` AS `content`,
   `s`.`date` AS `date`,
   `s`.`script_id` AS `script_id`,
   `s`.`author_id` AS `author_id`,
   `s`.`description` AS `description`,
   `s`.`positivereviews` AS `positivereviews`,
   `s`.`cumulativepositivereviews` AS `cumulativepositivereviews`,
   `s`.`installations` AS `installations`,
   `s`.`runs` AS `runs`
FROM `scripts` `s` where `s`.`author_id` in (select `normal_authors`.`id` from `normal_authors`);

CREATE OR REPLACE VIEW `notutorial_scripts`
AS SELECT * FROM normalauthor_scripts s
WHERE s.author_id NOT IN (SELECT DISTINCT author_id FROM tutorials_by_normalauthor);

CREATE OR REPLACE VIEW `tutorial0_scripts`
AS SELECT s.* FROM normalauthor_scripts s
INNER JOIN tutorials_by_normalauthor ta ON s.author_id = ta.author_id
WHERE ta.author_id NOT IN (SELECT DISTINCT author_id FROM tutorials_by_normalauthor WHERE is_completed = TRUE);

CREATE OR REPLACE VIEW `tutorial1_scripts`
AS SELECT s.* FROM normalauthor_scripts s
WHERE s.author_id IN
(SELECT DISTINCT author_id
FROM tutorials_by_normalauthor ta
WHERE is_completed = TRUE
GROUP BY author_id
HAVING COUNT(author_id) = 1);

CREATE OR REPLACE VIEW `tutorial2plus_scripts`
AS SELECT s.* FROM normalauthor_scripts s
WHERE s.author_id IN
(SELECT DISTINCT author_id
FROM tutorials_by_normalauthor ta
WHERE is_completed = TRUE
GROUP BY author_id
HAVING COUNT(author_id) = 2);