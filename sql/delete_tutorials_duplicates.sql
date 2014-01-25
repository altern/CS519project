DELETE
FROM scripts_tutorials
WHERE script_id
IN (
SELECT 
s2.id script_id

FROM (

SELECT COUNT( * ) c, MAX( s.date ) max_date, t.name, t.is_interactive
FROM tutorials t
JOIN scripts s ON s.id = t.script_id
WHERE t.id
IN (

SELECT tutorial_id
FROM scripts_tutorials
)
GROUP BY t.name, t.is_interactive
HAVING c >1
ORDER BY c DESC
) AS s1
JOIN tutorials t2 ON t2.name = s1.name
AND t2.is_interactive = s1.is_interactive
JOIN scripts s2 ON s2.date < s1.max_date
AND t2.script_id = s2.id-- and s2.id in (select script_id from scripts_tutorials)
-- group by s1.name, t2.is_interactive
-- order by c1 desc
);

 
DELETE
FROM scripts_tutorials
WHERE tutorial_id
IN (
SELECT 
t2.id tutorial_id

FROM (

SELECT COUNT( * ) c, MAX( s.date ) max_date, t.name, t.is_interactive
FROM tutorials t
JOIN scripts s ON s.id = t.script_id
WHERE t.id
IN (

SELECT tutorial_id
FROM scripts_tutorials
)
GROUP BY t.name, t.is_interactive
HAVING c >1
ORDER BY c DESC
) AS s1
JOIN tutorials t2 ON t2.name = s1.name
AND t2.is_interactive = s1.is_interactive
JOIN scripts s2 ON s2.date < s1.max_date
AND t2.script_id = s2.id-- and s2.id in (select script_id from scripts_tutorials)
-- group by s1.name, t2.is_interactive
-- order by c1 desc
);


DELETE
FROM tutorials_libraries
WHERE tutorial_id
IN (
SELECT 
t2.id tutorial_id

FROM (

SELECT COUNT( * ) c, MAX( s.date ) max_date, t.name, t.is_interactive
FROM tutorials t
JOIN scripts s ON s.id = t.script_id
WHERE t.id
IN (

SELECT tutorial_id
FROM scripts_tutorials
)
GROUP BY t.name, t.is_interactive
HAVING c >1
ORDER BY c DESC
) AS s1
JOIN tutorials t2 ON t2.name = s1.name
AND t2.is_interactive = s1.is_interactive
JOIN scripts s2 ON s2.date < s1.max_date
AND t2.script_id = s2.id-- and s2.id in (select script_id from scripts_tutorials)
-- group by s1.name, t2.is_interactive
-- order by c1 desc
);

DELETE
FROM tutorials_features
WHERE tutorial_id
IN (
SELECT 
t2.id tutorial_id

FROM (

SELECT COUNT( * ) c, MAX( s.date ) max_date, t.name, t.is_interactive
FROM tutorials t
JOIN scripts s ON s.id = t.script_id
WHERE t.id
IN (

SELECT tutorial_id
FROM scripts_tutorials
)
GROUP BY t.name, t.is_interactive
HAVING c >1
ORDER BY c DESC
) AS s1
JOIN tutorials t2 ON t2.name = s1.name
AND t2.is_interactive = s1.is_interactive
JOIN scripts s2 ON s2.date < s1.max_date
AND t2.script_id = s2.id-- and s2.id in (select script_id from scripts_tutorials)
-- group by s1.name, t2.is_interactive
-- order by c1 desc
);

DELETE
FROM tutorials
WHERE id
IN (
SELECT 
t2.id tutorial_id

FROM (

SELECT COUNT( * ) c, MAX( s.date ) max_date, t.name, t.is_interactive
FROM tutorials t
JOIN scripts s ON s.id = t.script_id
WHERE t.id
IN (

SELECT tutorial_id
FROM scripts_tutorials
)
GROUP BY t.name, t.is_interactive
HAVING c >1
ORDER BY c DESC
) AS s1
JOIN tutorials t2 ON t2.name = s1.name
AND t2.is_interactive = s1.is_interactive
JOIN scripts s2 ON s2.date < s1.max_date
AND t2.script_id = s2.id-- and s2.id in (select script_id from scripts_tutorials)
-- group by s1.name, t2.is_interactive
-- order by c1 desc
)