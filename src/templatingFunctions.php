<?php declare(strict_types=1);

namespace Zvax\Templating;

use Closure;

function getForeachReplacementCallback(mixed $context, Renderer $renderer): Closure
{
    return static function ($match) use ($context, $renderer) {
        $key = $match[1];
        $array = getValueFromContext($key, $context);
        if (!is_array($array)) {
            return "$key is not an array";
        }
        $subKey = $match[2];
        $template = $match[3];
        $string = '';
        foreach ($array as $value) {
            $string .= $renderer->render(
                $template,
                [
                    $subKey => $value,
                ]
            );
        }
        return $string;
    };
}

function getStringReplacementCallback(mixed $context): Closure
{
    return static function ($match) use ($context) {
        $key = $match[1];
        if ($value = getValueFromContext($key, $context)) {
            return $value;
        }

        if (str_starts_with($match[0], '{z')) {
            $value = getValueFromContext($match[0], $context);
            if ($value !== false) {
                return $value;
            }
            $subkey = strtolower(substr($key, 1));
            $value = getValueFromContext($subkey, $context);
            if ($value !== false) {
                return $value;
            }
        }
        return $match[0];
    };
}

function getVariableReplacementCallback(mixed $values): Closure
{
    return static function ($match) use ($values) {
        $key = $match[2];
        if (($value = getValueFromContext($key, $values)) !== false) {
            return $value;
        }
        return $match[0];
    };
}

function getPropertyReplacementCallback(mixed $values): Closure
{
    return static function ($match) use ($values) {
        $className = $match[1];
        $property = $match[2];
        if (is_array($values)) {
            if ($instance = getValueFromContext($className, $values)) {
                return property_exists($instance, $property)
                    ? $instance->$property
                    : $match[0];
            }
        } else {
            if (is_scalar($values)) {
                return $values;
            }

            if (property_exists($values, $property)) {
                return $values->$property;
            }
        }
        return $match[0];
    };
}

function getValueFromContext(string $key, mixed $context): mixed
{
    if (is_array($context)) {
        if (array_key_exists($key, $context)) {
            $value = $context[$key];
            if ($value === false || $value === null) {
                return '';
            }
            return $value;
        }
        return false;
    }

    if (property_exists($context, $key)) {
        $value = $context->$key;
        if ($value === false || $value === null) {
            return '';
        }
        return $context->$key;
    }
    return false;
}
