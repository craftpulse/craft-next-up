<?php
/**
 * Next Up plugin for Craft CMS 3.x
 *
 * Get the next upcoming event date from the matrix
 *
 * @link      https://percipio.london/
 * @copyright Copyright (c) 2022 Percipio.london
 */

namespace percipiolondon\nextup\fields;

use craft\validators\DateTimeValidator;
use percipiolondon\nextup\NextUp;
use percipiolondon\nextup\assetbundles\nextupfieldfield\NextUpFieldFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    Percipio.london
 * @package   NextUp
 * @since     1.0.0
 */
class NextUpField extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $nextUp = null;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('next-up', 'NextUpField');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['nextUp', DateTimeValidator::class],
        ]);
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(NextUpFieldFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').NextUpNextUpField(" . $jsonVars . ");");


        $css = <<<CSS
            #{$namespacedId}-field {
                display: none;
            }
        CSS;
        Craft::$app->getView()->registerCss($css);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'next-up/_components/fields/NextUpField_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }
}
