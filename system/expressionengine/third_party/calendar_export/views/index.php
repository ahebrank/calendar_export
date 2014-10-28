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
    <div>
      <input name="submit" class="submit" type="submit" value="Preview"> 
      <input name="submit" class="submit" type="submit" value="Export">
    </div>
  <?=form_close()?>

  <?php if (isset($events)): ?>
  <div>
    <table class="events">
      <thead>
        <tr>
          <th>Title</th>
          <th>Categories</th>
          <th>Start Date</th>
          <th>End Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($events as $id=>$e): ?>
        <tr>
          <td><?=$e['title']?></td>
          <td><?=$e['categories']?></td>
          <td><?=date("Y-m-d", strtotime($e['start_date']))?></td>
          <td><?=date("Y-m-d", strtotime($e['end_date']))?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>