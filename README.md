# Symfony Micropost

Welcome to Symfony Micropost, a web application for managing microposts, user profiles, and API endpoints.

## Overview

Symfony Micropost is a lightweight application built using the Symfony PHP framework. It provides a streamlined architecture for handling microposts and user interactions. The application consists of several controllers, each responsible for specific functionalities:

- **ApiController**: Handles actions related to the API, including posting new microposts and user registration.
- **DashboardController**: Manages the dashboard interface, allowing users to view and edit their profiles, upload images, change passwords, and delete accounts.
- **PostsController**: Controls the creation, deletion, editing, and display of microposts, as well as user interactions such as following and unfollowing users.
- **Authentication and Authorization**: Implements secure login and logout functionality, with password hashing for user security.

## Features

Symfony Micropost offers the following features:

- **API Endpoint**: Allows users to post microposts through API requests, providing a lightweight interface for integrating with other applications.
- **Dashboard Interface**: Enables users to manage their profiles, upload profile images, change passwords, and delete accounts.
- **Micropost Management**: Facilitates the creation, deletion, and editing of microposts, as well as user interactions such as following and unfollowing other users.
- **Authentication and Authorization**: Implements secure user authentication and authorization, ensuring user data privacy and security.

## Jenkins Integration

Symfony Micropost is integrated with Jenkins for continuous integration and deployment. Jenkins automates the build, test, and deployment processes, ensuring smooth development workflows and efficient delivery of updates.

## Getting Started

To get started with Symfony Micropost, follow these steps:

1. Clone the repository to your local machine.
2. Install dependencies using Composer: `composer install`.
3. Set up your database configuration in `.env` file.
4. Run database migrations: `php bin/console doctrine:migrations:migrate`.
5. Start the Symfony server: `symfony serve`.
6. Access the application in your web browser.

## License

Symfony Micropost is a personal project and does not have a specific license.

## Support

For any questions or issues, please [open an issue](https://github.com/BenRhoumaMaher/symfony_microspost/issues) on GitHub.

Enjoy using Symfony Micropost! 🚀
