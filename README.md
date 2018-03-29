# webpad
A url-based webpad based on LAMP stack

## What does it do?
Allows you to create notes on the fly through the URL and can be accessed by any device. The entire page is editable. It only supports plain text. Some future features considered.

## Prerequisites
* Assumes you have mod_rewrite enabled on Apache, this project is based on ```http://your-local-ip/webpad/``` as the base directory of application.

* Assumes you have a LAMP stack setup and can import the ```webpad.sql``` file.

For my paritcular case this webapp is hosted on a Raspberry Pi and the PHP code is using PDO.

## Demo

Current capability - 03/29/2018

![webpad basic CRUD capability first version](https://raw.githubusercontent.com/jdc-cunningham/webpad/master/webpad-basic-crud-demo.gif)

## Basic commands

These are all done through the url

## Future features

* Self timestamping on no-change based on webworker
* Ability to scroll up/down using # with floating menu on top right or maybe bound to up/down arrow keys

## Thoughts

Yeah the code(PHP) is pretty bad, need to look into using classes/OOP. I literally just wanted to make it work.

## Updates
