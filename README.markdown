>> Cimpfo by Cyb3r
>>
>> PHP5 Messenger bot for 37signals's Campfire
>> 
>> MIT License
>>
>> Version 0.1
>> 
>> Uses 37signals's Campfire API
>> Uses CURL librairy

I spent some time playing with the Campfire API but mainly I was looking to implement a messenger bot for Campfire. Currently, Cimpfo 0.1, has two parts:

* The Cimpfo Messenger Service
	* Submits messages to Campfire
* The Twitter Requester
	* Requests user updates from Twitter

I still have to tweak it so Twitter messages won't reappear once submitted. My goal is to add more publishers to the messenger service so I can 
have access to information where it might be blocked by firewall for example.

## Usage

I run it on a web server with a cron job running it 4 times per hour. The main file is **cimpfo.php**.

## Configuration

Currently there's 2 configuration files, one for the Campfire API service and one for the Twitter API service.

* **campfire.confs.inc.php**
* **twitter.confs.inc.php**

Fork it and give some feedback.

## User Agent Test

Make a request with a URL parameter UA=1. Does not publish to Campfire but ouputs results in the browser/user agent.

Example:

    http://cimpfo.domain.tld/cimpfo.php?UA=1

## Tested

Submit request/issues through the github Issues project section.

~::~ Cyb3r ~::~