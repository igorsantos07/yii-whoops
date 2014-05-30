Yii Error Handler with Whoops
=============================

Integrates the Whoops library into Yii 1.1 and enables further error processing.

Current `CErrorHandler` behaviour is to use internal error views to display development problems,
such as the `error` and `exception` views. If you're not in debug mode, it will simply call the
vanilla error action and display less stuff in the screen so your users don't get ugly errors.

This new implementation allows you to - if needed - include a last, global error handler before
displaying error messages. The `errorAction` is called and, if it can't handle the issue,
we take the stage and decide what to do with the error - if you're debugging the application we
will give you a really, really nice error page that will help you finding what's wrong :)


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

5. There were some changes in the API for further error action handling. If you want to have custom
   error pages you can as usual include a `errorAction` property into the `errorHandler` above, but
   with the following differences:

   - `Yii::app()->errorHandler->error` now can be a `CEvent` in case of PHP errors or a normal `Exception`
     in case of, uh, exceptions. Have that in mind when handling errors in your action, as PHP Errors have
     no code and etc - however, if you're showing an error page is advised to use the standard 500 code.
   - If your action is unable to handle the error, Whoops will still get to the stage as usual. Example:
     all API errors in your app will show a small message and redirect the user, while other errors are
     real problems and should be handled by the framework's error handler.
     To tell `WhoopsErrorHandler` you've taken care of the issue, call `Yii::app()->errorHandler->handled()`,
     and then Whoops will not interfere with what the action has done; if after the action he still thinks
     he should do something, the Whoops error page will be called as usual.

Sample screenshot
-----------------
<a href="http://i.imgur.com/pqt8fK4.png" alt="Sample screenshot">
    <img src="http://i.imgur.com/pqt8fK4.png" width="650" />
</a>

[Composer]:http://getcomposer.org/
[downloading]:https://github.com/igorsantos07/yii-whoops/archive/master.zip
[ydtb]:http://github.com/malyshev/yii-debug-toolbar
