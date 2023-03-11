<? foreach ((array)$model as $theme) : ?>
<div id="th-<?=$theme['id']?>" class="item <?=($active && in_array($theme['id'], $active))?'active':''?>" number="<?=$theme['id']?>"><?=
    $theme['name'].'/'.$theme['id']
?></div>
<? endforeach; ?>