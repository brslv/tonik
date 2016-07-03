<?php

use Tonik\Entities\Url;
use Tonik\Entities\UrlComponent;

class UrlComponentTest extends PHPUnit_Framework_TestCase 
{
    public function testItInstantiates()
    {
        $urlComponent = new UrlComponent('name');

        $expect = true;
        $this->assertEquals($expect, $urlComponent instanceof UrlComponent);
    }

    public function testGet()
    {
        $urlComponent = new UrlComponent('name');

        $uc = $urlComponent->get();
        $expect = 'name';
        $this->assertEquals($expect, $uc);
    }
}
