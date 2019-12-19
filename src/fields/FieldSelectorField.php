<?php
/**
 * Field Selector plugin for Craft CMS 3.x
 *
 * Select fields from an element on the element 
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\fieldselector\fields;

use kuriousagency\fieldselector\FieldSelector;
use kuriousagency\fieldselector\assetbundles\fieldselectorfield\FieldSelectorFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;
use benf\neo\elements\Block as NeoBlock;

/**
 * @author    Kurious Agency
 * @package   ElementFieldSelector
 * @since     1.0.0
 */
class FieldSelectorField extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $showAll = true;
    // public $someAttribute = 'Some Default';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('field-selector', 'FieldSelector');
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
            ['showAll','boolean'],
            ['showAll','default','value'=>true]
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
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'field-selector/_components/fields/FieldSelector_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(FieldSelectorFieldAsset::class);
        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);
        // Get some variables
        if (get_class($element) == NeoBlock::class) {
            if (isset($element->ownerId)) {
                $element = Craft::$app->getElements()->getElementById($element->ownerId);
            }
        }
        $elementFields = $element->getFieldLayout()->getFields();
        $options = [];
        foreach ($elementFields as $field) {
            if (!($field === $this)) {
                $options[] = ['value' => $field->handle,'label' => $field->name];
            }
        } 
        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            'options' => $options
            ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').FieldSelectorFieldSelector(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'field-selector/_components/fields/FieldSelector_input',
            [
                'name' => $this->handle,
                'value' => json_decode($value,true),
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'options' => $options
            ]
        );
    }
}
