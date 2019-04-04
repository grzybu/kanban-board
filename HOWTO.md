#Kanbanboard app HOWTO file #

Demo version is available at:
[https://bgrzyb-kanbanboard.herokuapp.com/](https://bgrzyb-kanbanboard.herokuapp.com/)

## Pre-requirements:
* PHP 7.2
* composer
* php-xdebug extension if you want run phpunit tests and test coverage

## Installation

### 1. Dependencies installation 

Application requires external libraries, you can install them using composer. Run command in root directory

``
composer install
``

### 2. Configuration

There are to methods to configure application - either use `.env` file or define them in your server configuration.

If you want to run it in your local development environment, I strongly recommend to use `.env` file.
The application will automatically pick-up variables stored in file and use them.

There is `.env.dist` file provided in root directory. This can be a good start. To create your local `.env` file simply 
create a copy of `.env.dist` file and name it `.env`:

``
cp .env.dist .env
``

For testing purposes you can use the configuration provided in ``.env.dist`` file.

***2.1 Custom configuration***
Then open newly create `.env` file in your favourite text editor.

The file contains fallowing variables:

``GH_CLIENT_ID=``   \
``GH_CLIENT_SECRET=``\
``GH_ACCOUNT=``\
``GH_REPOSITORIES=`` 

First of them are ``GH_CLIENT_ID`` and ``GH_CLIENT_SECRET``

These are required to authenticate to GitHub API. You can create a new application on
[Developer setting page](https://github.com/settings/developers) in your [GitHub Account](https://github.com/settings/profile)

When creating your Application you should provide also ``Homepage URL`` and ``Authorization callback URL``
Provide full url on which your application runs in both, e.g. ``https://myboard-app.dev/`` or ``http://localhost:9000``

After you create a new application you will get ``Client ID`` and ``Client Secret``. Put them correspondingly 

``GH_ACCOUNT`` is your GitHub account name we want to use in our application to display data.

``GH_REPOSITORIES`` - this variable stores GitHub repositories names list, separated using ``|``
char. Simply put any repositories names you want to check, e.g.
``
GH_REPOSITORIES=repo_name_1|repo_name_2
``


### 3. Running local development version

After you fallowed step 2, you can start using application. The simplest method is to use PHP built-in web server.
Simple in your root directory run following command:

```bash
php -S localhost:9000 -t public/
```

Or use provided ``Composer`` script, witch starts locals server using localhost host and port 9000.

```bash
composer serve
```

Then open your favourite web-browser and go to address, e.g ``http://localhost:9000`` or any other, if you decided e.g. to listen on a different port.


### 4. Running up on HTTP server

You can run application on http server. Configuration may vary depending if you're running e.g. Apache or nginx.
In both cases you have to point your document_root to `public` folder and redirect all requests to ``index.php`` file.

If you want to run application on Heroku, there is ``Procfile`` and corresponding ``nginx_app.conf`` file.

### 5. Running code tests

Application comes with PHPUnit tests, to run test you need ``Xdebug`` extension. To run unit test, run following commands:

#### 5.1 Unit test
```bash
composer test
```

#### 5.2 Unit tests with code coverage
```bash
composer test-coverage
```
The output will be written in ``test-coverage/coverage`` folder. To view coverage report open ``index.html`` file.

#### 5.3 Code quality tests
```bash
composer check
```
It will run ``PHP CodeSniffer`` and ``PHP Mess Detector`` tests.





 



