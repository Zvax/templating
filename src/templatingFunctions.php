<?php

namespace Templating;

function getForeachReplacementCallback($context, $renderer)
{
    /** @var Engine $renderer */
    $fn = function($match) use ($context, $renderer)
    {
        $key = $match[1];
        $array = getValueFromContext($key, $context);
        if(!is_array($array))
        {
            return "$key is not an array";
        }
        $subKey = $match[2];
        $template = $match[3];
        $string = '';
        foreach ($array as $value)
        {
            $string.= $renderer->render($template,[
                $subKey => $value,
            ]);
        }
        return $string;
    };
    return $fn;
}

function getStringReplacementCallback($context)
{
    $fn = function($match) use ($context)
    {
        $key = $match[1];
        if ($value = getValueFromContext($key, $context))
        {
            return $value;
        }
        else if (strpos($match[0],'{z') === 0) {
            if ($value = getValueFromContext($match[0], $context)) {
                return $value;
            }
            $subkey = strtolower(substr($key, 1));
            if ($value = getValueFromContext($subkey, $context)) {
                return $value;
            }
        }
        return $match[0];
    };
    return $fn;
}

function getVariableReplacementCallback($values)
{
    $callback = function($match) use ($values)
    {
        $key = $match[2];
        if (($value = getValueFromContext($key,$values)) !== false)
        {
            return $value;
        }
        return $match[0];
    };
    return $callback;
}

function getPropertyReplacementCallback($values)
{
    $callback = function($match) use ($values)
    {
        $className = $match[1];
        $property = $match[2];
        if (is_array($values))
        {
            if ($instance = getValueFromContext($className, $values))
            {
                return property_exists($instance, $property)
                    ? $instance->$property
                    : $match[0];
            }
        }
        if (property_exists($values,$property))
        {
            return $values->$property;
        }
        return $match[0];
    };
    return $callback;
}

function getValueFromContext($key, $context)
{
    if (is_array($context))
    {
        return array_key_exists($key, $context) ? $context[$key] : false;
    }
    else if (property_exists($context,$key))
    {
        return $context->$key;
    }
    return false;
}