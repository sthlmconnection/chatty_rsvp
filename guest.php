<?php

/**
 * guest.php
 * JSON service for reading/writing REST records.
 */

require 'Guest.inc.php';
require 'custom/config.php';

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
  $input['reference'] = trim($_POST['reference']);
  $input['message'] = trim($_POST['message']);

  if (preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $input['email'])
      && !empty($input['name'])) {
    $guest = new Guest();
    if ($guest->load($input['email'])) {
      $ret = "existing";
      foreach ($input as $key => $val) {
        $guest->{$key} = $val;
      }
      $ret .= ' ' . $guest->update() ? 'updated' : 'failed';
    }
    else {
      $ret = "new";
      $guest->email = $input['email'];
      $guest->name = $input['name'];
      $guest->coming = $input['coming'];
      $guest->friend = $input['friend'];
      $guest->reference = $input['reference'];
      $guest->message = $input['message'];
      $ret .= ' ' . $guest->insert() ? 'inserted' : 'failed';
    }

  }
  else {
    $ret = 'validation failed';
  }

}

print $ret;
