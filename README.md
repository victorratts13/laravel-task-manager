<p align="center">
<a href="#" target="_blank" style="display: flex; justify-content: center;">
<img src="public/app.png" width="400" alt="Laravel Logo" style="width: 80px;">
</a>
</p>

<p align="center">
<a href="#" style="font-size: 30px;">Laravel Task-Manager</a>
</p>

<p align="center">
<img src="https://img.shields.io/badge/version-1.1.3-cyan" alt="version">
<img src="https://img.shields.io/badge/build-pass-red" alt="build">
<img src="https://img.shields.io/badge/test-pass-green" alt="test">
</p>

<p align="center">
<img src="public/wallpaper-02.png" />
</p>


## About Task-Manager

Task-manager is a Laravel system for managing tasks on your server. It functions as an interface for ```cronjobs``` and executes tasks in the background, whether it's a Linux script, a PHP file, or a specific command on your production server. It can run on both dedicated and shared servers.

## Tested Hosting Services

| Service | Provider | Status |
|---------|----------|--------|
| Dedicated Hosting | Hostinger | ✅ |
| Shared Hosting | Hostinger | ✅ |
| Premium Hosting | Hostigator | ✅ |
| Shared Hosting | Hostigator | ✅ |
| Dedicated Hosting | Digital Ocean | ✅ |

## Installation

To install, the following requirements are needed:

>- PHP version 8.2 or higher
>- Composer version 2.7 or higher
>- cURL installed
>- Database: sqlite3, MySQL, PostgreSQL, or MongoDB

#### Step-by-Step Installation
* Clone this repository or download it to your server.
* Install the dependencies with composer:
```sh
composer install --no-interaction --prefer-dist --optimize-autoloader
```
* Create a ```.env``` file from the ```.env.example``` file:
```sh
cat .env.example >> .env
```
* Generate an application key:
```sh
php artisan key:generate
```
* Run the system migration
```sh
php artisan migrate
```

## Configuring Services

After installation, the system is accessible via the installation URL.
To access the panel, use the route ```/manager```.

However, the system still needs to be configured. For the system to function, you need to point only 1 cronjob to the endpoint ```/supervisor``` with ```cURL``` or ```wget```. This way, the system internally executes the command ```app:supervisor```, which keeps the registered processes and services running continuously according to the configuration and interval set.

>- NOTE: To ensure that the Manager's services remain always running, it is recommended to use a 1-minute interval for the cronjob.

Example to set cronjob
 ```bash
* * * * * curl https://[your-taskmanager-domain.com]/supervisor
 ```
Change [your-taskmanager-domain.com] for your task-manager application domain.
