<?php
namespace Tests;
use stdClass;
use Storage\FileLoader;
use Templating\Engine;
use Templating\RznKeysRenderer;
use Templating\TemplateParser;
use PHPUnit\Framework\TestCase;
class EngineTest extends TestCase
{
    public function testReplace()
    {
        $parser = new TemplateParser;
        $values = [
            'first' => 12,
            'second' => 'hello',
        ];
        $template = 'template with {$first} and {$second}';
        $replaced = 'template with 12 and hello';
        $this->assertEquals($replaced, $parser->replaceVars($template, $values));
    }
    public function testHtmlStringRendering()
    {
        $loader = new FileLoader(__DIR__ . "/templates", "html");
        $rznKeysRenderer = new RznKeysRenderer($loader);
        $values = [
            '{zValue}' => 'replaced',
            '{zSecondValue}' => 'replaced2',
        ];
        $string = $rznKeysRenderer->render('string_template', $values);
        $this->assertEquals('replacedreplaced2', $string);
    }
    public function testEngine()
    {
        $loader = new FileLoader(
            __DIR__ . "/templates",
            "tpl"
        );
        $engine = new Engine($loader);
        $rendered = $engine->render(
            'template_futur',
            [
                'posts' => 'post',
                'zValue' => 'post',
            ]
        );
        $this->assertEquals('postpost', $rendered);
        $stringEngine = new Engine;
        $object = new stdClass;
        $object->posts = 'post';
        $stringTemplate = '{$posts}';
        $this->assertEquals('post', $stringEngine->render($stringTemplate, $object));
    }
    public function testPropertiesReplace()
    {
        $engine = new Engine;
        $post = new stdClass;
        $post->value = 'string';
        $string = $engine->render('{$post->value}', $post);
        $this->assertEquals('string', $string);
        $string = $engine->render(
            '{$post->value}',
            [
                'post' => $post,
            ]
        );
        $this->assertEquals('string', $string);
    }
    public function testStringReplacement()
    {
        $engine = new Engine;
        $string = $engine->render(
            '{zValue}{zTransform}{simple}',
            [
                '{zValue}' => 'valeur',
                'transform' => 'please',
                'simple' => 'work',
            ]
        );
        $this->assertEquals('valeurpleasework', $string);
    }
    public function testCanParseStringWhenNecessary()
    {
        $loader = new FileLoader(__DIR__);
        $engine = new Engine($loader);
        $string = $engine->render(
            '{$value}',
            [
                'value' => 'ok',
            ]
        );
        $this->assertEquals('ok', $string);
    }
    public function testMixing()
    {
        $templateString = '{first}{$second}{$obj->third}';
        $engine = new Engine;
        $obj = new stdClass;
        $obj->third = 'sentence.';
        $string = $engine->render(
            $templateString,
            [
                'first' => 'This ',
                'second' => 'is a ',
                'obj' => $obj,
            ]
        );
        $this->assertEquals('This is a sentence.', $string);
    }
}
