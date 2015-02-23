<?php
/**
 * @var yii\web\View $this
 * @var yii\gii\generators\model\Generator $generator
 * @var stdClass[] $itemTypes item types metadata
 */

echo "<?php\n";
?>

/**
 * Content model metadata helper class for enumerating item types
 */
class <?= $helperName . "\n" ?>
{

    static public function all()
    {
        return [
<?php foreach ($itemTypes as $itemType): ?>
            '<?= $itemType->model_class; ?>' => '<?= $itemType->table; ?>',
<?php endforeach; ?>
        ];
    }

    static public function label($modelClass, $n)
    {

    }

    /**
     * Frontend UI labels for the models
     * @return array
     */
    static public function choiceFormatLabels()
    {
        return [
<?php foreach ($itemTypes as $itemType): ?>
            '<?= $itemType->model_class; ?>' => '<?= $itemType->title_choice_format; ?>',
<?php endforeach; ?>
        ];
    }

    /**
     * Slugs for the model classes
     * Used when setting semantic routes
     * @return array
     */
    static public function modelSlugs()
    {
        return [
<?php foreach ($itemTypes as $itemType): ?>
            '<?= $itemType->model_class; ?>' => '<?= $itemType->slug; ?>',
<?php endforeach; ?>
        ];
    }

}
