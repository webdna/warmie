<?php

namespace webdna\warmie\utilities;

use Craft;
use craft\base\Utility;

/**
 * Warmie utility
 */
class Warmie extends Utility
{
    public static function displayName(): string
    {
        return Craft::t('warmie', 'Warmie');
    }

    static function id(): string
    {
        return 'warmie';
    }

    public static function iconPath(): ?string
    {
        return null;
    }

    static function contentHtml(): string
    {
        // todo: replace with custom content HTML
        return '';
    }
}
