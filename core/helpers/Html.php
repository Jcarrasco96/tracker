<?php

namespace app\core\helpers;

class Html
{

    public static function css($css): string
    {
        return '<link href="' . Url::to('assets/css/' . $css) . '" rel="stylesheet">' . "\n";
    }

    public static function js($js): string
    {
        return '<script src="' . Url::to('assets/js/' . $js) . '"></script>' . "\n";
    }

    public static function icon($icon, $rel = 'icon'): string
    {
        return '<link href="' . Url::to('assets/' . $icon) . '" rel="' . $rel . '">' . "\n";
    }

    public static function img($img): string
    {
        return Url::to('assets/img/' . $img);
    }

    public static function uploadImg($img): string
    {
        return Url::to('uploads/' . $img);
    }

    public static function encode(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    public static function a(string $label, string $href = '#', array $options = []): string
    {
        if (isset($options['visible']) && !$options['visible']) {
            return '';
        }

        $a = '<a href="' . $href . '"';

        if (isset($options['active']) && $options['active']) {
            $options['class'] .= ' active';
        }

        if (isset($options['class'])) {
            $a .= ' class="' . $options['class'] . '"';
        }

        return $a . '>' . $label . '</a>';
    }

}