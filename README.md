# Customer API

This project implements a RESTful API to perform CRUD operations on a "Customer" entity using PHP and MySQL. The API supports JSON and `multipart/form-data` formats for requests and responds with JSON.

## Prerequisites

- PHP (>= 7.4)
- MySQL Database
- Web Server (e.g., Apache, Nginx)
- Composer (optional for dependencies)

## Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/MuhammadMahediHasan/php-RESTful-API.git
   cd php-RESTful-API
   ```

2. **Database Configuration**
    - Update database name in `api.php` file.
    - Create a `customers` table with the following structure:
      ```sql
      CREATE TABLE customers (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(255) NOT NULL,
          email VARCHAR(255) NOT NULL
      );
      ```

3. **API Deployment**
    - Place the `api.php` file in your web server's document root.
    - Ensure your server supports PHP and MySQL.

4. **Start the Server** (For built-in PHP server):
   ```bash
   php -S localhost:8000
   ```

5. **Test the API**
   Use tools like [Postman](https://www.postman.com/) or `curl` to test the API endpoints.

## API Endpoints

### Create a Customer (POST /api.php)
- **Request:**
  ```json
  {
      "name": "John Doe",
      "email": "john.doe@example.com"
  }
  ```
- **Response:**
  ```json
  {
      "message": "Customer created",
      "id": 1
  }
  ```

### Get All Customers (GET /api.php)
- **Request:**
    - URL: `GET /api.php`

- **Response:**
  ```json
  [
      {
          "id": 1,
          "name": "John Doe",
          "email": "john.doe@example.com"
      }
  ]
  ```

### Get a Single Customer (GET /api.php?id=1)
- **Request:**
    - URL: `GET /api.php?id=1`

- **Response:**
  ```json
  {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com"
  }
  ```

### Update a Customer (PUT /api.php)
- **Request:**
    - URL: `PUT /api.php?id=1`
    - Body:
      ```json
      {
          "name": "Jane Doe",
          "email": "jane.doe@example.com"
      }
      ```
- **Response:**
  ```json
  {
      "message": "Customer updated"
  }
  ```

### Delete a Customer (DELETE /api.php)
- **Request:**
    - URL: `DELETE /api.php?id=1`

- **Response:**
  ```json
  {
      "message": "Customer deleted"
  }
  ```

## Notes
- Ensure the database credentials in the script are correct.
- For `multipart/form-data`, ensure proper request formatting to parse data correctly.

## License
This project is licensed under the [MIT License](LICENSE).

