<?php
/**
 * @link http://neamlabs.com/
 * @copyright Copyright (c) 2015 Neam AB
 */

namespace neam\yii_content_model_metadata_generators\model_trait;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * This generator will generate one or multiple model metadata traits for the specified item type(s).
 *
 * @author Fredrik Wollsén <fredrik@neam.se>
 * @since 1.0
 */
class Generator extends \neam\yii_content_model_metadata_generators\ContentModelMetadataGenerator
{

    public $ns = 'app\models\metadata';

    /**
     * @var null string
     */
    public $itemType = '*';

    /**
     * @inheritdoc
     */
    public $templates = [
        'yii' => '@vendor/neam/yii2-content-model-metadata-generators/model_trait/yii',
        'yii2' => '@vendor/neam/yii2-content-model-metadata-generators/model_trait/yii2',
    ];

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Content Model Metadata Model Trait Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator will generate one or multiple model metadata traits for the specified item type(s).';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['itemType'], 'safe'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'itemType' => 'Item Type(s)',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'itemType' => 'This is the name of the item type that the new trait is associated with, e.g. <code>post</code>.
                The item type may end with asterisk to match multiple item types, e.g. <code>foo*</code>
                will match item types whose name starts with <code>foo</code>. In this case, multiple traits
                will be generated, one for each matching item type.',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['trait.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];

        foreach ($this->getItemTypes() as $itemType) {

            $traitName = $itemType->model_class . 'Trait';

            $params = [
                'itemType' => $itemType,
                'traitName' => $traitName,
                //'labels' => $this->generateLabels($tableSchema),
                //'rules' => $this->generateRules($tableSchema),
                //'relations' => isset($relations[$className]) ? $relations[$className] : [],
                'ns' => $this->ns,
            ];

            $modelTraitFile = Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $traitName . '.php';
            $files[] = new CodeFile(
                $modelTraitFile,
                $this->render('trait.php', $params)
            );

        }

        return $files;
    }

    protected $itemTypes;

    /**
     * @return array the item types that match the pattern specified by [[itemType]].
     */
    protected function getItemTypes()
    {
        if ($this->itemTypes !== null) {
            return $this->itemTypes;
        }
        $cmm = $this->getContentModelMetadata();
        if ($cmm === null) {
            return [];
        }
        $itemTypes = [];

        $pattern = '/^' . str_replace('*', '\w+', $this->itemType) . '$/';
        foreach ($cmm->itemTypes as $itemType) {
            if (preg_match($pattern, $itemType->model_class)) {
                $itemTypes[] = $itemType;
            }
        }

        return $this->itemTypes = $itemTypes;
    }

}
