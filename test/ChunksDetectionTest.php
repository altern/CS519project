<?php

include_once("../api/functions.php");

class ChunksDetectionTest extends PHPUnit_Framework_TestCase {
    
    public function chunksDetectionDataProviderTemplate() {
        return array(array(
            'script_id' => '',
            'chunks' => array('')
        ));
    }
    
    public function emptyScriptData() {
        return array(array(
            'script_id' => 'siys',
            'chunks' => array('')
        ));
    }
    
    /**
     * @test
     * @dataProvider emptyScriptData
     */
    public function testEmptyScript($script_id, $chunks) {
        $tutorial_source = file_get_contents("http://www.touchdevelop.com/api/$script_id/text");
        $detected_chunks = detect_chunks($tutorial_source);
        $this->assertEmpty($detected_chunks);
    }
    
    public function testTutorialsChunksData() {
        return array(
            array(
                'script_id' => 'pgzba',
                'chunks' => array(
                    'code→tree(100, 5);',
                    'meta private;',
                    "code→tree(100, 3);\nmeta private;"
                )
            ),
            array(
                'script_id' => 'vozf',
                'chunks' => array(
                    "data→k := data→k + 1;\ndata→k→\u25c8set(data→k→\u25c8get + 1);",
                    'data→k→\u25c8add(1);',
                    'code→double(data→k→\u25c8ref);'
                )
            ),
            array(
                'script_id' => 'mwde',
                'chunks' => array(
                    "cloud_data→switch_sessions;",
                    'cloud_data→switch_to_session(cloud_data→everyone_session);',
                    'cloud_data→switch_to_session(cloud_data→just_me_session);'."\n".
                    'cloud_data→switch_to_session(cloud_data→session_of("bqsl0prcbbmuwe", ""));',
                    '$s := cloud_data→create_session("my new session", "shareable");'
                )
            ),
            
            array(
                'script_id' => 'auko',
                'chunks' => array(
                    '$row := records→grocery_table→add_row;',
                    '$row→product := "milk";'."\n".
                    '$row→quantity := 2;',
                    '$product := $row→product;'."\n".
                    '$product→post_to_wall;',
                    'foreach g in records→grocery_table'."\n".
                    'where true'."\n".
                    'do {'."\n".
                    '$g→post_to_wall;'."\n".
                    '}',
                    'foreach gr in records→grocery_table'."\n".
                    'where $gr→quantity > 5'."\n".
                    'do {'."\n".
                    'skip;'."\n".
                    '}',
                )
            ),
            array(
                'script_id' => 'ecek',
                'chunks' => array(
                    'code→do_stuff;'
                )
            ),
            array(
                'script_id' => 'qsfn',
                'chunks' => array(
                    'art;'."\n".
                    'media→create_board(640);'."\n".
                    '$c := colors→red;'."\n".
                    '$c→make_transparent(0);'."\n".
                    'skip;'
                )
            ),
            array(
                'script_id' => 'mizba',
                'chunks' => array(
                    '@\u267b→scratch→when_flag_clicked($clicked);'."\n".
                    'where clicked() {'."\n".
                    'data→random_number := @\u267b→scratch→pick_random(1, 10);'."\n".
                    '}'
                )
            ),
            array(
                'script_id' => 'vffdb',
                'chunks' => array(
                    '@\u267b→scratch→switch_backdrop_to(art→soccer_field);',
                    '$platform := @\u267b→scratch→new_rectangle_sprite(colors→blue, 150, 10);',
                    '$platform→set_y_to( - 150);',
                    '@\u267b→scratch→when_flag_clicked($clicked);'."\n".
                    'where clicked() {',
                    '@\u267b→scratch→forever($@action);'."\n".
                    'where @action() {',
                    '$platform→set_x_to(@\u267b→scratch→mouse_x);',
                    '}'."\n".
                    '}',
                    '$ball := @\u267b→scratch→new_sprite(art→soccer_ball);',
                    '@\u267b→scratch→when_flag_clicked($clicked2);'."\n".
                    'where clicked2() {',
                    '$ball→go_to_x_y(20, 150);',
                    '$ball→point_in_direction(45);',
                    '@\u267b→scratch→forever($action2);'."\n".
                    'where action2() {',
                    '$ball→if_on_edge\u002c_bounce;',
                    '$ball→move(10);',
                    '}'."\n".
                    '}',
                    '$@bottom := @\u267b→scratch→new_rectangle_sprite(colors→red, 480, 20);'."\n".
                    '$@bottom→set_y_to( - 170);',
                    '@\u267b→scratch→when_flag_clicked($clicked3);'."\n".
                    'where clicked3() {',
                    '@\u267b→scratch→forever($action3);'."\n".
                    'where action3() {',
                    'if $ball→touching($platform) then {',
                    '@\u267b→scratch→play_sound(art→water_drop_sound);',
                    '$ball→turn_right(@\u267b→scratch→pick_random(160, 200));',
                    '$ball→move(10);',
                    '}'."\n". 
                    'else {',
                    'if $ball→touching($@bottom) then {',
                    '@\u267b→scratch→stop_all;',
                    '}'."\n".
                    '}'."\n".
                    '}'."\n".
                    '}',
                )
            ),
            
        );
    }
    
    /**
     * @test
     * @dataProvider testTutorialsChunksData
     */
    public function testTutorialsChunks($script_id, $chunks) {
        $tutorial_source = file_get_contents("http://www.touchdevelop.com/api/$script_id/text");
        $detected_chunks = detect_chunks($tutorial_source);
        print_r($chunks);
        print_r($detected_chunks);
        print_r(array_diff($chunks, $detected_chunks));
        $this->assertTrue($chunks == $detected_chunks);
    }
    
    /**
     * @test
     */
    public function testTest() {
        $a = array (
            'codeâ†’tree(100, 5);',
            'meta private;',
            'codeâ†’tree(100, 3);
meta private;'
        );
        $b = array(
            'codeâ†’tree(100, 5);',
            'meta private;',
            'codeâ†’tree(100, 3);
meta private;'
        );
        $this->assertTrue($a == $b);
    }
}

