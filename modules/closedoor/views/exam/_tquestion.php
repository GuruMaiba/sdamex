<?php

/* Приём параметров
 * ----------------------
 * $i        - инкремент
 * $model    - модель
 * $corr     - правильные ответы
 * $str      - если нужно вернуть строкой
 * $themes   - темы
 */

 ?>

<?php if (!empty($model)): ?>
<?php foreach ($model as $qst): ?>
<?php if ($str): ?>`<?php endif; ?>
    <div id="question_<?=$i?>" class="question" style="padding-bottom: 40px;" number="<?=$i?>">
        <span class="delete">Х</span>
        <label class="control-label" for="question_<?=$i?>-text">Вопрос-<?=$i?></label>
        <input type="hidden" name="Test[questions][<?=$i?>][id]" value="<?=$qst->id?>">
        <div class="form-group">
            <input class="form-control" type="text" name="Test[questions][<?=$i?>][place]" placeholder="Порядковый номер" value="<?= $qst->place ?>">
            <textarea id="question_<?=$i?>-text" class="form-control" name="Test[questions][<?=$i?>][text]"><?=strip_tags($qst->text)?></textarea>
        </div>
        <input class="qstThemes" type="hidden" name="Test[questions][<?=$i?>][themes]"
            value='<?=($corr[$qst->id]['themes'])?json_encode($corr[$qst->id]['themes']):''?>'>
        <?= $this->render('/theme/_themes', [
            'model' => $themes,
            'hide' => true,
            'active' => $corr[$qst->id]['themes'],
        ]); ?>
        <!-- MULTIPLE -->
        <div class="checkbox-inline">
            <label><input type="checkbox" name="Test[questions][<?=$i?>][multiple_answer]" value='1' <?php if($qst->multiple_answer){echo 'checked';}?>>Множественный ответ</label>
        </div>
        <div class="checkbox-inline">
            <label><input type="checkbox" name="Test[questions][<?=$i?>][hard]" value='1' <?php if($qst->hard){echo 'checked';}?>>Сложный вопрос - удвоенный опыт</label>
        </div>
        <div style="padding-left: 60px;">
            <hr>
            <div class="answers">
                <?= $this->render('_tanswer', [
                    'model' => $qst->answers,
                    'corr' => $corr[$qst->id]['answers'],
                    'i' => $i,
                    'j' => 1,
                ]) ?>
            </div>
            <div class="btn btn-primary btnAddAns"> Добавить ответ </div>
        </div>
    </div>
<?php if ($str): ?>`<?php endif; ?>
<?php ++$i; endforeach; ?>
<?php endif; ?>