<!doctype html>
<html>
  <head>
    <title><?php print $texts->title; ?></title>
    <meta charset="utf-8" />
    <style>@import url(<?php print $css_file; ?>);</style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script>jQuery("html").addClass("js");</script>
    <script>var texts = <?php print $texts_json; ?>;</script>
    <script src="chatty_rsvp.js"></script>
  </head>
  <body>
    <div class="page">
      <div class="intro">
        <?php print $texts->intro; ?>
      </div>
      <form id="rsvp" action="guest.php" method="post" autocomplete="off">
        <div id="step-1" class="step">
          <p class="message"><?php print $texts->step_1; ?></p>
          <div class="input">
            <input type="email" name="email" id="email" value="" />
          </div>
        </div>
        <div id="step-2" class="step">
          <p class="message"><?php print $texts->step_2->message_new; ?></p>
          <div class="input">
            <input type="text" name="nameinput" id="nameinput" value="" />
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="lastname" id="lastname" value="" />
          </div>
        </div>
        <div id="step-3" class="step">
          <p class="message"><?php print $texts->step_3->message_existing; ?></p>
          <div class="input">
            <label for="radio-1"><input id="radio-1" type="radio" name="coming" value="1" checked="checked"> <?php print $texts->step_3->option_yes; ?></label>
            <label for="radio-2"><input id="radio-2" type="radio" name="coming" value="0"> <?php print $texts->step_3->option_no; ?></label>
            <label for="radio-3"><input id="radio-3" type="radio" name="coming" value="0.5"> <?php print $texts->step_3->option_maybe; ?></label>
          </div>
        </div>
        <div id="step-4" class="step">
          <p class="message"><?php print $texts->step_4->message_default; ?></p>
          <div class="input">
            <label for="radio-2-1"><input id="radio-2-1" type="radio" name="friend" value="1"> <?php print $texts->step_4->option_yes; ?></label>
            <label for="radio-2-2"><input id="radio-2-2" type="radio" name="friend" value="0" checked="checked"> <?php print $texts->step_4->option_no; ?></label>
          </div>
        </div>
        <div id="step-5" class="step">
          <p class="message"><?php print $texts->step_5->message; ?></p>
          <div class="input">
            <input type="text" name="message" id="message" value="">
          </div>
        </div>
        <div class="submit-wrapper"> <input type="submit" name="submit" id="submit" value="<?php print $texts->submit; ?>" /></div>
        <div id="step-6" class="step">
          <p class="message"><?php print $texts->step_6->message_default; ?></p>
          <p class="message"><?php print $texts->bye; ?></p>
        </div>
      </form>
    </div>
  </body>
<html>
