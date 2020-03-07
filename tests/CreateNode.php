<?php
use PHPUnit\Framework\TestCase;

final class CreateNode extends TestCase {

    public function setup(){
        
    }
    public function testCreateNodeFromModel(){
        $dom= new DOMDocument();

        #$dom->appendChild($dom->createElement("ha"));

       $pet= new Characteristics();
       $pet->setDomDocument($dom);
       $c= new Characteristic();
       $c->name="dsa";
       $c->description="description";
       $pet->CharacteristicList[]=$c;
       $dom->appendChild($pet->createNodeAndPopulate("ha"));
       echo $dom->saveXML();
    }

}