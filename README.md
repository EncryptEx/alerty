<p align="center"><a href="#"><img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fcdn.dribbble.com%2Fusers%2F2344150%2Fscreenshots%2F4814121%2F0711_notification_bell_dribble.gif&f=1&nofb=1" alt="Alerty" height="60"/></a></p>
<h1 align="center">Alerty</h1>
<p align="center">Fast and reliable reply notifications after tasks.</p>
<p align="center">
<img src="https://img.shields.io/github/languages/code-size/EncryptEx/alerty"/>
<img src="https://img.shields.io/github/languages/top/EncryptEx/alerty"/>
<img src="https://tokei.ekzhang.com/b1/github/EncryptEx/alerty"/>
<img src="https://img.shields.io/github/last-commit/EncryptEx/alerty"/>
<!-- <a href="https://github.com/EncryptEx/alerty/actions/workflows/main.yml"><img src="https://github.com/EncryptEx/alerty/actions/workflows/main.yml/badge.svg"></img></a> -->

A minimalistic platform which triggerers an action (emails by default) when a specific url is requested with an optional data sent. Useful when doing crontab/cronjob monitoring. 

By default, it sends an email to the previously saved email.

---

## Architecture
This project is using
- PHP
- MySQL (pdo conections)
- Composer, to use:
    - phpdotenv (credentials management)
    - phpmailer (SMTP email library)
    - phpunit (unit testing in php)

---
## Story

I was messing arround with my linux, until I discovered that I wanted to recieve some feedback whenever a crontab (the update & upgrade) was finished. 

That's basically the main reason of why I have chosen to build this triggerer website. 

---

## Philosophy
I would like to bring this little service open and free to anyone, so feel free to fork it or use it at [my hosted version](https://alerty.jaumelopez.dev)

---
## Installation
1. First, clone the repository and install all the dependencies:
    ```sh 
    composer update
    ```
    Depending of your hosting provider you will need to move the ``vendor`` folder inside the ``private`` folder because of permission conflicts with the user www-data (happened in my case). If this happens to you, you'll need to change the path of the vendor's autoload at the ``src/private/utils.php`` file (line 15).

2. Then, with the PHP installed and its dependencies, let's import the databse structure, for this, create a database and drag and drop (in phpMyAdmin) the file located in:

    **db/database.sql**

    or simply go to the import section after clicking on the database's name. A tutorial can be found [here](https://www.inmotionhosting.com/support/server/databases/import-database-using-phpmyadmin/) or if you prefer the in-line method [this](https://stackoverflow.com/questions/7828060/how-do-i-import-a-sql-data-file-into-sql-server) may help you.

3. Create the .env file by changing the name or copying the **src/private/.env.example** file
    To change the name:
    ```
    mv .env.template .env
    ```
    To copy the file
    ```
    cp .env.template .env
    ```
    And then, place all the credentials needed. Here's a table explaining what do they mean:
    | Enviroment variable  | Description                                        |
    |----------------------|----------------------------------------------------|
    | DB_HOST              | The databse host, most times is localhost          |
    | DB_NAME              | The name of the Database you have just created     |
    | DB_USER              | The MySQL database username, needs access to CRUD (Create, Read, Update, Delete) |
    | DB_PASS              | The MySQL database password                        |
    | MAIL_SENDER          | The email, example: name@example.com               |
    | MAIL_PWD             | The email account password, highly recommended to generate one with [this method](https://support.google.com/accounts/answer/185833?hl=en) if is a google account             |
    | MAIL_HOST            | The SMTP host, gmail uses smtp.gmail.com           |
    | MAIL_PORT            | The SMTP port, gmail uses 587 (with TLS, that is required in this application)  |
    | HASH_SALT            | A secret string that is crucial to encrypt the account verification link. Do not leave in blank or make it easy to guess, you'll never have to type it anywhere. |
    
4. Run server and enjoy!

---
### Suggestions or questions
If you feel that something is wrong in this README file or you need help while setting up this project, feel free to contact or open a [GitHub Issue](https://github.com/EncryptEx/alerty/issues/new).

---

<p align="center"><a href="https://github.com/EncryptEx/hammer/"><img src="http://randojs.com/images/barsSmallTransparentBackground.gif" alt="Animated footer bars" width="100%"/></a></p>
