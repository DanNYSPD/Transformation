<?php
 
use PHPUnit\Framework\TestCase;
use Lindan\Tranformation\XmlModel\XmlModel;
use Lindan\Tranformation\XmlModel\XmlModelSerializer;
class Person extends XmlModelSerializer{

    public $attributes=[
        'name', #in this case property name is the same for the attribute
        'saü'=>'hi' #the property is hi but the attribute in the XML is saü
    ];
    public $name;

    public $hi;
}
class Car extends XmlModelSerializer{

    public $attributes=[
        'name', #in this case property name is the same for the attribute
        'model' 
    ];
    public $name;

    public $model;
}
final class ClassWithPropeties extends TestCase {

    public function setup(){
        
    }
    public function test(){
        $dom= new DOMDocument();
        $dom->load(__DIR__.'/xml.xml');
        $itemList=$dom->getElementsByTagName("car");

        $car= new Car();
        $car->parseAttributes($itemList->item(0)); 
        $this->assertEquals("Faster",$car->name);
        $this->assertEquals("0101",$car->model);
    }
    public function testPerson(){
        $dom= new DOMDocument();
        $dom->load(__DIR__.'/xml.xml');
        $itemList=$dom->getElementsByTagName("person");

        $person= new Person();
        $person->parseAttributes($itemList->item(0)); 
        $this->assertEquals("Dan",$person->name);
        $this->assertEquals("loremtest",$person->hi);
        #echo json_encode($person,JSON_PRETTY_PRINT);
    }

}