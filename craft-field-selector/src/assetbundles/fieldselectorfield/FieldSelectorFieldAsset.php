<?php
/**
 * Field Selector plugin for Craft CMS 3.x
 *
 * Select fields from an element on the element 
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\fieldselector\assetbundles\fieldselectorfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Kurious Agency
 * @package   ElementFieldSelector
 * @since     1.0.0
 */
class FieldSelectorFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@kuriousagency/fieldselector/assetbundles/fieldselectorfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/FieldSelector.js',
        ];

        $this->css = [
            'css/FieldSelector.css',
        ];

        parent::init();
    }
}
