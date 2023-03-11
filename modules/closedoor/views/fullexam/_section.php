<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* Приём параметров
 * ----------------------
 * $model - модель Секции
 */

 ?>

<?php if (!empty($model)): ?>
<?php foreach ($model as $sect): ?>
    <div id="section_<?=$sect['id']?>" class="section" style="padding-bottom: 40px;" number="<?=$sect['id']?>">
        <?php $form = ActiveForm::begin([ 'id' => 'form_section_'.$sect['id'] ]); ?>
        <div class="form-group">
            <span class="delete delSection">Х</span>
            <input type="hidden" name="id" value="<?=$sect['id']?>">
            <input class="form-control changeSection" type="text" name="place"
                placeholder="№" style="display: inline-block; width: 50px;" value="<?=$sect['place']?>">
            <input class="form-control changeSection" type="text" name="name"
                placeholder="Имя секции" style="display: inline-block; width: 300px;" value="<?=$sect['name']?>">
            <!-- PUBLISH -->
            <div class="checkbox-inline">
                <label>
                    <input class="changeSection" type="checkbox" style="margin-left: -15px;"
                    name="publish" value='1'
                    <?php if($sect['publish']){echo 'checked';}?>>
                    Опубликовать
                </label>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <div style="padding-left: 40px;">
            <a href="<?=Url::to(['fullexam/exercise', 'section_id'=>$sect['id']])?>" class="btn btnAddExercise"> Добавить задание </a>
            <div class="exercises">
                <ul class="list-group">
                    <? foreach ($sect['exercises'] as $exe) :?>
                    <li class="list-group-item" number='<?=$exe['id']?>'>
                        <span class="delete delExercise">Х</span>
                        <a href="<?=Url::to(['fullexam/exercise/'.$exe['id'], 'section_id'=>$sect['id']])?>"><?=$exe['place']?>) <?=$exe['name']?></a>
                    </li>
                    <? endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>
