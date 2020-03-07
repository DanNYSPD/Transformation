<?php

use PHPUnit\Framework\TestCase;

final class ChildrenTest extends TestCase {

    public function setup(){
        
    }
    public function test(){
        $dom= new DOMDocument();
        $dom->load(__DIR__.'/xml.xml');
        $itemList=$dom->getElementsByTagName("pet");

        $pet= new Pet($itemList->item(0));
        $pet->parseAttributes($itemList->item(0));
        $pet->parseChildren();

        $this->assertNotEmpty($pet->characteristics);
        $this->assertNotEmpty($pet->characteristics->CharacteristicList);
        $this->assertEquals(2,count($pet->characteristics->CharacteristicList));
        #echo json_encode($pet);
        echo "\n";
        echo json_encode($pet->characteristics);
        echo json_encode($pet->characteristics->CharacteristicList);
    }
    public function testWhenPropertyNameAndTagNameAreDifferentForSingleChild(){
        $dom= new DOMDocument();
        $dom->load(__DIR__.'/xml.xml');
        $itemList=$dom->getElementsByTagName("pet");

        $pet= new Pet($itemList->item(0));
        $pet->parseAttributes($itemList->item(0));
        $pet->parseChildren();

        
        $this->assertNotEmpty($pet->raceObj);
        
        #echo json_encode($pet);
        
    }
}