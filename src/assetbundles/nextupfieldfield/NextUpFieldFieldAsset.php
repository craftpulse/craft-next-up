<?php
/**
 * Next Up plugin for Craft CMS 3.x
 *
 * Get the next upcoming event date from the matrix
 *
 * @link      https://percipio.london/
 * @copyright Copyright (c) 2022 Percipio.london
 */

namespace percipiolondon\nextup\assetbundles\nextupfieldfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Percipio.london
 * @package   NextUp
 * @since     1.0.0
 */
class NextUpFieldFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@percipiolondon/nextup/assetbundles/nextupfieldfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/NextUpField.js',
        ];

        $this->css = [
            'css/NextUpField.css',
        ];

        parent::init();
    }
}
