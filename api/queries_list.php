<?php

$save_csv_script = 'save_csv.php';

include_once ('config.php');

$data = array();

$res = mysql_query("select * from queries");

while( $row = mysql_fetch_assoc($res) ) {
    
    $data[] =  array(
        'name' => $row['name'],
        'query' => $row['query'],
        'skip' => $row['skip']
    );
}

/*$data = array(
	array(
        'name' => 'General stats',
		'query' => "select 'table', 'count' from dual
union
SELECT 'authors', count(*)  from authors
union
SELECT 'scripts', count(*) from scripts
union
SELECT 'features', count(*) from features
union
SELECT 'tutorials', count(*) from tutorials
union
SELECT 'interactive tutorials', count(*) from tutorials where is_interactive = true
union
SELECT 'non-interactive tutorials', count(*) from tutorials where is_interactive = false
union
SELECT 'tutorial completions', count(*) from scripts_tutorials",
	),
	array(
		'name' => 'duplicate values (scripts)',
		'query' => "SELECT name, COUNT(*) c FROM scripts GROUP BY name HAVING c > 1 ORDER BY c DESC",
	),
	array(
		'name' => 'duplicate values (tutorials)',
		'query' => "SELECT name, COUNT(*) c FROM tutorials GROUP BY name HAVING c > 1 ORDER BY c DESC",
	),
	array(
		'name' => 'duplicate values (features)',
		'query' => "SELECT name, COUNT(*) c FROM features GROUP BY name HAVING c > 1 ORDER BY c DESC",
	),
	array(
		'name' => 'duplicate values (chunks)',
		'query' => 'SELECT content, COUNT(*) c FROM chunks GROUP BY content HAVING c > 1 ORDER BY c DESC'
	),
	array(
		'name' => 'duplicate values (authors)',
		'query' => 'SELECT name, COUNT(*) c FROM authors GROUP BY name HAVING c > 1 ORDER BY c DESC'
	),
	array(
		'name' => 'duplicate values (scripts_chunks)',
		'query' => 'SELECT c.content, group_concat(s.script_id) script_ids, COUNT(*) N FROM chunks c JOIN scripts_chunks sc on sc.chunk_id = c.id join scripts s on s.id = sc.script_id GROUP BY content, sc.script_id, sc.seq HAVING N > 1 ORDER BY N DESC'
	) ,
    
    array(
		'name' => 'Number of completed tutorials by author',
		'query' => "SELECT count(ta.id) number_of_completed_tutorials, group_concat(s.script_id) script_ids, a.name author_name FROM `tutorials_by_author` ta join authors a on ta.author_id = a.id join tutorials t on t.id = ta.tutorial_id join scripts s on s.id = t.script_id where ta.is_completed  = TRUE group by ta.author_id  order by number_of_completed_tutorials desc",
	),
	array(
		'name' => 'Number of started tutorials by author',
		'query' => "SELECT count(ta.id) number_of_completed_tutorials, group_concat(s.script_id) script_ids, a.name author_name FROM `tutorials_by_author` ta join authors a on ta.author_id = a.id join tutorials t on t.id = ta.tutorial_id join scripts s on s.id = t.script_id where ta.is_completed  = FALSE group by ta.author_id  order by number_of_completed_tutorials desc",
	),
	array(
		'name' => 'Number of published scripts by author (excluding tutorials)',
		'query' => "select 
count(s.id) number_of_scripts, 
count(distinct ta.tutorial_id) number_of_completed_tutorials,
a.name author_name,
a.join_date,
a.features,
a.activedays,
a.receivedpositivereviews,
a.subscribers,
a.score
from authors a 
join scripts s 
on s.author_id = a.id 
left join tutorials_by_author ta
on a.id = ta.author_id and ta.is_completed = true
where s.id not in (
    select script_id 
    from tutorials
)
and a.name not in ('peli@touchdevelop', 'Nikolai@TouchDevelop', 'Michał Moskal')
and a.name not like 'TouchDevelop%'
and a.name not like '%@touchdevelop'
group by s.author_id order by number_of_scripts desc",
	),
    array(
		'name' => 'Number of published scripts by author (interactive)',
		'query' => "select 
t.is_interactive,
count(s.id) number_of_scripts, 
count(distinct ta.tutorial_id) number_of_completed_tutorials,
a.name author_name,
a.join_date,
a.features,
a.activedays,
a.receivedpositivereviews,
a.subscribers,
a.score
from authors a 
join scripts s 
on s.author_id = a.id 
join tutorials_by_author ta
on a.id = ta.author_id and ta.is_completed = true
join tutorials t 
on t.id = ta.tutorial_id
where s.id not in (
    select script_id 
    from tutorials
)
and t.is_interactive = true

and a.name not in ('peli@touchdevelop', 'Nikolai@TouchDevelop', 'Michał Moskal')
and a.name not like 'TouchDevelop%'
and a.name not like '%@touchdevelop'
group by s.author_id order by number_of_scripts desc",
	),
    array(
		'name' => 'Number of published scripts by author (non-interactive)',
		'query' => "select 
t.is_interactive,
count(s.id) number_of_scripts, 
count(distinct ta.tutorial_id) number_of_completed_tutorials,
a.name author_name,
a.join_date,
a.features,
a.activedays,
a.receivedpositivereviews,
a.subscribers,
a.score
from authors a 
join scripts s 
on s.author_id = a.id 
join tutorials_by_author ta
on a.id = ta.author_id and ta.is_completed = true
join tutorials t 
on t.id = ta.tutorial_id
where s.id not in (
    select script_id 
    from tutorials
)
and t.is_interactive = false

and a.name not in ('peli@touchdevelop', 'Nikolai@TouchDevelop', 'Michał Moskal')
and a.name not like 'TouchDevelop%'
and a.name not like '%@touchdevelop'
group by s.author_id order by number_of_scripts desc",
	),
    
    array(
        'name' => 'number of scripts by author',
        'query' => 'SELECT count(*) number_of_scripts, a.name author FROM `scripts` s join authors a on s.author_id = a.id GROUP BY author order by number_of_scripts desc'
    ),
    array(
        'name' => 'authors who have more than 5 published scripts',
        'query' => 'SELECT count(*) number_of_scripts, a.name author FROM `scripts` s join authors a on s.author_id = a.id GROUP BY author HAVING number_of_scripts > 5 order by number_of_scripts desc'
    ),
    array(
        'name' => 'authors who have <= 5 published scripts',
        'query' => 'SELECT count(*) number_of_scripts, a.name author FROM `scripts` s join authors a on s.author_id = a.id GROUP BY author HAVING number_of_scripts <= 5 order by number_of_scripts desc'
    ),
    array(
        'name' => 'authors who have one published script',
        'query' => 'SELECT count(*) number_of_scripts, a.name author FROM `scripts` s join authors a on s.author_id = a.id GROUP BY author HAVING number_of_scripts = 1'
    ),
    array(
        'skip' => true,
        'name' => 'frequency of features usage in scripts',
        'query' => 'SELECT f.name feature_name, count(sf.id) feature_frequency FROM scripts_features sf JOIN features f ON sf.feature_id = f.id GROUP by f.id ORDER BY feature_frequency DESC'
    ),
    array(
        'name' => 'frequency of features coverage in tutorials',
        'query' => 'SELECT f.name feature_name, count(tf.id) feature_frequency FROM tutorials_features tf JOIN features f ON tf.feature_id = f.id GROUP by f.id ORDER BY feature_frequency DESC'
    ),
    array(
        'name' => 'Number of features covered by each tutorial',
        'query' => 'SELECT t.name tutorial_name, s.script_id, count(*) number_of_feature_usages FROM `tutorials` t join tutorials_features tf on t.id = tf.tutorial_id join features f on f.id = tf.feature_id join scripts s on t.script_id = s.id group by t.id order by number_of_feature_usages desc'
    ),
    array(
        'name' => 'Features covered by tutorials',
        'query' => 'select f.name feature_name from features f where f.id in (select feature_id from tutorials_features) order by f.name'
    ),
    array(
        'name' => 'Features that are not covered by tutorials',
        'query' => 'select f.name feature_name from features f where f.id not in (select feature_id from tutorials_features) order by f.name'
    ),
    array(
        'name' => 'Features used in scripts',
        'query' => 'select f.name feature_name from features f where f.id in (select feature_id from scripts_features) order by f.name'
    ),
    array(
        'name' => 'Features not used in scripts',
        'query' => 'select f.name feature_name from features f where f.id not in (select feature_id from scripts_features) order by f.name'
    ),
    array(
        'name' => 'Libraries covered by tutorials',
        'query' => 'select l.name library_name from libraries l where l.id in (select library_id from tutorials_libraries) order by l.name'
    ),
    array(
        'name' => 'Libraries not covered by tutorials',
        'query' => 'select l.name library_name from libraries l where l.id not in (select library_id from tutorials_libraries) order by l.name'
    ),
    array(
        'skip' => true,
        'name' => 'Number of scripts based on specific tutorials',
        'query' => '(SELECT t.name, s.script_id, s.date, count(st.script_id) `number of successors`, t.is_interactive FROM tutorials t 
join `scripts_tutorials` st  on st.tutorial_id = t.id 
join scripts s on s.id = t.script_id
where t.is_interactive = true
group by s.id
order by N desc)

union all 

(SELECT t.name, s.script_id, s.date, count(st.script_id) `number of successors`, t.is_interactive FROM tutorials t 
join `scripts_tutorials` st  on st.tutorial_id = t.id 
join scripts s on s.id = t.script_id
where t.is_interactive = false
group by s.id
order by N desc)
'
    ),
    array(
        'skip' => true,
        'name' => 'Number of features appearance in tutorials',
        'query' => 'SELECT distinct f.name, count(tf.tutorial_id) N FROM `tutorials_features` tf join features f on tf.feature_id = f.id group by f.id order by N desc',
    ),
    array(
        'name' => 'Number of libraries appearance in tutorials',
        'query' => 'SELECT distinct l.name, count(tl.library_id) N FROM `tutorials_libraries` tl join libraries l on tl.library_id = l.id group by l.id order by N desc'
    ),
    array(
        'skip' => true,
        'name' => 'Number of features appearance in scripts',
        'query' => 'SELECT distinct f.name, count(sf.script_id) N FROM `scripts_features` sf join features f on sf.feature_id = f.id group by f.id order by N desc'
    ),
    array(
        'name' => 'Number of libraries appearance in scripts',
        'query' => 'SELECT distinct l.name, count(sl.library_id) N FROM `scripts_libraries` sl join libraries l on sl.library_id = l.id group by l.id order by N desc'
    ),
    array(
        'name' => 'Number of tutorial successors and completions',
        'query' => 'SELECT t.name, s.script_id, s.date, count(st.script_id) successors, 
( select count(ta.id) from tutorials_by_author ta where ta.tutorial_id = t.id 
-- and ta.is_completed = true
) completions FROM tutorials t 
join `scripts_tutorials` st  on st.tutorial_id = t.id 
-- join scripts s on s.id = st.script_id 
join scripts s on s.id = t.script_id

group by s.id
order by successors desc'
    ),
    array(
        'name' => 'Number of tutorial successors and completions (interactive)',
        'query' => 'SELECT t.name, s.script_id, s.date, count(st.script_id) successors, 
( select count(ta.id) from tutorials_by_author ta where ta.tutorial_id = t.id 
-- and ta.is_completed = true
) completions FROM tutorials t 
join `scripts_tutorials` st  on st.tutorial_id = t.id 
-- join scripts s on s.id = st.script_id 
join scripts s on s.id = t.script_id
where t.is_interactive = true
group by s.id
order by successors desc'
    ),
    array(
        'name' => 'Number of tutorial successors and completions (non-interactive)',
        'query' => 'SELECT t.name, s.script_id, s.date, count(st.script_id) successors, 
( select count(ta.id) from tutorials_by_author ta where ta.tutorial_id = t.id 
-- and ta.is_completed = true
) completions FROM tutorials t 
join `scripts_tutorials` st  on st.tutorial_id = t.id 
-- join scripts s on s.id = st.script_id 
join scripts s on s.id = t.script_id
where t.is_interactive = false
group by s.id
order by successors desc'
    ),
    array(
        'name' => 'Number of tutorial with and without successors',
        'query' => "select 'WITHOUT', count(t.id) N from tutorials t where t.id not in (select tutorial_id from scripts_tutorials st)
union
select 'WITH', count(t.id) N from tutorials t where t.id in (select tutorial_id from scripts_tutorials st)
union 
select 'TOTAL', count(t.id) N from tutorials t"
    ),
    array(
        'name' => 'Number of tutorial with and without successors (interactive)',
        'query' => "select 'WITHOUT', count(t.id) N from tutorials t where t.id not in (select tutorial_id from scripts_tutorials st) and t.is_interactive = true
union
select 'WITH', count(t.id) N from tutorials t where t.id in (select tutorial_id from scripts_tutorials st) and t.is_interactive = true
union 
select 'TOTAL', count(t.id) N from tutorials t where t.is_interactive = true"
    ),
    array(
        'name' => 'Number of tutorial with and without successors (non-interactive)',
        'query' => "select 'WITHOUT', count(t.id) N from tutorials t where t.id not in (select tutorial_id from scripts_tutorials st) and t.is_interactive = false
union
select 'WITH', count(t.id) N from tutorials t where t.id in (select tutorial_id from scripts_tutorials st) and t.is_interactive = false
union 
select 'TOTAL', count(t.id) N from tutorials t where t.is_interactive = false"
    ),
    array(
        'name' => 'Statistics of tutorial completions ',
        'query' => "SELECT number_of_completed_tutorials, COUNT( * ) N FROM `AUTHORS_STATS` GROUP BY number_of_completed_tutorials ORDER BY number_of_completed_tutorials DESC"
    ),
    array(
        'name' => 'Statistics of tutorial completions (interactive)',
        'query' => "SELECT number_of_completed_tutorials, COUNT( * ) N FROM `AUTHORS_STATS_INTERACTIVE` GROUP BY number_of_completed_tutorials ORDER BY number_of_completed_tutorials DESC"
    ),
    array(
        'name' => 'Statistics of tutorial completions (non-interactive)',
        'query' => "SELECT number_of_completed_tutorials, COUNT( * ) N FROM `AUTHORS_STATS_NONINTERACTIVE` GROUP BY number_of_completed_tutorials ORDER BY number_of_completed_tutorials DESC"
    ),
);*/
