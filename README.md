# Chatty RSVP page

This is a small PHP and JavaScript application that provides event organizers
with a nifty little RSVP page with a chat-style interface. The idea is to make
the guests feel welcome and give a personal touch to an otherwise rather boring
process.

## Features

* GoogleDocs integration – saves RSVPs to a spreadsheet.
* Can deal with and update existing guest records as well as adding new guests.
* Has a friendly, interactive, chat-like interface.
* Intelligently asks the right questions and talks to the user based on existing
information.

## Unfeatures

* Does not send emails.
* Does not limit the guest list to invited guests (existing email addresses).

## Requirements

* PHP (probably 5.2 minimum).
* A Google Spreadsheet.
* A Google Developer Project.
* [php-google-spreadsheet-client](https://github.com/asimlqt/php-google-spreadsheet-client)
  and [google-api-php-client](https://github.com/google/google-api-php-client).

## Getting started

* Download the project files.
* Install [Composer](https://getcomposer.org/).
* Install the dependencies with `composer install`.
* Create a GoogleDocs spreadsheet. Choose a good, unique name for the
  spreadsheet, and don't change it! Then add the following columns:
  * Email
  * FirstName
  * LastName
  * Coming
  * Friend
  * Message
* Create a Google Developer Project in the
  [Developers Console](https://console.developers.google.com/).
  * In the Credentials section, create a Client ID with the Service Account
    type. Choose the P12 key type.
  * Download the P12 key file. Keep it secret. Keep it safe. In a location where
    your PHP app can read it.
* Copy the Email address of your Google Project's Client ID and share the
  spreadsheet with this email address (give it read/write access).
* Create a subdirectory called `custom`.
* Make a copy of `default/config.php` in the `custom` directory. Fill in the
  required options.

## Customization

* Make a copy of `default/style.css` at `custom/style.css`. Go crazy.
* Make a copy of `default/texts.json` in the `custom` directory and add your
  personal language to it.
* Make a copy of `default/template.php` in the `custom` directory if you want to
  tweak the markup.
