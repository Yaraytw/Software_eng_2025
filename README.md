# Cinema Ticketing System

A comprehensive web-based cinema ticketing system built with PHP and MySQL.

**114的軟體工程期末project**

## Features

### Member Management
- **User Registration**: New users can create an account with username, password, email, full name, and phone number
- **User Login**: Secure authentication system with password hashing
- **Cash Card Top-up**: Members can add funds to their cash card for ticket purchases

### Movie Booking
- **Cinema Selection**: Choose from multiple cinema locations
- **Movie Selection**: Browse available movies at selected cinema
- **Session Selection**: View and select show times for chosen movies
- **Seat Selection**: Interactive seat map with real-time availability
- **Booking Confirmation**: Secure payment processing using cash card balance

### Additional Features
- **Booking Inquiry**: View all past and current bookings with detailed information
- **Refund System**: Request refunds for future bookings with automatic seat release
- **Responsive Design**: Mobile-friendly interface that works on all devices

## Database Schema

The system uses the following database tables:

1. **memberProfile**: Stores member account information
2. **memberCashCard**: Manages member cash card balances
3. **cinema**: Cinema location information
4. **theater**: Theater/hall information within each cinema
5. **movie**: Movie details and metadata
6. **showing**: Movie session/showing information
7. **bookingRecord**: Booking transaction records
8. **seatCondition**: Individual seat status for each showing

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/Yaraytw/Software_eng_2025.git
   cd Software_eng_2025
   ```

2. **Configure the database**
   
   Edit `config/db_config.php` and update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'cinema_ticketing');
   ```

3. **Create the database**
   
   Run the SQL schema file to create the database and tables:
   ```bash
   mysql -u your_username -p < config/schema.sql
   ```
   
   Or use phpMyAdmin:
   - Create a new database named `cinema_ticketing`
   - Import the `config/schema.sql` file

4. **Configure web server**
   
   Point your web server document root to the project directory.
   
   For Apache, you might need to enable `.htaccess` and mod_rewrite.

5. **Set permissions**
   ```bash
   chmod 755 -R .
   chmod 644 config/db_config.php
   ```

6. **Access the application**
   
   Open your web browser and navigate to:
   ```
   http://localhost/
   ```

## Demo Account

The system comes with a pre-configured demo account:

- **Username**: demouser
- **Password**: password123
- **Cash Card Balance**: $1,000.00

## File Structure

```
Software_eng_2025/
├── config/
│   ├── db_config.php       # Database connection configuration
│   └── schema.sql          # Database schema and sample data
├── includes/
│   └── functions.php       # Common functions and session handling
├── css/
│   └── style.css          # Main stylesheet
├── js/                    # JavaScript files (if needed)
├── images/                # Image assets
├── index.php              # Home page
├── login.php              # Login page
├── register.php           # Registration page
├── logout.php             # Logout handler
├── topup.php              # Cash card top-up page
├── select_cinema.php      # Cinema selection page
├── select_movie.php       # Movie selection page
├── select_session.php     # Session/showing selection page
├── select_seat.php        # Seat selection and booking page
├── inquiry.php            # View bookings page
├── refund.php             # Refund request page
└── README.md              # This file
```

## Usage Guide

### For Members

1. **Register/Login**
   - Create a new account or login with existing credentials
   - Demo account available for testing

2. **Top-up Cash Card**
   - Navigate to "Top-up" page
   - Enter amount and confirm
   - Balance updates immediately

3. **Book Tickets**
   - Select a cinema location
   - Choose a movie
   - Pick a show time
   - Select your seats
   - Confirm and pay with cash card

4. **View Bookings**
   - Access "My Bookings" page
   - View all booking details
   - See booking status (confirmed/cancelled/refunded)

5. **Request Refund**
   - From "My Bookings" page
   - Click "Request Refund" on eligible bookings
   - Confirm refund request
   - Amount automatically refunded to cash card

## Technical Details

### Security Features
- Password hashing using PHP's `password_hash()`
- SQL injection protection with prepared statements
- Session-based authentication
- XSS prevention with `htmlspecialchars()`

### Database Transactions
- Booking process uses transactions for data integrity
- Refund process ensures all related records are updated atomically
- Seat availability managed with proper locking

### Responsive Design
- Mobile-first CSS approach
- Grid layout for movie/cinema cards
- Flexible navigation menu
- Touch-friendly seat selection

## Sample Data

The system includes sample data for:
- 3 cinemas across different cities
- 5 theaters with various types (standard, IMAX, VIP, 3D)
- 4 movies (3 now showing, 1 coming soon)
- 7 showings across different dates and times

## Future Enhancements

Potential features for future development:
- Email notifications for booking confirmations
- QR code tickets
- Online payment gateway integration
- Movie reviews and ratings
- Loyalty points system
- Admin dashboard for managing movies and showings
- Seat recommendations based on viewing preferences
- Multi-language support

## Troubleshooting

### Database Connection Issues
- Verify database credentials in `config/db_config.php`
- Ensure MySQL service is running
- Check if database exists and schema is imported

### Permission Errors
- Ensure proper file permissions
- Check web server user has access to files

### Session Issues
- Verify PHP session is enabled
- Check session directory permissions

## Support

For issues, questions, or contributions, please open an issue on GitHub.

## License

This project is created for educational purposes.

## Credits

Developed as a Software Engineering 2025 project.
