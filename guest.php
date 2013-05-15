<?php

/**
 * guest.php
 * RESTful service for reading/writing guest records.
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
  $input['firstname'] = trim($_POST['firstname']);
  $input['lastname'] = trim($_POST['lastname']);
  $input['email'] = trim($_POST['email']);
  $input['coming'] = (float) trim($_POST['coming']);
  $input['friend'] = (int) trim($_POST['friend']);
  $input['message'] = trim($_POST['message']);

  if (preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $input['email'])
      && !empty($input['firstname'])) {
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
      $guest->firstname = $input['firstname'];
      $guest->lastname = $input['lastname'];
      $guest->coming = $input['coming'];
      $guest->friend = $input['friend'];
      $guest->message = $input['message'];
      $ret .= ' ' . $guest->insert() ? 'inserted' : 'failed';
    }

  }
  else {
    $ret = 'validation failed';
  }

}

print $ret;
