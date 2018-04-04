# webpad
A url-based webpad based on LAMP stack

## What does it do?
Allows you to create notes on the fly through the URL and can be accessed by any device. The entire page is editable. It only supports plain text. Some future features considered.

## Prerequisites
* Assumes you have mod_rewrite enabled on Apache, this project is based on ```http://your-local-ip/webpad/``` as the base directory of application.

* Assumes you have a LAMP stack setup and can import the ```webpad.sql``` file.

For my particular case this webapp is hosted on a Raspberry Pi and the PHP code is using PDO.

## Demo

Current capability - 03/29/2018, 4.5hrs to build mostly got stuck on mod_rewrite particularly the RewriteBase directory being wrong.

![webpad basic CRUD capability first version](https://raw.githubusercontent.com/jdc-cunningham/webpad/master/webpad-basic-crud-demo-smaller.gif)

## Basic commands

These are all done through the url

* Create: use /webpad/save/padname to create a new pad called padname
* Read: use /webpad/padname to read a pad
* Update: if you're using a pad, your changes are saved automatically
* Delete: use /webpad/delete/padname to delete a pad
* View all pads: use /webpad/view to display all pads in alphabetical order

The whole page is editable, simply click and start writing.';

## Note

There is a .htaccess file in the repo, I can't see it I guess it's hidden or it's not here, I just tried to re-upload it in case.

The content is simple though:
```
<IfModule mod_rewrite.c>

    RewriteEngine On
    RewriteBase /webpad/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . index.php [L]

</IfModule>
```
The .htacccess goes in the base directory eg. /webpad/

## Future features

* Self timestamping on no-change based on webworker
* Ability to scroll up/down using # with floating menu on top right or maybe bound to up/down arrow keys

## Thoughts

Yeah the code(PHP) is pretty bad, need to look into using classes/OOP. I literally just wanted to make it work.

## Updates
