<?
  require("../extensions/global.php");
  $start_time = microtime(true);
  
  //delete our dev db.
  db()->execute("TRUNCATE TABLE job_clock");
  
  $rs = db()->query("SELECT * FROM jobs");
  while ($row = $rs->fetch_assoc())
  {
    $log = new JobClockEntry();
    $log->set('job_id', $row['id']);
    $log->set('bot_id', $row['bot_id']);
    $log->set('queue_id', $row['queue_id']);
    
    if ($row['status'] == 'working' || $row['status'] == 'qa')
    {
      $log->set('start_date', $row['downloaded_time']);
      $log->set('end_date', $row['finished_time']);
      $log->set('status', 'working');
      $log->save();
    }
    else if ($row['status'] == 'complete' || $row['status'] == 'failure')
    {
      $log->set('start_date', $row['downloaded_time']);
      $log->set('end_date', $row['finished_time']);
      $log->set('status', $row['status']);
      if ($row['status'] == 'failure')
        $log->set('status', 'error');
      $log->save();
    }
  }
  
  //finished!!!!
  echo "\nPopulated job clock log in " . round((microtime(true) - $start_time), 2) . " seconds.\n";
?>