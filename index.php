<?php

$texts_file = file_exists('texts.json') ? 'texts.json' : 'default.texts.json';
$texts_json = trim(file_get_contents($texts_file));
$texts = json_decode($texts_json);
$css_file = file_exists('style.css') ? 'style.css' : 'default.style.css';

?>
<!doctype html>
<html>
  <head>
    <title><?php print $texts->title; ?></title>
    <meta charset="utf-8" />
    <style>@import url(<?php print $css_file; ?>);</style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script>jQuery("html").addClass("js");</script>
    <script>var texts = <?php print $texts_json; ?>;</script>
    <script src="guest.js"></script>
  </head>
  <body>
    <?php print $texts->intro; ?>
    <form id="rsvp" action="guest.php" method="post">
      <div id="step-1" class="step">
        <p class="message"><?php print $texts->step_1; ?></p>
        <div class="input">
          <input type="email" name="email" id="email" value="" />
        </input>
      </div>
      <div id="step-2" class="step">
        <p class="message"><?php print $texts->step_2->message_new; ?></p>
        <input type="text" name="name" id="name" value="" />
      </div>
      <div id="step-3" class="step">
        <p class="message"><?php print $texts->step_3->message_existing; ?></p>
        <div class="input">
          <input type="radio" name="coming" value="1" checked="checked"> <?php print $texts->step_3->option_yes; ?>
          <input type="radio" name="coming" value="0"> <?php print $texts->step_3->option_no; ?>
          <input type="radio" name="coming" value="0.5"> <?php print $texts->step_3->option_maybe; ?>
        </div>
      </div>
      <div id="step-4" class="step">
        <p class="message"><?php print $texts->step_4->message_default; ?></p>
        <div class="input">
          <input type="radio" name="friend" value="1"> <?php print $texts->step_4->option_yes; ?>
          <input type="radio" name="friend" value="0" checked="checked"> <?php print $texts->step_4->option_no; ?>
        </div>
      </div>
      <input type="submit" name="submit" id="submit" value="<?php print $texts->submit; ?>" />
      <div id="step-5" class="step">
        <p class="message"><?php print $texts->step_5->message_default; ?></p>
        <p class="message"><?php print $texts->bye; ?></p>
      </div>
    </form>
  </body>
<html>
