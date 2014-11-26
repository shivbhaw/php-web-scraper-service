PHP Web Scraper Automation Service (+features)
================================
An service that can be added onto any system to gather data from the web
and insert it into databases,updating it on a scheduled basis. Very adaptable,
if your database changes you simply update the database layout and your scripts.

Lots of added functionality.


Before you try and add this to your db, make sure to back up your DB. It backs up the relevent tables but better safe than sorry.

There are alot of functionality presented in this application, and they are as follows:-
================================
Ignore List (prevent a table entry from entering your database)

Varify List (prevent entire datasets from entering your database (preventing doubles))

Simple Script and URL Editor (for adding new sources and matching them up against appropriate scripts, with an in-browser script editor to make preg_match in php quicker)

Although it says Scheduled and Automated, it is designed in a way that it can
be used along side cron jobs and by inputting arguments it will run
the appropriate version, day, week, month, year.

ETC
===
This application works by run each script against the URL.
The gathered data is stored to the end when it is inserted into the database.

