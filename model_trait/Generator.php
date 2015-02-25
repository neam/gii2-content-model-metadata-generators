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

    public $enableI18N = true;
    public $messageCategory = 'app';
    public $ns = 'app\models\metadata\traits';

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

        foreach ($this->getMatchingItemTypes() as $itemType) {

            $traitName = $itemType->model_class . 'Trait';

            $dependencies = $this->generateDependencies($itemType);
            //var_dump($dependencies);die();

            $params = [
                'itemType' => $itemType,
                'traitName' => $traitName,
                'ns' => $this->ns,
                'statusRequirements' => $itemType->is_preparable ? $this->generateStatusRequirements($itemType) : [],
                'flowSteps' => $itemType->is_workflow_item ? $this->generateFlowSteps($itemType) : [],
                'flowStepCaptions' => $itemType->is_workflow_item ? $this->generateFlowStepCaptions($itemType) : [],
                'labels' => $this->generateLabels($itemType),
                'hints' => $this->generateHints($itemType),
                'traits' => $dependencies['traits'],
                'mixins' => $dependencies['mixins'],
                'rules' => $dependencies['rules'],
                'relations' => $dependencies['relations'],
                'attributes' => $dependencies['attributes'],
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
    protected function getMatchingItemTypes()
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

    public function generateStatusRequirements($itemType)
    {
        $statusRequirements = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->preparableStatusRequirement)) {
                continue;
            }
            $statusRequirements[$attribute->preparableStatusRequirement->ref][] = $attribute->ref;
        }
        return $statusRequirements;
    }

    public function generateFlowSteps($itemType)
    {
        $flowSteps = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->workflowItemStep)) {
                continue;
            }
            $flowSteps[$attribute->workflowItemStep->ref][] = $attribute->ref;
        }
        return $flowSteps;
    }

    public function generateFlowStepCaptions($itemType)
    {
        $flowStepCaptions = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->workflowItemStep)) {
                continue;
            }
            $flowStepCaptions[$attribute->workflowItemStep->ref] = $attribute->workflowItemStep->_title;
        }
        return $flowStepCaptions;
    }

    /**
     * Generates the attribute labels for the specified item type.
     * @param stdCass $itemType the item type metadata
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($itemType)
    {
        $labels = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->label)) {
                continue;
            }
            $labels[$attribute->ref] = $attribute->label;
        }
        return $labels;
    }

    /**
     * Generates the attribute labels for the specified item type.
     * @param stdCass $itemType the item type metadata
     * @return array the generated attribute labels (name => label)
     */
    public function generateHints($itemType)
    {
        $labels = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->hint)) {
                continue;
            }
            $labels[$attribute->ref] = $attribute->hint;
        }
        return $labels;
    }

    const MIXIN_HAS_MANY_HANDSONTABLE_INPUT = 'has-many-handsontable-input';
    const MIXIN_I18N_ATTRIBUTE_MESSAGES = 'i18n-attribute-messages';
    const MIXIN_I18N_COLUMNS_ATTRIBUTE = 'i18n-columns-attribute';
    const MIXIN_I18N_COLUMNS_RELATION = 'i18n-columns-relation';
    const MIXIN_OWNABLE = 'ownable';
    const MIXIN_PERMALINKABLE_FILES = 'permalinkable-files';
    const MIXIN_PERMALINKABLE_ITEM = 'permalinkable-item';
    const MIXIN_RESTRICTED_ACCESS = 'restricted-access';
    const MIXIN_RELATIONAL_GRAPH_DB = 'relational-graph-db';
    const MIXIN_RELATED_ITEMS_SIR_TREVOR_UI = 'related-items-sir-trevor-ui-behavior';
    const MIXIN_QA_STATE = 'qa-state';

    protected function generateDependencies($itemType)
    {

        $traits = [];
        $mixins = [];
        $rules = [];
        $relations = [];
        $attributes = [];

        // is_translatable
        if ($itemType->is_translatable) {

            // Get attributes to translate and what mixin to use
            foreach ($itemType->attributes as $attribute) {
                if (empty($attribute->translatableBehaviorChoice)) {
                    continue;
                }

                // Check attribute type


                $mixins[$attribute->translatableBehaviorChoice->ref][] = $attribute->ref;
            }

            $rules[] = '$this->i18nRules()';

        }

        // is_listable
        if ($itemType->is_listable) {

            $attributes[] = 'thumb';
            $attributes[] = 'heading';
            $attributes[] = 'subheading';
            $attributes[] = 'caption';

        }

        // is_presentable
        if ($itemType->is_presentable) {

            $attributes[] = 'about';
            $attributes[] = 'related';
            $attributes[] = 'contributions';

        }

        // is_composable
        if ($itemType->is_composable) {

            $attributes[] = 'composition';
            $attributes[] = 'composition_type_id';

        }

        // is_graph_relatable
        if ($itemType->is_graph_relatable) {

            $traits[] = 'GraphRelatableItemTrait';
            $mixins[static::MIXIN_RELATIONAL_GRAPH_DB] = [];
            $relations[] = '$this->graphRelatableItemBaseRelations()';
            $attributes[] = 'node_id';

        }

        // is_permalinkable
        if ($itemType->is_permalinkable) {

            $traits[] = '\neam\yii_permalinkable_items_core\traits\PermalinkableItemTrait';
            $mixins[static::MIXIN_HAS_MANY_HANDSONTABLE_INPUT][] = 'routes';
            $mixins[static::MIXIN_PERMALINKABLE_ITEM] = [];
            $relations[] = '$this->permalinkableItemRelations()';
            $attributes[] = 'routes';

        }

        // has_permalinkable_files
        $permalinkable_file_route_attribute_refs = [];
        foreach ($itemType->attributes as $attribute) {
            // Check for file route attributes
            if (!empty($attribute->permalinkable_file_route_attribute_ref)) {
                $permalinkable_file_route_attribute_refs[] = $attribute->permalinkable_file_route_attribute_ref;
            }
        }
        if (!empty($permalinkable_file_route_attribute_refs)) {
            $traits[] = '\neam\yii_permalinkable_items_core\traits\PermalinkableItemTrait';
            $mixins[static::MIXIN_HAS_MANY_HANDSONTABLE_INPUT][] = 'fileRoutes';
            $mixins[static::MIXIN_PERMALINKABLE_FILES] = $permalinkable_file_route_attribute_refs;
        }

        // attributes with edit_relation_using_handsontable_input
        foreach ($itemType->attributes as $attribute) {
            if (!empty($attribute->edit_relation_using_handsontable_input)) {
                $mixins[static::MIXIN_HAS_MANY_HANDSONTABLE_INPUT][] = $attribute->ref;
            }
        }

        // attributes with edit_relation_using_sir_trevor_ui
        foreach ($itemType->attributes as $attribute) {
            if (!empty($attribute->edit_relation_using_sir_trevor_ui)) {
                $mixins[static::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI][] = [$attribute->ref => $attribute->graph_relation_item_type_constraint];
            }
        }

        // is_ownable
        if ($itemType->is_ownable) {

            $mixins[static::MIXIN_OWNABLE] = [];
            $attributes[] = 'owner_id';

        }

        // is_workflow_item
        if ($itemType->is_workflow_item) {

            //$traits[] = '\neam\yii_workflow_core\traits\ItemTrait';
            $traits[] = 'ItemTrait';
            $mixins[static::MIXIN_HAS_MANY_HANDSONTABLE_INPUT][] = 'changesets';
            $rules[] = '$this->flowStepRules()';
            $relations[] = "array('changesets' => array(CActiveRecord::HAS_MANY, 'Changeset', array('id' => 'node_id'), 'through' => 'node'))";

        }

        // is_preparable
        if ($itemType->is_preparable) {

            $mixins[static::MIXIN_QA_STATE] = [];
            $rules[] = '$this->statusRequirementsRules()';
            $attributes[] = $itemType->table . '_qa_state_id';

        }

        // is_access_restricted
        if ($itemType->is_access_restricted) {

            $traits[] = 'RestrictedAccessItemTrait';
            $mixins[static::MIXIN_RESTRICTED_ACCESS] = [];
            $attributes[] = 'node_id';
            $attributes[] = 'owner_id';

        }

        // is_versioned
        if ($itemType->is_versioned) {

            $attributes[] = 'version';
            $attributes[] = 'cloned_from_id';

        }

        // is_timestamped
        if ($itemType->is_timestamped) {

            $attr = $itemType->attributes;

            // generate correct settings for created and/or modified fields
            if (array_key_exists("created", $attr) && array_key_exists("modified", $attr)) {
                $behaviors['CTimestampBehavior'] = array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute' => 'created',
                    'updateAttribute' => 'modified',
                );
            }
            if (array_key_exists("created", $attr) && !array_key_exists("modified", $attr)) {
                $behaviors['CTimestampBehavior'] = array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute' => 'created',
                    'updateAttribute' => null,
                );
            }

        }

        // is_labeled
        if ($itemType->is_labeled) {

            $attributes[] = 'label';

        }

        return [
            'traits' => $traits,
            'mixins' => $mixins,
            'rules' => $rules,
            'relations' => $relations,
            'attributes' => $attributes,
        ];

    }

}
