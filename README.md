# How to use

1. ### Activate the module

   Once the modules installed, make sure to edit the **modules.php** & uncomment this line of code under **/web/app/theme/{name-of-the-theme}/app**

   _modules.php_
   ```sh
   require_once get_template_directory() . '/modules/wp-module-people/people.php';
   ```

2. ### Compiling assets

   To compile the people script, you just need to run yarn & yarn start or yarn build under **/web/app/theme/{name-of-the-theme}**
