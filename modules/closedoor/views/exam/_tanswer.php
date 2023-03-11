<?php

/* Приём параметров
 * ----------------------
 * $i     - инкремент для вопросов
 * $j     - инкремент для ответов
 * $model - модель
 * $corr  - правильные ответы
 * $str   - если нужно вернуть строкой
 */
?>

<?php if (!empty($model)): ?>
<?php foreach ($model as $ans): ?>
<?php if ($str): ?>`<?php endif; ?>
<div id="answer_<?=$j?>" class="answer form-group" number="<?=$j?>">
    <span class="delete">Х</span>
    <!-- ID -->
    <input type="hidden" name="Test[questions][<?=$i?>][answers][<?=$j?>][id]"
    value="<?=$ans->id?>">
    <!-- TEXT -->
    <label class="control-label" for="qst_<?=$i?>-ans_<?=$j?>-text">Ответ-<?=$j?></label>
    <textarea id="qst_<?=$i?>-ans_<?=$j?>-text" class="form-control"
        name="Test[questions][<?=$i?>][answers][<?=$j?>][text]"><?=strip_tags($ans->text)?></textarea>
    <!-- CORRECT -->
    <div class="checkbox-inline">
        <label><input type="checkbox" name="Test[questions][<?=$i?>][answers][<?=$j?>][correct]" value='1'
        <?php if($corr && $ans->id > 0 && in_array($ans->id, $corr)){echo 'checked';}?>>Правильный</label>
    </div>
</div>
<?php if ($str): ?>`<?php endif; ?>
<?php ++$j; endforeach; ?>
<?php endif; ?>
