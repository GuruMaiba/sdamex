<?php

/* Приём параметров
 * ----------------------
 * $model  - модель
 * $i      - инкремент
 * $str    - возврат строки
 * $themes - темы
 */

 ?>

<?php if (!empty($model)): ?>
<?php foreach ($model as $pair): ?>
<?php if ($str): ?>`<?php endif; ?>
    <div id="pair_<?=$i?>" class="pair" style="padding-bottom: 40px;" number="<?=$i?>">
        <div class="form-group">
            <span class="delete delPair">Х</span> <label>Пара соотношения</label>
            <input type="hidden" name="Pairs[<?=$i?>][id]" value="<?=$pair['id']?>">
            <textarea class="form-control" name="Pairs[<?=$i?>][qst_text]"
                placeholder="Текст вопроса или утверждения: A,B,C..."><?=$pair['qst_text']?></textarea>
            <textarea class="form-control" name="Pairs[<?=$i?>][ans_text]"
                placeholder="Текст ответа: 1,2,3..."><?=$pair['ans_text']?></textarea>
        </div>
        <input class="pairThemes" type="hidden" name="Pairs[<?=$i?>][themes]"
            value='<?=($active_themes[$pair->id])?json_encode($active_themes[$pair->id]):''?>'>
        <?= $this->render('/theme/_themes', [
            'model' => $all_themes,
            'hide' => true,
            'active' => $active_themes[$pair->id],
        ]); ?>
    </div>
<?php if ($str): ?>`<?php endif; ?>
<?php ++$i; endforeach; ?>
<?php endif; ?>
