<tr class="lvlRow" data-key="<?=$level['id']?>">
    <td class="lvl"><?=$level['id']?></td>
    <td><input type="text" class="form-control exp" value="<?=$level['exp']?>" <?=($level['id'] == 1)?'disabled':''?>></td>
    <td><input class="max" name="max" value="<?=$level['id']?>" type="radio" <?=($level['isMax'])?'checked':''?>></td>
</tr>