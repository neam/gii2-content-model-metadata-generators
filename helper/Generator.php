<?php
/**
 * @link http://neamlabs.com/
 * @copyright Copyright (c) 2015 Neam AB
 */

namespace neam\gii2_content_model_metadata_generators\helper;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * This generator will generate a helper class to access metadata about the content model.
 *
 * @author Fredrik Wollsén <fredrik@neam.se>
 * @since 1.0
 */
class Generator extends \neam\gii2_content_model_metadata_generators\ContentModelMetadataGenerator
{

    public $ns = 'app\helpers';

    /**
     * @inheritdoc
     */
    public $templates = [
        'yii' => '@vendor/neam/yii2-content-model-metadata-generators/helper/yii',
        'yii2' => '@vendor/neam/yii2-content-model-metadata-generators/helper/yii2',
    ];

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Content Model Metadata Helper Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a helper class to access metadata about the content model.';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['helper.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];

        $helperName = 'ItemTypes';

        $cmm = $this->getContentModelMetadata();
        $params = [
            'itemTypes' => $cmm->itemTypes,
            'itemTypesByBooleanAttributes' => $this->generateItemTypesByBooleanAttributes($cmm->itemTypes),
            'helperName' => $helperName,
            'ns' => $this->ns,
        ];

        $modelTraitFile = Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $helperName . '.php';
        $files[] = new CodeFile(
            $modelTraitFile,
            $this->render('helper.php', $params)
        );

        return $files;
    }

    public function generateItemTypesByBooleanAttributes($itemTypes)
    {
        $itemTypesByBooleanAttributes = [];
        foreach (array_keys((array) $itemTypes[0]) as $attribute) {
            if (strpos($attribute, "is_") === false && strpos($attribute, "generate_") === false) {
                continue;
            }
            $matchingItemTypes = [];
            foreach ($itemTypes as $itemType) {
                if ($itemType->$attribute) {
                    $matchingItemTypes[] = $itemType;
                }
            }
            $itemTypesByBooleanAttributes[$attribute] = $matchingItemTypes;
        }
        //var_dump($itemTypes[0], $itemTypesByBooleanAttributes);die();
        return $itemTypesByBooleanAttributes;
    }

}
