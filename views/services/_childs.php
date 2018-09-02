<?php
if( $model->type == 'txtgroup' ){
  echo '
  <div style="padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; margin-bottom: 20px;"><table class="table table-bordered"><thead>
  <th width="25%">
  Название
  </th>
  <th width="25%" class="text-center">
  Формат
  </th>
  <th width="25%" class="text-center">
  Действие
  </th>
  <th width="25%" class="text-right">
  Удалить
  </th>
  </thead>';

  foreach( $model->childServices as $childModel ){
    echo '<tr>
    <td>
    '.$childModel->name.'
    </td>
    <td class="text-center">
    '.Services::getTypeTitle($childModel->type).'
    </td>
    <td class="text-center">
    '.$childModel->serviceLink().'
    </td>
    <td class="text-right">
    <a href="#" data-id="'. $childModel->id .'" onclick="deleteLink2(event, this)"><i class="glyphicon glyphicon-trash" title="'.App::t('Удалить').'"></i></a>
    </td>
    </tr>';
  }

  echo '</table></div>';
}
?>
