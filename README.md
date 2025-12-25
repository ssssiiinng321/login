# Your Purpose - Register/Login

Authentication system including Login, Registration, and Dashboard.
Hosted on Vercel.

## Local Setup (XAMPP)

1.  **Start Server**: Open XAMPP Control Panel and start **Apache** and **MySQL**.
2.  **Database Setup**:
    - Open [phpMyAdmin](http://localhost/phpmyadmin).
    - Create a new database named `user_auth`.
    - Import `database.sql` (located in the project root) into this database.
    - *Note: This will create the `users`, `products`, `orders`, and `order_items` tables.*
3.  **Run Application**:
    - Open your browser and navigate to: [http://localhost/register/api/](http://localhost/register/api/)
    - Login with your registered account.

## Features
- **User Authentication**: Login and Register.
- **Dashboard**: View products and orders.
- **Sales Management**: Add/Edit products, update order status.
