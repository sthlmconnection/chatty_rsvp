<?php
require 'Guest.inc.php';
require 'settings.php';

Guest::config($conf);

$ret = '';

if (!empty($_GET['email'])) {
  $guest = new Guest();
  $guest->load($_GET['email']);
  $ret = json_encode($guest);
}
elseif (!empty($_POST['email'])) {
  $input = array();
  $input['name'] = trim($_POST['name']);
  $input['email'] = trim($_POST['email']);
  $input['coming'] = (float) trim($_POST['coming']);
  $input['friend'] = (int) trim($_POST['friend']);

  if (preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $input['email'])
      && !empty($input['name'])) {
    $guest = new Guest();
    $guest->load($_POST['email']);
    if ($guest->load($_POST['email'])) {
      $ret = "existing";
      foreach ($input as $key => $val) {
        $guest->{$key} = $val;
      }
      $ret .= ' ' . $guest->update() ? 'updated' : 'failed';
    }
    else {
      $ret = "new";
      $guest = new Guest($input['email'], $input['name'], $input['coming'], $input['friend']);
      $ret .= ' ' . $guest->insert() ? 'inserted' : 'failed';
    }

  }
  else {
    $ret = 'validation failed';
  }

}

print $ret;
