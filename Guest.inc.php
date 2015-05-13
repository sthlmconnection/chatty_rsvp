<?php

require_once 'vendor/autoload.php';

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\SpreadsheetService;

/**
 * Guest
 * Class handling RSVP records for individual guests.
 */
class Guest {

  static $config;
  static $spreadsheet;
  static $worksheet;

  /**
   * Configure the class.
   *
   * @param $config
   *  - A configuration array, as defined in default.settings.php.
   */
   public static function config($config) {
    self::$config = $config;
  }

  public $email;
  public $firstname;
  public $lastname;
  public $coming;
  public $friend;
  public $message;

  private $spreadsheet_entry; // Used to cache this guest's spreadsheet record.

  /**
   * Class constructor.
   */
  function __construct($email = '', $firstname = '', $lastname = '', $coming = 1, $friend = 0, $message = '') {
    $this->email = $email;
    $this->firstname = $firstname;
    $this->lastname = $lastname;
    $this->coming = $coming;
    $this->friend = $friend;
    $this->message = $message;
  }

  /**
   * Connect to the spreadsheet.
   */
  function connectSpreadsheet() {

    // Connect if not already connected.
    if (empty(self::$spreadsheet)) {

      $g_client_key = file_get_contents(self::$config['g_client_key_file']);

      $g_client = new Google_Client();
      $g_client->setApplicationName(self::$config['g_client_app_name']);
      $g_client->setClientId(self::$config['g_client_id']);
      $g_client->setAssertionCredentials(new Google_Auth_AssertionCredentials(
          self::$config['g_client_email'],
          array('https://spreadsheets.google.com/feeds', 'https://docs.google.com/feeds'),
          $g_client_key
      ));
      $g_client->getAuth()->refreshTokenWithAssertion();
      $token_obj = json_decode($g_client->getAccessToken());
      $token = $token_obj->access_token;

      $service_request = new DefaultServiceRequest($token);
      ServiceRequestFactory::setInstance($service_request);

      $spreadsheet_service = new SpreadsheetService();
      $spreadsheet_feed = $spreadsheet_service->getSpreadsheets();
      $spreadsheet = $spreadsheet_feed->getByTitle(self::$config['spreadsheet_name']);
      $worksheet_feed = $spreadsheet->getWorksheets();
      $worksheet = $worksheet_feed->getByTitle(self::$config['worksheet_name']);

      self::$spreadsheet = $spreadsheet;
      self::$worksheet = $worksheet;
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

      $list_feed = self::$worksheet->getListFeed(array("sq" => "email = \"$email\""));

      foreach ($list_feed->getEntries() as $entry) {
        // Cache the spreadsheet entry and populate the object.
        $this->spreadsheet_entry = $entry;
        $values = $entry->getValues();
        $this->email = $values['email'];
        $this->firstname = $values['firstname'];
        $this->lastname = $values['lastname'];
        $this->coming = $values['coming'];
        $this->friend = $values['friend'];
        $this->message = $values['message'];
        return $this->email ? $this : false;
      }
      return false;
    }
    catch (Exception $e) {
      error_log($this->errorMessage($e));
      return false;
    }
  }

  /**
   * Insert a guest record into the spreadsheet.
   */
  function insert() {
    try {
      self::connectSpreadsheet();

      $list_feed = self::$worksheet->getListFeed();

      // Create the row content.
      $row = array(
        'email' => (string) $this->email,
        'firstname' => (string) $this->firstname,
        'lastname' => (string) $this->lastname,
        'coming' => (string) $this->coming,
        'friend' => (string) $this->friend,
        'message' => (string) $this->message,
      );

      $list_feed->insert($row);
    }
    catch (Exception $e) {
      error_log($this->errorMessage($e));
      return false;
    }
  }

  /**
   * Update a guest record in the spreadsheet.
   */
  function update() {
    try {
      self::connectSpreadsheet();

      if (!$this->spreadsheet_entry) {
        $this->load($this->email);
      }

      // Create the row content.
      $row = array(
        'email' => (string) $this->email,
        'firstname' => (string) $this->firstname,
        'lastname' => (string) $this->lastname,
        'coming' => (string) $this->coming,
        'friend' => (string) $this->friend,
        'message' => (string) $this->message,
      );

      $this->spreadsheet_entry->update($row);
    }
    catch (Exception $e) {
      error_log($this->errorMessage($e));
      return false;
    }
  }

  function errorMessage($e) {
    return 'ERROR ' . $e->getCode() . ' in ' . $e->getFile() . ', line ' . $e->getLine() . ': ' . $e->getMessage();
  }
}
