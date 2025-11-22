-- Cinema Ticketing System Database Schema
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS cinema_ticketing;
USE cinema_ticketing;

-- Table: memberProfile
-- Stores member information
CREATE TABLE IF NOT EXISTS memberProfile (
    memberID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    fullName VARCHAR(100) NOT NULL,
    phoneNumber VARCHAR(20),
    registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lastLogin TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: memberCashCard
-- Stores member cash card balance information
CREATE TABLE IF NOT EXISTS memberCashCard (
    cardID INT AUTO_INCREMENT PRIMARY KEY,
    memberID INT NOT NULL,
    balance DECIMAL(10, 2) DEFAULT 0.00,
    lastTopUpDate TIMESTAMP NULL,
    lastTopUpAmount DECIMAL(10, 2),
    createdDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (memberID) REFERENCES memberProfile(memberID) ON DELETE CASCADE,
    INDEX idx_memberID (memberID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: cinema
-- Stores cinema location information
CREATE TABLE IF NOT EXISTS cinema (
    cinemaID INT AUTO_INCREMENT PRIMARY KEY,
    cinemaName VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(50) NOT NULL,
    phoneNumber VARCHAR(20),
    openingHours VARCHAR(100),
    status ENUM('open', 'closed', 'maintenance') DEFAULT 'open',
    INDEX idx_city (city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: theater
-- Stores theater (hall) information within each cinema
CREATE TABLE IF NOT EXISTS theater (
    theaterID INT AUTO_INCREMENT PRIMARY KEY,
    cinemaID INT NOT NULL,
    theaterName VARCHAR(50) NOT NULL,
    totalSeats INT NOT NULL,
    theaterType ENUM('standard', 'imax', 'vip', '3d') DEFAULT 'standard',
    status ENUM('available', 'maintenance', 'closed') DEFAULT 'available',
    FOREIGN KEY (cinemaID) REFERENCES cinema(cinemaID) ON DELETE CASCADE,
    INDEX idx_cinemaID (cinemaID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: movie
-- Stores movie information
CREATE TABLE IF NOT EXISTS movie (
    movieID INT AUTO_INCREMENT PRIMARY KEY,
    movieTitle VARCHAR(200) NOT NULL,
    description TEXT,
    director VARCHAR(100),
    cast TEXT,
    genre VARCHAR(100),
    duration INT NOT NULL COMMENT 'Duration in minutes',
    rating VARCHAR(10) COMMENT 'e.g., G, PG, PG-13, R',
    releaseDate DATE,
    language VARCHAR(50),
    posterImage VARCHAR(255),
    trailerURL VARCHAR(255),
    status ENUM('coming_soon', 'now_showing', 'ended') DEFAULT 'now_showing',
    INDEX idx_status (status),
    INDEX idx_releaseDate (releaseDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: showing
-- Stores movie showing/session information
CREATE TABLE IF NOT EXISTS showing (
    showingID INT AUTO_INCREMENT PRIMARY KEY,
    movieID INT NOT NULL,
    cinemaID INT NOT NULL,
    theaterID INT NOT NULL,
    showDate DATE NOT NULL,
    showTime TIME NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    availableSeats INT NOT NULL,
    status ENUM('available', 'sold_out', 'cancelled') DEFAULT 'available',
    FOREIGN KEY (movieID) REFERENCES movie(movieID) ON DELETE CASCADE,
    FOREIGN KEY (cinemaID) REFERENCES cinema(cinemaID) ON DELETE CASCADE,
    FOREIGN KEY (theaterID) REFERENCES theater(theaterID) ON DELETE CASCADE,
    INDEX idx_movie_cinema (movieID, cinemaID),
    INDEX idx_showDate (showDate),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: bookingRecord
-- Stores booking/reservation information
CREATE TABLE IF NOT EXISTS bookingRecord (
    bookingID INT AUTO_INCREMENT PRIMARY KEY,
    memberID INT NOT NULL,
    showingID INT NOT NULL,
    bookingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    totalSeats INT NOT NULL,
    totalAmount DECIMAL(10, 2) NOT NULL,
    paymentMethod ENUM('cash_card', 'credit_card', 'cash') DEFAULT 'cash_card',
    bookingStatus ENUM('confirmed', 'cancelled', 'refunded') DEFAULT 'confirmed',
    refundDate TIMESTAMP NULL,
    refundAmount DECIMAL(10, 2),
    FOREIGN KEY (memberID) REFERENCES memberProfile(memberID) ON DELETE CASCADE,
    FOREIGN KEY (showingID) REFERENCES showing(showingID) ON DELETE CASCADE,
    INDEX idx_memberID (memberID),
    INDEX idx_showingID (showingID),
    INDEX idx_bookingStatus (bookingStatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: seatCondition
-- Stores individual seat booking information
CREATE TABLE IF NOT EXISTS seatCondition (
    seatID INT AUTO_INCREMENT PRIMARY KEY,
    showingID INT NOT NULL,
    bookingID INT,
    seatRow VARCHAR(5) NOT NULL COMMENT 'e.g., A, B, C',
    seatNumber INT NOT NULL COMMENT 'Seat number in the row',
    seatStatus ENUM('available', 'reserved', 'sold') DEFAULT 'available',
    FOREIGN KEY (showingID) REFERENCES showing(showingID) ON DELETE CASCADE,
    FOREIGN KEY (bookingID) REFERENCES bookingRecord(bookingID) ON DELETE SET NULL,
    UNIQUE KEY unique_seat_per_showing (showingID, seatRow, seatNumber),
    INDEX idx_showing_status (showingID, seatStatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for testing

-- Sample cinemas
INSERT INTO cinema (cinemaName, address, city, phoneNumber, openingHours, status) VALUES
('Star Cinema Downtown', '123 Main Street', 'Taipei', '02-1234-5678', '10:00 AM - 11:00 PM', 'open'),
('Galaxy Multiplex', '456 Movie Boulevard', 'Taipei', '02-2345-6789', '09:00 AM - 12:00 AM', 'open'),
('Premium Cinema', '789 Entertainment Ave', 'Taichung', '04-3456-7890', '10:00 AM - 11:00 PM', 'open');

-- Sample theaters for each cinema
INSERT INTO theater (cinemaID, theaterName, totalSeats, theaterType, status) VALUES
(1, 'Hall 1', 100, 'standard', 'available'),
(1, 'Hall 2', 150, 'imax', 'available'),
(2, 'Hall 1', 120, 'standard', 'available'),
(2, 'Hall 2', 80, 'vip', 'available'),
(3, 'Hall 1', 100, '3d', 'available');

-- Sample movies
INSERT INTO movie (movieTitle, description, director, cast, genre, duration, rating, releaseDate, language, status) VALUES
('The Adventure Begins', 'An epic adventure across distant lands', 'John Director', 'Actor A, Actor B, Actor C', 'Adventure', 120, 'PG-13', '2025-11-01', 'English', 'now_showing'),
('Mystery of the Night', 'A thrilling mystery that unfolds in the darkness', 'Jane Director', 'Actor D, Actor E', 'Mystery/Thriller', 105, 'PG-13', '2025-11-10', 'English', 'now_showing'),
('Comedy Gold', 'The funniest movie of the year', 'Bob Director', 'Actor F, Actor G, Actor H', 'Comedy', 95, 'PG', '2025-11-15', 'English', 'now_showing'),
('Future World', 'A sci-fi masterpiece about the future', 'Alice Director', 'Actor I, Actor J', 'Sci-Fi', 140, 'PG-13', '2025-12-01', 'English', 'coming_soon');

-- Sample showings
INSERT INTO showing (movieID, cinemaID, theaterID, showDate, showTime, price, availableSeats, status) VALUES
(1, 1, 1, '2025-11-25', '14:00:00', 250.00, 100, 'available'),
(1, 1, 1, '2025-11-25', '17:00:00', 300.00, 100, 'available'),
(1, 1, 2, '2025-11-25', '20:00:00', 350.00, 150, 'available'),
(2, 1, 1, '2025-11-26', '15:00:00', 250.00, 100, 'available'),
(2, 2, 3, '2025-11-26', '18:00:00', 280.00, 120, 'available'),
(3, 2, 4, '2025-11-27', '16:00:00', 400.00, 80, 'available'),
(3, 3, 5, '2025-11-27', '19:00:00', 320.00, 100, 'available');

-- Sample member (password is 'password123' hashed with bcrypt-like hash)
INSERT INTO memberProfile (username, email, fullName, phoneNumber, password, status) VALUES
('demouser', 'demo@example.com', 'Demo User', '0912-345-678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active');

-- Sample cash card for the demo user
INSERT INTO memberCashCard (memberID, balance) VALUES
(1, 1000.00);
