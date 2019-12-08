# Deserializer Xml to Php classes

[![Build Status](https://travis-ci.org/DanNYSPD/Transformation.svg?branch=master)](https://travis-ci.org/DanNYSPD/Transformation)

Xarenisoft XmlModel is a simple class that you can extend to deserialize and XML Tag and its attributes to a Php Class.

```php
class Person extends XmlModel{
    public $attributes=['name','age'];
    public $name; //in this property the value of name attribute will be stored if it's found when you call parseAttributes method

    public $age;//another attribute

    //you can also define a children array to indicate that Person has an Addres Child or children (internally a getElementsByTag is use)
    public $children=[
        'Address'=>Address::class # with this , we indicate that we are gonna take only the first element, if we have a set of elements we can use * after the propertyName
        'Friends*'=>Friend::class
    ]
    public $Address;
    /***
     *  
     * @var Friend[]
     */
    public $Friends;

}
class Address extends XmlModel{
    //if we not set the attributes array by default all the class properties will be taken as attributes and the names of the properties will be used as the attribute name.
    public $street;
}
class Friend {
    public $name;
}

#some code before to get the node:
$person= new Person($node);
$person->parseAttributes();
$person->parseChildren();
#now the object person has the attributes (if they are correctly defined in the node) and the Friends array populated with the Objects.