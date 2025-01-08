# WP-UserAuth

A Single Plugin Which provides WP ReST-API's for user registration, login, and Authentication.

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Contributing](#contributing)
- [License](#license)

## Installation

To install the WP-UserAuth plugin, follow these steps:

1. Download and Upload the plugin files to the `/wp-content/plugins/wp-UserAuth` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the provided ReST-API endpoints for user registration, login, and authentication.

## Usage

Here are some examples of how to use the plugin:

### User Registration
```bash
POST /wp-json/wpvbr/v1/register
```

### User Login
```bash
POST /wp-json/wpvbr/v1/login
```

### User Logout
```bash
POST /wp-json/wpvbr/v1/logout
```

### Check Login Cookie
```bash
GET /wp-json/wpvbr/v1/checkLoginCookie
```

## Features

- User Registration via ReST-API
- User Login via ReST-API
- User Logout via ReST-API
- Check Login Cookie via ReST-API

## Contributing

We welcome contributions to this project. Please follow these guidelines for contributing:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Create a new Pull Request.

For any inquiries or contributions, please reach out through the project's GitHub issues or pull requests.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).