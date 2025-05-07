# Autolink – Recycled Car Parts Web App

Autolink is a web application built with Symfony that offers an eco-friendly platform for buying and managing recycled car parts. It connects users with trusted recycling suppliers and promotes sustainable automotive maintenance.

👉 **Also available as a Desktop App**: [Autolink Java Desktop Application](https://github.com/AutolinkTechX/AutolinkCleanVersion)

## Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Features
- **Advanced Search**: Find parts by name, type, vehicle model, or supplier  
- **Shopping Cart & Checkout**: Add parts to cart and complete purchases  
- **Order History**: Track past orders and current status  
- **Supplier Management**: Admin portal for inventory management  
- **Secure Authentication**: Protected user accounts with encryption  

## Installation

### Prerequisites
- PHP 8.1 or later  
- Composer  
- Symfony CLI (optional but recommended)  
- MySQL Database  

### Step-by-Step Setup

1. **Clone and enter project directory**:
    ```bash
    git clone https://github.com/AutolinkTechX/AutolinkSymfony.git
    cd AutolinkSymfony
    ```

2. **Install dependencies**:
    ```bash
    composer install
    ```

3. **Configure Environment**:
    - Copy the environment file:
      ```bash
      cp .env .env.local
      ```
    - Edit `.env.local` with your database credentials:
      ```
      DATABASE_URL="mysql://root:@127.0.0.1:3306/autolinkdb"
      ```

4. **Set up the database**:
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

5. **Run the application**:
    ```bash
    symfony server:start
    ```
    Or:
    ```bash
    php bin/console server:start
    ```

## Usage

### For Buyers
- Browse parts using search filters  
- Add items to shopping cart  
- Secure checkout process  
- Track order history  

### For Administrators
- Manage supplier accounts  
- Monitor inventory levels  
- Process orders and shipments  
- Generate sales reports  

## Contributing

We welcome contributions!

1. Fork the repository.  
2. Create a new branch:
    ```bash
    git checkout -b feature/your-feature-name
    ```
3. Make your changes and commit them:
    ```bash
    git commit -m "Add your message here"
    ```
4. Push to the branch:
    ```bash
    git push origin feature/your-feature-name
    ```
5. Open a pull request detailing your changes.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For questions or feedback, please open an issue on the [GitHub repository](https://github.com/AutolinkTechX/AutolinkSymfony/issues).
