<?php
/**
 * @var yii\web\View $this
 * @var yii\gii\generators\model\Generator $generator
 * @var stdClass $itemType item type metadata
 */

echo "<?php\n";
?>

/**
 * This is the content model metadata trait for item type "<?= $itemType->model_class ?>".
 */
trait <?= $traitName . "\n" ?>
{
    /**
     * Define status-dependent fields
     * @return array
     */
    public function statusRequirements()
    {
        return array(
<?php foreach ($statusRequirements as $statusRequirementRef => $statusRequirement): ?>
            '<?= "$statusRequirementRef" ?>' => array(
    <?php foreach ($statusRequirement as $attribute): ?>
                <?= "'$attribute',\n" ?>
    <?php endforeach; ?>
            ),
<?php endforeach; ?>
        );
    }

    /**
     * Define step-dependent fields
     * @return array
     */
    public function flowSteps()
    {
        return array(
<?php foreach ($flowSteps as $flowStepRef => $flowStep): ?>
            '<?= "$flowStepRef" ?>' => array(
    <?php foreach ($flowStep as $attribute): ?>
                <?= "'$attribute',\n" ?>
    <?php endforeach; ?>
            ),
<?php endforeach; ?>
        );
    }

    public function flowStepCaptions()
    {
        return array(
<?php foreach ($flowStepCaptions as $name=>$flowStepCaption): ?>
            <?= "'$name' => " . $generator->generateString($flowStepCaption) . ",\n" ?>
<?php endforeach; ?>
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(), array(
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(
            parent::attributeHints(), array(
<?php foreach ($hints as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
            )
        );
    }

}
