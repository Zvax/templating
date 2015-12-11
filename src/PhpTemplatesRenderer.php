<?php

namespace Templating;

use Templating\Exceptions\InvalidFileException;

class PhpTemplatesRenderer implements Renderer
{
    public function render($template, $value = null)
    {
        if (!file_exists($template))
        {
            throw new InvalidFileException($template);
        }
        ob_start();
        $this->activate($template,$value);
        return ob_get_clean();
    }

    private function activate($template, $value)
    {
        $scope = function() use ($template) { require $template; };
        if ($value !== null)
        {
            if (is_array($value))
            {
                $value = $this->makeObject($value);
            }
            $scope = $scope->bindTo($value);
        }
        $scope();
    }

    private function makeObject(array $value)
    {
        $newValue = new \stdClass();
        foreach ($value as $key => $content)
        {
            $newValue->$key = $content;
        }
        return $newValue;
    }

}