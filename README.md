# Chatty RSVP page

This is a small PHP and JavaScript application that provides a nifty little RSVP page with a chat-style interface. The idea is to make the guests feel welcome and give a personal touch to an otherwise rather boring process.

## Features

* GoogleDocs integration – saves RSVPs to a spreadsheet.
* Can deal with and update existing guest records as well as adding new guests.
* Friendly, interactive, chat-like interface.
* Intelligently asks the right questions and talks to the user based on existing information.

## Unfeatures

* Does not send emails.
* Does not limit the guest list to invited guests (existing email addresses).
* Is not very nice without JavaScript.
* Is not properly browser tested.

(Four features and four unfeatures makes perfect balance.)

## Requirements

* PHP (probably 5.2 minimum).
* A Gmail/Google Apps account.
* [Zend Framework](http://framework.zend.com/) – specifically the GData extension.

## Getting started

* Download the project files.
* Download [Zend Framework](http://framework.zend.com/download/current/). The Minimal package is sufficient.
* Create a GoogleDocs spreadsheet with the following columns (important):
  * Email
  * Name
  * Coming
  * Friend
* Set up a new Gmail/Google Apps account with the least permissions possible while still being able to edit the spreadsheet.
* Make a copy of default.settings.php and call it settings.php. Fill in the required options.

## Customization

* Make a copy of default.style.css and call it style.css. Go crazy.
* Make a copy of default.texts.json called texts.json and add your personal language to it.
