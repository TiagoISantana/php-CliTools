# PHP library to help on console application or debug

- Get user input
- Debug code
- Progress bars
- Multicolor text and backgrounds
- Block divisor
- And more...


#### Requirements

- PHP > 5.6.*;


###### Running

```php
<?php

//Create cli CPU process name
$cli = new CliTools('My Process Name');

//Welcome user
$cli->clear()->drawLogo()->jumpLine(1)->writeLine('Welcome!')->jumpLine(2);

//Get user input
$user_input = $cli->getUserInput('What is your name?',$cli::FOREGROUND_WHITE,$cli::BACKGROUND_GREEN);

//Use requested variable on your code
$cli->jumpLine()->writeLine("Hello $user_input")->jumpLine(1);