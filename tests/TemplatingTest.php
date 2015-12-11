<?php

class TemplatingTest extends \Tests\BaseTestCase
{
    public function testRenders()
    {
        $view = new \Tests\ExampleTemplate();
        $this->assertInstanceOf('Tests\ExampleTemplate',$view);

        $string = $view->render(__DIR__.'/templates/test_template.php');
        $this->assertInternalType('string',$string);

    }
    public function testException()
    {
        $this->setExpectedException('Templating\Exceptions\InvalidFileException');
        $view = new \Tests\ExampleTemplate();
        $view->render('nonfile');
    }

    public function testAcceptsObjectAndRendersIt()
    {
        $view = new \Tests\ExampleViewObject();
        $renderer = new \Templating\PhpTemplatesRenderer();
        $string = $renderer->render(__DIR__.'/templates/test_template.php',$view);
        $this->assertInternalType('string',$string);
        $this->assertContains('default body',$string);
    }

    public function testAcceptsArrayAndRendersValues()
    {
        $renderer = new \Templating\PhpTemplatesRenderer();
        $string = $renderer->render(__DIR__.'/templates/test_template.php',[
            'body' => 'body from array',
        ]);
        $this->assertInternalType('string',$string);
        $this->assertContains('body from array',$string);
    }

}