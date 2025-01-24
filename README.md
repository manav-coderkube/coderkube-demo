# User Registration and Multi-Login System

This project is a **Week 1 Assignment** for **CoderKube**. It implements a MySQL-backed user registration system with login functionality for multiple user types (Admin, User, and Seller).

## Features
- **User Registration**: Register with validation checks for name, email, mobile number, gender, user type, and password.
- **Login Functionality**: Allows different user types (Admin, User, and Seller) to log in and redirects them to their respective dashboards.
- **Server-Side Validation**: Secure validation checks using PHP.
- **Client-Side Validation**: User-friendly validation using jQuery for real-time feedback.
- **SQL Injection Prevention**: Uses prepared statements for database queries.

---

## Project Requirements
- **Frontend**: HTML, CSS, JavaScript (jQuery for validation).
- **Backend**: PHP for server-side processing.
- **Database**: MySQL for storing user data.

---

## Installation
Follow these steps to set up the project on your local machine:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/manav-coderkube/coderkube-demo.git

2. **Import the database or Set-up the database**
* Import the db_schema.sql file into your MySQL server to create the required database and tables.
* Example schema:
<p>CREATE TABLE tbl_user (
    user_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) UNIQUE NOT NULL,
    user_email VARCHAR(100) UNIQUE NOT NULL,
    user_phone VARCHAR(10) UNIQUE NOT NULL,
    user_gender INT(1) NOT NULL,
    user_type INT(1) NOT NULL,
    user_password VARCHAR(100) NOT NULL
);
</p>
3. **Configure the Database Connection:**

* Update the db_connect.php file with your MySQL credentials:
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

4. **Start the Server:**

* Use a local server environment like XAMPP or WAMP.
* Place the project folder in the htdocs directory.

5. **Access the Application:**

Open your browser and navigate to http://localhost/<project-folder>/.
