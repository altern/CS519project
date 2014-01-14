<?php

$save_csv_script = 'save_csv2.php';

$data = array(
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
);