<?php

use Borislav\Broute\Entities\Url;
use Borislav\Broute\Entities\RouteComponent;

class RouteComponentTest extends PHPUnit_Framework_TestCase 
{
    public function testItInstantiates()
    {
        $routeComponent = new RouteComponent('{name}');

        $expect = true;
        $this->assertEquals($expect, $routeComponent instanceof RouteComponent);
    }

    public function testIsEager() 
    {
        $routeComponent = new RouteComponent('*');

        $isEager = $routeComponent->isEager();
        $expect = true;
        $this->assertEquals($expect, $isEager);

        /** --- */

        $routeComponent = new RouteComponent('{notEager}');

        $isEager = $routeComponent->isEager();
        $expect = false;
        $this->assertEquals($expect, $isEager);
    }

    public function testIsParameter() 
    {
        $routeComponent = new RouteComponent('notParameter');

        $isEager = $routeComponent->isParameter();
        $expect = false;
        $this->assertEquals($expect, $isEager);

        /** --- */

        $routeComponent = new RouteComponent('{parameter}');

        $isEager = $routeComponent->isParameter();
        $expect = true;
        $this->assertEquals($expect, $isEager);

        /** --- */

        $routeComponent = new RouteComponent('{optParameter?}');

        $isEager = $routeComponent->isParameter();
        $expect = true;
        $this->assertEquals($expect, $isEager);

        /** --- */

        $routeComponent = new RouteComponent('{');

        $isParameter = $routeComponent->isParameter();
        $expect = false;
        $this->assertEquals($expect, $isParameter);
    }

    public function testIsOptional() 
    {
        $routeComponent = new RouteComponent('{notOptional}');

        $isOptional = $routeComponent->isOptional();
        $expect = false;
        $this->assertEquals($expect, $isOptional);

        /** --- */

        $routeComponent = new RouteComponent('{optional?}');

        $isOptional = $routeComponent->isOptional();
        $expect = true;
        $this->assertEquals($expect, $isOptional);
    }

    public function testIsCompulsory()
    {
        $routeComponent = new RouteComponent('{notCompulsory?}');

        $isCompulsory = $routeComponent->isCompulsory();
        $expect = false;
        $this->assertEquals($expect, $isCompulsory);

        /** --- */

        $routeComponent = new RouteComponent('{compulsory}');

        $isCompulsory = $routeComponent->isCompulsory();
        $expect = true;
        $this->assertEquals($expect, $isCompulsory);
    }

    public function testIsPattern()
    {
        $routeComponent = new RouteComponent('{notPattern}');

        $isPattern = $routeComponent->isPattern();
        $expect = false;
        $this->assertEquals($expect, $isPattern);

        /** --- */

        $routeComponent = new RouteComponent('{name:(s)}');

        $isPattern = $routeComponent->isPattern();
        $expect = true;
        $this->assertEquals($expect, $isPattern);

        /** --- */

        $routeComponent = new RouteComponent('{name:( s )}');

        $isPattern = $routeComponent->isPattern();
        $expect = true;
        $this->assertEquals($expect, $isPattern);

        /** --- */

        $routeComponent = new RouteComponent('{name:( a-zA-Z0-5-_ )}');

        $isPattern = $routeComponent->isPattern();
        $expect = true;
        $this->assertEquals($expect, $isPattern);

        /** --- */

        $routeComponent = new RouteComponent('{name:( (abc)\.[a-z0-9] )}');

        $isPattern = $routeComponent->isPattern();
        $expect = true;
        $this->assertEquals($expect, $isPattern);
    }

    public function testItMatchesPattern()
    {
        $routeComponent = new RouteComponent('{name:( [abc] )}');

        $mathces = $routeComponent->matchesPattern('a');
        $expect = true;
        $this->assertEquals($expect, $mathces);

        /** --- */

        $routeComponent = new RouteComponent('{name:( [abc] )}');

        $mathces = $routeComponent->matchesPattern('r');
        $expect = false;
        $this->assertEquals($expect, $mathces);

        /** --- */

        $routeComponent = new RouteComponent('{name:( [a-z]{0,3} )}');

        $mathces = $routeComponent->matchesPattern('baz');
        $expect = true;
        $this->assertEquals($expect, $mathces);

        /** --- */

        $routeComponent = new RouteComponent('{name:( [a-z]{0,3} )}');

        $mathces = $routeComponent->matchesPattern('bazz');
        $expect = false;
        $this->assertEquals($expect, $mathces);

        /** --- */

        $routeComponent = new RouteComponent('{name:( [a-z]{0,3}-([0-9]) )}');

        $mathces = $routeComponent->matchesPattern('baz-5');
        $expect = true;
        $this->assertEquals($expect, $mathces);
    }

    public function testGet()
    {
        $routeComponent = new RouteComponent('{name}');

        $rc = $routeComponent->get();
        $expect = '{name}';
        $this->assertEquals($expect, $rc);
    }

    public function testGetNormalized()
    {
        $routeComponent = new RouteComponent('{name}');

        $rc = $routeComponent->getNormalized();
        $expect = 'name';
        $this->assertEquals($expect, $rc);

        /** --- */

        $routeComponent = new RouteComponent('{name?}');

        $rc = $routeComponent->getNormalized();
        $expect = 'name';
        $this->assertEquals($expect, $rc);

        /** --- */

        $routeComponent = new RouteComponent('{name:( [a-zA-Z0-9] )?}');

        $rc = $routeComponent->getNormalized();
        $expect = 'name';
        $this->assertEquals($expect, $rc);
    }
}
