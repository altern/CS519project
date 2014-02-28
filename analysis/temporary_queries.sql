-- Tutorials that have been completed, by author
-- SELECT
-- completed.name, COUNT(completions)
-- FROM (
	SELECT
	CONCAT(ta.author_id), t.name AS name, COUNT(ta.tutorial_id) AS completions
	FROM
	tutorials t 
	JOIN tutorials_by_normalauthor ta ON ta.tutorial_id = t.id
	WHERE is_completed >= 1
	GROUP BY t.id
-- ) completed
-- GROUP BY completed.name

-- Among authors who completed only one tutorial, here are the ones they completed.
-- SELECT
-- completed.name, COUNT(completions)
-- FROM (
	SELECT
	CONCAT(ta.author_id), t.name AS name, COUNT(ta.tutorial_id) AS completions
	FROM
	tutorials t
	JOIN tutorials_by_normalauthor ta ON ta.tutorial_id = t.id
	WHERE is_completed >= 1
	GROUP BY t.id
-- ) completed
-- GROUP BY completed.name


-- List of authors that attempted 1 tutorial only
SELECT
"Completed 1 tutorial", ta.author_id AS 'author', GROUP_CONCAT(t.name) tutorials
FROM
tutorials_by_normalauthor ta
JOIN tutorials t ON t.id = ta.tutorial_id
WHERE is_completed = FALSE
GROUP BY ta.author_id
HAVING COUNT(ta.author_id) = 1
ORDER BY author ASC

-- List of authors that attempted tutorials
SELECT
"Completed 1 tutorial", ta.author_id AS 'author', GROUP_CONCAT(t.name) tutorials, GROUP_CONCAT(ta.is_completed)
FROM
tutorials_by_normalauthor ta
JOIN tutorials t ON t.id = ta.tutorial_id
WHERE is_completed = FALSE
GROUP BY ta.author_id
ORDER BY author ASC

-- List of authors who completed 1 tutorial
SELECT
"Completed 1 tutorial", ta.author_id AS 'author', GROUP_CONCAT(t.name) AS tutorials, GROUP_CONCAT(ta.is_completed)
FROM
tutorials_by_normalauthor ta
JOIN tutorials t ON t.id = ta.tutorial_id
WHERE is_completed = TRUE
GROUP BY ta.author_id
HAVING COUNT(ta.author_id) = 1
ORDER BY author ASC

-- List of authors who completed 2+ tutorials
SELECT
"Completed 2 tutorials", ta.author_id AS 'author', GROUP_CONCAT(t.name) AS tutorials, GROUP_CONCAT(ta.is_completed)
FROM
tutorials_by_normalauthor ta
JOIN tutorials t ON t.id = ta.tutorial_id
WHERE is_completed = TRUE
GROUP BY ta.author_id
HAVING COUNT(ta.author_id) = 2
ORDER BY author ASC

-- List of tutorials that are attempted by authors
SELECT
t.name Tutorial, s.script_id as id, COUNT(DISTINCT ta.author_id) AS Attempts
FROM
tutorials_by_normalauthor ta
JOIN tutorials t ON t.id = ta.tutorial_id
JOIN scripts s ON s.id = t.script_id
WHERE is_completed = FALSE
GROUP BY ta.tutorial_id
-- HAVING COUNT(ta.author_id) = 1
ORDER BY Attempts ASC

-- List of tutorials that are completed by authors
SELECT
t.name Tutorial, COUNT(DISTINCT ta.author_id) AS Attempts
FROM
tutorials_by_normalauthor ta
JOIN tutorials t ON t.id = ta.tutorial_id
WHERE is_completed = TRUE
GROUP BY ta.tutorial_id
-- HAVING COUNT(ta.author_id) = 1
ORDER BY Attempts ASC


-- Mapping the tutorials that result in the creation of scripts
SELECT
DISTINCT
t.name tutorial, tutorialscript.script_id tutorialidentifier, tp.seq as s.name script, s.script_id identifier, ta.is_completed completed
FROM
scripts_tutorials st
JOIN tutorials t ON t.id = st.tutorial_id
JOIN scripts s ON s.id = st.script_id
JOIN scripts tutorialscript ON tutorialscript.id = t.script_id
JOIN tutorials_by_normalauthor ta ON ta.tutorial_id = st.tutorial_id
















-- Author statistics for authors who didn't try tutorials
SELECT DISTINCT
"Authors who ",
a.id, a.name, join_date, activedays, receivedpositivereviews, subscribers, score, 'notutorial'
FROM notutorial_scripts ts
JOIN normal_authors a ON ts.author_id = a.id

UNION

-- Author statistics for authors who completed 0 tutorials
SELECT DISTINCT
a.id, a.name, join_date, activedays, receivedpositivereviews, subscribers, score, '0tutorial'
FROM tutorial0_scripts ts
INNER JOIN normal_authors a ON ts.author_id = a.id

UNION

-- Author statistics for authors who completed 1 tutorial
SELECT DISTINCT
a.id, a.name, join_date, activedays, receivedpositivereviews, subscribers, score, '1tutorial'
FROM tutorial1_scripts ts
INNER JOIN normal_authors a ON ts.author_id = a.id

UNION

-- Author statistics for authors who completed 2+ tutorials
SELECT DISTINCT
a.id, a.name, join_date, activedays, receivedpositivereviews, subscribers, score, '2tutorial'
FROM tutorial2plus_scripts ts
INNER JOIN normal_authors a ON ts.author_id = a.id


-- Author statistics for authors who completed 1 tutorial, including is_interactive
SELECT DISTINCT
a.author_id, a.name, a.join_date, a.activedays, a.receivedpositivereviews as 'positivereviews', a.subscribers, a.score, t.is_interactive as "interactive"
FROM tutorial1_scripts s
JOIN tutorials_by_normalauthor ta ON s.author_id = ta.author_id
JOIN tutorials t ON ta.tutorial_id = t.id
JOIN normal_authors a ON ta.author_id = a.id
WHERE is_completed = TRUE

-- Authors who did not attempt or complete any tutorials
SELECT DISTINCT a.name as creator FROM normal_authors a
JOIN scripts s ON a.id = s.author_id
WHERE a.id NOT IN (SELECT author_id FROM tutorials_by_normalauthor)
ORDER BY creator

-- Features used by an author in scripts
SELECT DISTINCT a.name as creator FROM normal_authors a
JOIN scripts s ON a.id = s.author_id
JOIN scripts_features sf ON sf.script_id = s.id