<section id="calendar_export">
  <?=form_open($action_url, '', false)?>
    <?php echo $message; ?>
    <fieldset>
      <legend>Date Range</legend>
      <span class="date">
        <?php 
          $date_from  = isset($filter['from_date']) ? $filter['from_date'] : '';
          $date_to  = isset($filter['to_date']) ? $filter['to_date'] : '';
        ?>
        <input name="filter[from_date]" type="text" class="datepicker" value="<?=$date_from?>"> - <input name="filter[to_date]" type="text" class="datepicker" value="<?=$date_to?>">
      </span>
    </fieldset>
    <fieldset>
      <legend>Fields to show</legend>
      <ul class="no-decoration flex">
      <?php
        foreach ($fields as $id => $v): ?>
          <li>
            <input name="fields[<?=$id?>]" type="checkbox" id="field_<?=$id?>" <?php if (isset($fields_selected) && isset($fields_selected[$id])): ?>checked<?php endif; ?> value="1">
            <label for="field_<?=$id?>"><?=$v['label']?></label>
          </li>
      <?php
        endforeach; ?>
      </ul>
    </fieldset>
    <div>
      <input name="submit" class="submit" type="submit" value="Preview"> 
      <input name="submit" class="submit" type="submit" value="Export">
    </div>
  <?=form_close()?>

  <?php if (isset($events)): ?>
  <?php if (empty($events)): ?>
  <p>No events found!</p>
  <?php else: ?>
  <div>
    <table class="events">
      <thead>
        <tr>
          <th>Title</th>
          <th>Categories</th>
          <th>Start Date</th>
          <th>Start Time</th>
          <th>End Date</th>
          <th>End Time</th>
          <?php foreach ($events['field_lookup'] as $id => $v): ?>
            <th><?=$v['label']?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($events['entries'] as $id=>$e): ?>
        <tr>
          <td><?=$e['title']?></td>
          <td><?=$e['categories']?></td>
          <td><?=date("Y-m-d", strtotime($e['start_date']))?></td>
          <td><?=$e['start_time']?></td>
          <td><?=date("Y-m-d", strtotime($e['end_date']))?></td>
          <td><?=$e['end_time']?></td>
          <?php foreach ($events['field_lookup'] as $id => $v): ?>
            <td><?=$e['data'][$id]?></th>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</section>