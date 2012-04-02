<?php

class Guest {

  static $config;
  static $g_service;

  public $email;
  public $name;
  public $coming;
  public $friend;

  private $messages = array();
  private $spreadsheet_item; // Used for cache this guest's spreadsheet record.

  function config($config) {
    self::$config = $config;
  }

  function __construct($email = '', $name = '', $coming = 1, $friend = 0) {
    $this->email = $email;
    $this->name = $name;
    $this->coming = $coming;
    $this->friend = $friend;
  }

  function connectSpreadsheet() {
    if (empty($g_service)) {
      // load Zend Gdata libraries
      require_once 'Zend/Loader.php';
      Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
      Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

      // connect to API
      $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
      $client = Zend_Gdata_ClientLogin::getHttpClient(self::$config['user'], self::$config['pass'], $service);
      $service = new Zend_Gdata_Spreadsheets($client);

      self::$g_service = $service;
    } 
  }

  function load($email) {
    try {
      self::connectSpreadsheet();

      // define worksheet query
      // get list feed for query
      $query = new Zend_Gdata_Spreadsheets_ListQuery();
      $query->setSpreadsheetKey(self::$config['spreadsheet_id']);
      $query->setWorksheetId(self::$config['worksheet_id']);
      $query->setSpreadsheetQuery('email = "' . $email . '"');
      $listFeed = self::$g_service->getListFeed($query);

      if ($listFeed->entries[0]) {
        $this->spreadsheet_item = $listFeed->entries[0];
        $values = $this->spreadsheet_item->getCustom();
        $this->email = $values[0]->getText();
        $this->name = $values[1]->getText();
        $this->coming = $values[2]->getText();
        $this->friend = $values[3]->getText();
        return $this->email ? $this : false;
      }
      return false;
    }
    catch (Exception $e) {
      $messages[] = 'ERROR: ' . $e->getMessage();
      return false;
    } 
  }

  function insert() {
    try {
      self::connectSpreadsheet();

      // create row content
      $row = array(
        'email' => (string) $this->email, 
        'name' => (string) $this->name, 
        'coming' => (string) $this->coming,
        'friend' => (string) $this->friend,
      );

      // insert new row
      $entryResult = self::$g_service->insertRow($row, self::$config['spreadsheet_id'], self::$config['worksheet_id']);
      return $entryResult->id;
      
    }
    catch (Exception $e) {
      $messages[] = 'ERROR: ' . $e->getMessage();
      return false;
    }
  }

  function update() {
    try {
      self::connectSpreadsheet();
      if (!$this->spreadsheet_item) {
        $this->load($this->email);
      }

      // create row content
      $row = array(
        'email' => (string) $this->email, 
        'name' => (string) $this->name, 
        'coming' => (string) $this->coming,
        'friend' => (string) $this->friend,
      );

      return self::$g_service->updateRow($this->spreadsheet_item, $row);
    }
    catch (Exception $e) {
      $messages[] = 'ERROR: ' . $e->getMessage();
      return false;
    }
  }
}
