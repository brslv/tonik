<?php

use Tonik\Entities\Url;
use Tonik\Entities\UrlComponent;

class UrlTest extends PHPUnit_Framework_TestCase
{
    public function testItInstantiates()
    {
        $url = new Url('/about');

        $isUrlObject = $url instanceof Url;
        $expect = true;
        $this->assertEquals($expect, $isUrlObject);
    }

    public function testGet()
    {
        $url = new Url('/about');

        $theUrl = $url->get();
        $expect = '/about';
        $this->assertEquals($expect, $theUrl);

        /** --- */

        $url = new Url('/about?name=Borislav');

        $theUrl = $url->get();
        $expect = '/about';
        $this->assertEquals($expect, $theUrl);
    }

    public function testComponents()
    {
        $url = new Url('/foo/bar/baz/fuzz');

        $components = $url->components();

        $ok = true;
        foreach ($components as $component) {
            if ( ! ($component instanceof UrlComponent)) {
                $ok = false;
            }
        }

        $expect = true;
        $this->assertEquals($expect, $ok);
    }
}
