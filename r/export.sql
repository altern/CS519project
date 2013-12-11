SELECT 'is_interactive',
'number_of_scripts', 
'number_of_completed_tutorials',
'author_name',
'join_date',
'features',
'activedays',
'receivedpositivereviews',
'subscribers',
'score'
UNION ALL
SELECT is_interactive,
number_of_scripts, 
number_of_completed_tutorials,
author_name,
join_date,
features,
activedays,
receivedpositivereviews,
subscribers,
score
INTO OUTFILE './noninteractive.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM AUTHORS_STATS_NONINTERACTIVE;

SELECT 'is_interactive',
'number_of_scripts', 
'number_of_completed_tutorials',
'author_name',
'join_date',
'features',
'activedays',
'receivedpositivereviews',
'subscribers',
'score'
UNION ALL
SELECT is_interactive,
number_of_scripts, 
number_of_completed_tutorials,
author_name,
join_date,
features,
activedays,
receivedpositivereviews,
subscribers,
score
INTO OUTFILE './interactive.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM AUTHORS_STATS_INTERACTIVE