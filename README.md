Yii Error Handler with Whoops
=============================

Integrates the Whoops library into Yii 1.1.

Instead of depending on an action to display error stuff, this error handler calls directly
Whoops methods to handle the problems for you. They automatically outputs the error page, so
you don't need to have an action only for that.

However, I do recomend you to set the default errorHandler in production servers, since you
probably don't want to show users *useful debug information*, right? ;)

Usage
-----

1. First, install it via Composer (`composer require igorsantos07/yii-whoops:1`) or download
   and unpack it in your `extensions` folder.

2. If you're using Composer, I strongly recomend you to create a `vendor` alias if you haven't yet.
   Add this to the beginning of your `config/main.php`:

    ```php
    Yii::setPathOfAlias('vendor', __DIR__.'/../../vendor');
    ```

3. Replace your `errorHandler` entry at `config/main.php` with the error handler class. Example:

    ```php
    'errorHandler' => ['class' => 'vendor.igorsantos07.yii-whoops.WhoopsErrorHandler']
    ```