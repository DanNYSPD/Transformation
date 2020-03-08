<?php
use PHPUnit\Framework\TestCase;

final class CreateNodeTest extends TestCase {

    public function setup(){
        
    }
    public function testCreateNodeFromModel(){
        $dom= new DOMDocument();

        #$dom->appendChild($dom->createElement("ha"));

       $cm= new Characteristics();
       $cm->race=new Race();
       $breed=new Breed();
       $breed->bahevior="hoa";
       $breed->specie= new Specie();
       $cm->breed= $breed;
       $cm->setDomDocument($dom);
       $c= new Characteristic();
       $c->name="1";
       $c->description="description1";
       $c->breed= new Breed();
      
       $c2= new Characteristic();
       $c2->name="2";
       $c2->description="description2";
       $cm->CharacteristicList[]=$c;
       $cm->CharacteristicList[]=$c2;
       $dom->appendChild($cm->createNodeAndPopulate("ha"));
       echo $dom->saveXML();
        #at least I must have onew child node named ha:
       $this->assertEquals(1,$dom->getElementsByTagName("ha")->length);
       $characteristicList=$dom->getElementsByTagName("ha")->item(0)->getElementsByTagName("characteristic");

       $this->assertEquals(2,$characteristicList->length);

       #Now I must confirms that the elements have the rigth values in their properties:
       $this->assertEquals($c->name,$characteristicList->item(0)->getAttribute("name"));
       $this->assertEquals($c->description,$characteristicList->item(0)->getAttribute("description"));
       
       $this->assertEquals($c2->name,$characteristicList->item(1)->getAttribute("name"));
       $this->assertEquals($c2->description,$characteristicList->item(1)->getAttribute("description"));
    }

}