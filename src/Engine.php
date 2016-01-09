<?php

namespace Templating;


use Storage\Loader;

/**
 * Class Engine
 * @package Templating
 * 
 * the engine should do everything
 * take a loader, or not (in which case it would only render string templates)
 * detect which type of template is being rendered from what it will detect in it
 * parse the keys with the values that have been passed
 * the values could be an array or an object
 * 
 */
class Engine implements Renderer
{
    
    private $regexes = [
        '\Templating\getStringReplacementCallback' => Regexes::STRING_REGEX,
        '\Templating\getVariableReplacementCallback' => Regexes::VARIABLE_REGEX,
        '\Templating\getPropertyReplacementCallback' => Regexes::PROPERTY_REGEX,
        //'old_variables' => '/\$\w+/',
        //'functions' => '/{[\w]+\(\)}/',
        //'flow' => '/{\w+ \w+=\w+}/',
    ];
    
    private $stringTemplateOnly = true;
    private $loader;
    
    public function __construct(Loader $loader = null)
    {
        if ($loader !== null)
        {
            $this->loader = $loader;
            $this->stringTemplateOnly = false;
        }
    }

    public function render($template, $value = null)
    {
        $templateString = $this->stringTemplateOnly 
            ? $template 
            : $this->loader->getAsString($template);
        
        return $value === null
            ? $templateString
            : $this->parse($templateString, $value);
    }
    
    private function parse($string, $context)
    {
        foreach ($this->regexes as $callbackCaller => $regex)
        {
            $string = preg_replace_callback($regex, $callbackCaller($context), $string);
        }
        return $string;
    }
    
    

}