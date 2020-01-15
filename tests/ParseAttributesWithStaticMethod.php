<?php
use PHPUnit\Framework\TestCase;
use Lindan\Tranformation\XmlModel\XmlModel;

final class ParseAttributesWithStaticMethod extends TestCase {

    public function setup(){
        
    }
    public function testWhenClassIsNormalClass(){
        $dom= new DOMDocument();
        $dom->load(__DIR__.'/xml.xml');
        $itemList=$dom->getElementsByTagName("house");

        $house= new House();        
        XmlModel::parseFromArray($itemList->item(0),$house,['address','number','bedrooms']);

        $this->assertNotNull($house->address);
        $this->assertNotNull($house->number);
        $this->assertNotNull($house->bedrooms);
        echo json_encode($house);
    }

}