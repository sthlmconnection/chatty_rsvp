<?php

/**
 * Guest
 * Class handling RSVP records for individual guests.
 */
class Guest {

  public $email;
  public $name;
  public $coming;
  public $friend;
  public $reference;
  public $message;

  static $config;
  static $g_service;

  private $messages = array();
  private $spreadsheet_item; // Used to cache this guest's spreadsheet record.

  /**
   * Class constructor.
   */
  function __construct($email = '', $name = '', $coming = 1, $friend = 0, $reference = '', $message = '') {
    $this->email = $email;
    $this->name = $name;
    $this->coming = $coming;
    $this->friend = $friend;
    $this->reference = $reference;
    $this->message = $message;
  }

  /**
   * Configure the class.
   *
   * @param $config
   *  - A configuration array, as defined in default.settings.php.
   */
  function config($config) {
    self::$config = $config;
  }

  /**
   * Connect to the spreadsheet.
   */
  function connectSpreadsheet() {
    // Connect if not already connected.
    if (empty(self::$g_service)) {
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

  /**
   * Load a guest record and populate the guest object.
   *
   * @param $email
   *  - The email address to look up the record by.
   */
  function load($email) {
    try {
      self::connectSpreadsheet();

      // Define a worksheet query.
      $query = new Zend_Gdata_Spreadsheets_ListQuery();
      $query->setSpreadsheetKey(self::$config['spreadsheet_id']);
      $query->setWorksheetId(self::$config['worksheet_id']);
      $query->setSpreadsheetQuery('email = "' . $email . '"');

      // Get the list feed for the query.
      $listFeed = self::$g_service->getListFeed($query);

      // Cache the spreadsheet entry and populate the object.
      if ($listFeed->entries[0]) {
        $this->spreadsheet_item = $listFeed->entries[0];
        $values = $this->spreadsheet_item->getCustom();
        $this->email = $values[0]->getText();
        $this->name = $values[1]->getText();
        $this->coming = $values[2]->getText();
        $this->friend = $values[3]->getText();
        $this->reference = $values[4]->getText();
        $this->message = $values[5]->getText();
        return $this->email ? $this : false;
      }
      return false;
    }
    catch (Exception $e) {
      $messages[] = 'ERROR: ' . $e->getMessage();
      return false;
    } 
  }

  /**
   * Insert a guest record into the spreadsheet.
   */
  function insert() {
    try {
      self::connectSpreadsheet();

      // Create the row content.
      $row = array(
        'email' => (string) $this->email, 
        'name' => (string) $this->name, 
        'coming' => (string) $this->coming,
        'friend' => (string) $this->friend,
        'reference' => (string) $this->reference,
        'message' => (string) $this->message,
      );

      // Insert the row.
      $entryResult = self::$g_service->insertRow($row, self::$config['spreadsheet_id'], self::$config['worksheet_id']);
      return $entryResult->id;
      
    }
    catch (Exception $e) {
      $messages[] = 'ERROR: ' . $e->getMessage();
      return false;
    }
  }

  /**
   * Update a guest record in the spreadsheet.
   */
  function update() {
    try {
      self::connectSpreadsheet();
      if (!$this->spreadsheet_item) {
        $this->load($this->email);
      }

      // Create the row content.
      $row = array(
        'email' => (string) $this->email, 
        'name' => (string) $this->name, 
        'coming' => (string) $this->coming,
        'friend' => (string) $this->friend,
        'reference' => (string) $this->reference,
        'message' => (string) $this->message,
      );

      // Update the row.
      return self::$g_service->updateRow($this->spreadsheet_item, $row);
    }
    catch (Exception $e) {
      $messages[] = 'ERROR: ' . $e->getMessage();
      return false;
    }
  }
}
