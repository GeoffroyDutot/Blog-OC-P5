# Blog-OC-P5
## Author
[Geoffroy Dutot](https://geoffroydutot.fr)  - 2020 

[contact@geoffroydutot.fr](mailto:contact@geoffroydutot.fr)
## Badge  
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/7559db47910840918df497861da87831)](https://www.codacy.com/gh/GeoffroyDutot/Blog-OC-P5/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=GeoffroyDutot/Blog-OC-P5&amp;utm_campaign=Badge_Grade)
## Introduction

This project is the 5th project of the [Developer PHP / Symfony](https://openclassrooms.com/fr/paths/59-developpeur-dapplication-php-symfony) formation of [Openclassrooms](https://openclassrooms.com/).  

The goal of this project is to create a professional blog with the [PHP](https://www.php.net/manual/en/intro-whatis.php) language without any frameworks.  

It can integrate front themes, twig, and some libraries. 

He sould present a front-office and a back-office. The front-office accessible by all should have a home with informations about the author of the blog and a contact form. It will have a page listing all posts of the blog, a page with the detail of the post and a register - login page.  

Registered users could submit comments on posts. It will have to be accept or not by an administrator.  

Administrators will have access to the admin dashboard, in the dashboard theme there will have a global view on lasts actions on the site, and pages to manage posts, users, comments. They could deactivate users if they want.

In addition to the blog, the project will have UML diagrams and an analysis by a code validator. 

## Build with 

- [Startbootstrap Clean Blog](https://github.com/startbootstrap/startbootstrap-clean-blog)
- [AdminLTE-3.0.5](https://adminlte.io/themes/v3/)
- Twig
- Jquery
- Bootstrap
## Requirements 

- PHP 7.4
- COMPOSER
- MYSQL
- Web Server

## Installation

- Clone / Download the project
- Config your webserver to point on the project directory
- Composer install in src directory
- Unzip and Import Database with blog-oc-p5.sql.zip file
- Rename config/config.php.sample in config/config.php and add your database infos

## Demo Datas 
The database contains already some data so you can test the blog.
> User registered : 
>
> Email : jeanne.dupont@gmail.com
> Password : QahRj2VZk8sE  

> User Administrator : 
>
> Email : dev.blog@gmail.com
> Password : Y271ft2pPTEj  

There is those users so you can see different views of the blog. If you want to create a new admin register you and modify in the database the role property ROLE_USER to ROLE_ADMIN of your new user and deletes demo users to secure.