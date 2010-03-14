>> CimpfoBot by Serafim Junior Dos Santos Fagundes Cyb3r Network
>>
>> PHP5 Messenger Bot for 37signals's Campfire Chatroom Web Application
>> 
>> MIT License
>>
>> Version 0.1.2
>> 
>> Uses Cimpfony, a Library for the API Service of 37signals's Campfire Chatroom Web Application
>> Uses CURL librairy
>>
>> Copyright (c) 2009-2010 Serafim Junior Dos Santos Fagundes

I spent some time playing with the Campfire API but mainly I was looking to implement a messenger bot for Campfire. Currently, CimpfoBot 0.1.2, is three constituances:

* The Cimpfony Library
	* An API Wrapper in PHP for 37Signals's Campfire Chatroom Web Application
* The CimpfoBot Messenger Service
	* Submits messages to Campfire
* The Twitter Requester
	* Requests user updates from Twitter

My goal is to add more publishers to the messenger service so I can have access to information where it might be blocked by firewall, but the main goal of this bot is to test the development of Cimpfo, Campfire's API Service Library.

## Usage

I run it on a web server with a cron job running it 4 times per hour. The main file is **cimpfobot.php**.

## Configuration

Currently there's 2 configuration files, one for the Campfire API service and one for the Twitter API service.

* **campfire.confs.inc.php**
* **twitter.confs.inc.php**

## User Agent Test

Make a request with a request URI /cimpfobot.php?UA=1. Does not publish to Campfire but ouputs results in the browser/user agent.

Example:

    http://cimpfo.domain.tld/cimpfobot.php?UA=1

