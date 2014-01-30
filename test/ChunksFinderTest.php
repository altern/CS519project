<?php

include_once("../api/functions.php");

class ChunksFinderTest extends PHPUnit_Framework_TestCase {

    public function chunksFinderDataProviderTemplate() {
        return array(array(
            'script' => '',
            'chunks' => array('')
        ));
    }
    
    public function emptyBlockData() {
        return array(array(
            'script' => '',
            'chunks' => array('')
        ));
    }
    
    /**
     * @test
     * @dataProvider emptyBlockData
     */
    public function testEmptyBlock($script, $chunks) {
        $lines = explode("\n", $script);
        $this->assertFalse(find_chunks($lines, $chunks));
    }
    
    public function emptyChunksData() {
        return array(array(
            'script' => '',
            'chunks' => array()
        ));
    }
    
    /**
     * @test
     * @dataProvider emptyChunksData
     */
    public function testEmptyChunks($script, $chunks) {
        $lines = explode("\n", $script);
        $this->assertFalse(find_chunks($lines, $chunks));
    }
    
    public function oneLineData() {
        return array(array(
            'script' => 'art→monster→post_to_wall;',
            'chunks' => array('art→monster→post_to_wall;')
        ));
    }
    
    /**
     * @test
     * @dataProvider oneLineData
     */
    public function testOneLine($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number = 1;
        $this->assertEquals($line_number, find_chunks($lines, $chunks));
    }
    
    public function twoBlocksData() {
        return array(array(
            'script' => 'art→monster→post_to_wall;
box→set_width(11);',
            'chunks' => array('art→monster→post_to_wall;')
        ));
    }
    
    /**
     * @test
     * @dataProvider twoBlocksData
     */
    public function testTwoBlocks($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number_of_last_match = 1;
        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
    }
    
    public function twoChunksData() {
        return array(array(
            'script' => 'art→monster→post_to_wall;
box→set_width(11);',
            'chunks' => array(
                'art→monster→post_to_wall;',
                'box→set_width(11);'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider twoChunksData
     */
    public function testTwoChunks($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number_of_last_match = 2;
        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
    }
    
    public function twoNonContiguousChunksData() {
        return array(array(
            'script' => 'art→monster→post_to_wall;
box→on_tapped($handler);
box→set_width(11);',
            'chunks' => array(
                'art→monster→post_to_wall;',
                'box→set_width(11);'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider twoNonContiguousChunksData
     */
    public function testTwoNonContiguousChunks($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number_of_last_match = 3;
        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
    }
    
    public function threeNonContiguousChunksData() {
        return array(array(
            'script' => 'art→monster→post_to_wall;
box→on_tapped($handler);
box→set_width(11);
art→monkey→post_to_wall;
box→use_horizontal_layout;',
            'chunks' => array(
                'art→monster→post_to_wall;',
                'box→set_width(11);',
                'box→use_horizontal_layout;'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider threeNonContiguousChunksData
     */
    public function testThreeNonContiguousChunks($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number_of_last_match = 5;
        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
    }
    
    public function oneBlockData() {
        return array(array(
            'script' => 'do box {
    box→use_horizontal_layout;
}',
            'chunks' => array(
                'do box {',
                'box→use_horizontal_layout;',
                '}'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider oneBlockData
     */
    public function testOneBlock($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number_of_last_match = 3;
        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
    }
    
    public function oneBlockFewChunksData() {
        return array(array(
            'script' => 'do box {
    box→use_horizontal_layout;
}',
            'chunks' => array(
                'do box {',
                '    box→use_horizontal_layout;',
                '}'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider oneBlockFewChunksData
     */
    public function testOneBlockFewChunks($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number_of_last_match = 3;
        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
    }
    
    public function oneLineOneChunkData() {
        return array(array(
            'script' => 'do box {',
            'chunks' => array(
                'do box {
    box→use_horizontal_layout;
}'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider oneLineOneChunkData
     */
    public function testOneLineOneChunk($script, $chunks) {
        $lines = explode("\n", $script);
        $this->assertFalse(find_chunks($lines, $chunks));
    }
    
    public function oneBlockOneChunkData() {
        return array(array(
            'script' => 'do box {
    box→use_horizontal_layout;
}',
            'chunks' => array(
                'do box {
    box→use_horizontal_layout;
}'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider oneBlockOneChunkData
     */
    public function testOneBlockOneChunk($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number_of_last_match = 1;
        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
    }
    
    public function fewLinesOneChunkData() {
        return array(array(
            'script' => 'art→sound____→play;
do box {
    box→use_horizontal_layout;
}',
            'chunks' => array(
                'do box {
    box→use_horizontal_layout;
}'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider fewLinesOneChunkData
     */
    public function testFewLinesOneChunk($script, $chunks) {
        $lines = explode("\n", $script);
        $line_number_of_last_match = 2;
        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
    }
    
    public function twoDuplicatedChunksData() {
        return array(array(
            'script' => 'do box {
   a = 123;
}
do box {
}',
            'chunks' => array(
                'do box {
    a = 123;                   
}',
                'do box {
}'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider twoDuplicatedChunksData
     */
//    public function testTwoDuplicatedChunksData($script, $chunks) {
//        $lines = explode("\n", $script);
//        $line_number_of_last_match = 4;
//        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
//    }
    
    public function fewLinesTwoMultilineChunksData() {
        return array(array(
            'script' => 
'art→sound____→play;
do box {
    box→use_horizontal_layout;
}
a = 123
b = 345
do box {
}',
            'chunks' => array(
'do box {
    box→use_horizontal_layout;
}',
'do box {
}'
            )
        ));
    }
    
    /**
     * @test
     * @dataProvider fewLinesTwoMultilineChunksData
     */
//    public function testfewLinesTwoMultilineChunks($script, $chunks) {
//        $lines = explode("\n", $script);
//        $line_number_of_last_match = 2;
//        $this->assertEquals($line_number_of_last_match, find_chunks($lines, $chunks));
//    }
    
    public function nestedStatementsData() {
        return array(
           array(
                 'script' => "a = 1;
do box {
    box→use_horizontal_layout;
    b = 134;
    do box {
        c = 546;
    }
}",
                'chunks' => array(
                    'do box {
                box→use_horizontal_layout;',
                    'do box {',
                    '}',
                    '}'
                )
            )
        );
    }
    /**
     * @test 
     * @dataProvider nestedStatementsData
     */
//    public function testNested($script, $chunks)
//    {
//        $lines = explode("\n", $script);
//        $this->assertTrue(find_chunks($lines, $chunks));
//    }

}

