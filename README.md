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

1. Install it:
    - Using [Composer] (it will automatically install Whoops main libraries as well):
    ```shell
    composer require igorsantos07/yii-whoops:1
    composer install
    ```
    - Or [downloading] and unpacking it in your `extensions` folder.

2. If you're using Composer, I strongly recomend you to create a `vendor` alias if you haven't yet.
   Add this to the beginning of your `config/main.php`:

    ```php
    Yii::setPathOfAlias('vendor', __DIR__.'/../../vendor');
    ```

3. Replace your `errorHandler` entry at `config/main.php` with the error handler class. Example:

    ```php
    'errorHandler' => ['class' => 'vendor.igorsantos07.yii-whoops.WhoopsErrorHandler']
    ```

4. If you're using some custom LogRoute that binds to the application's end, you can disable it using
   the component's `disabledLogRoutes` property. Just set it to an array containing all the classnames
   (not aliases!) of each route you want disabled whenever Whoops is launched. By default it disables
   the famous (Yii Debug Toolbar)[ydtb]; if you want to keep it enabled, override the
   `defaultDisabledLogRoutes` property.

   ```php
   'errorHandler' => [
       'class'             => 'vendor.igorsantos07.yii-whoops.WhoopsErrorHandler',
       'disabledLogRoutes' => 'MyCustomRouteClass'
   ]
   ```

Sample screenshot
-----------------
<a href="http://i.imgur.com/pqt8fK4.png" alt="Sample screenshot">
    <img src="http://i.imgur.com/pqt8fK4.png" width="650" />
</a>

[Composer]:http://getcomposer.org/
[downloading]:https://github.com/igorsantos07/yii-whoops/archive/master.zip
[ydtb]:http://github.com/malyshev/yii-debug-toolbar
