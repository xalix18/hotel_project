-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2025 at 01:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$10$6lw1jT79Rdg0NK6RpR3uIe9./bkLir1WBih58sCJ.ljaNTlq4rC0G');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `image_url`, `created_at`) VALUES
(1, 'تخفیف ویژه تابستان!', 'از هوای گرم تابستان لذت ببرید و با ۲۰٪ تخفیف در تمامی اتاق‌ها، اقامتی خنک و دلپذیر را در هتل ما تجربه کنید. این پیشنهاد ویژه برای رزروهای انجام شده تا پایان مرداد ماه معتبر است.', 'https://placehold.co/600x400/3498db/FFFFFF?text=Summer+Offer', '2025-07-07 10:00:06');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `alt_text` varchar(150) DEFAULT 'Hotel gallery image',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `image_filename`, `alt_text`, `uploaded_at`) VALUES
(1, 'gallery_686b8f5f587626.38040859.png', 'بیصشصش', '2025-07-07 09:11:59'),
(2, 'gallery_686b8f7bc70506.55682467.jpg', 'یشسص', '2025-07-07 09:12:27');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(100) NOT NULL,
  `guest_phone` varchar(20) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'در انتظار',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `room_id`, `guest_name`, `guest_email`, `guest_phone`, `check_in_date`, `check_out_date`, `status`, `created_at`) VALUES
(1, 3, 'ali mkese', 'iau67r@ezztt.com', '09028132800', '2025-07-29', '2025-07-31', 'در انتظار', '2025-07-06 17:03:50'),
(2, 2, 'Kimberly R. Brown', 'iau67r@ezztt.com', '09028132800', '2025-07-04', '2025-07-07', 'تایید شده', '2025-07-06 17:04:25'),
(3, 2, 'Kimberly R. Brown', 'iau67r@ezztt.com', '09028132800', '2025-07-30', '2025-08-11', 'تایید شده', '2025-07-06 17:18:23'),
(4, 2, 'Kimberly R. Brown', 'iau67r@ezztt.com', '09028132800', '2025-07-09', '2025-07-21', 'تایید شده', '2025-07-06 17:40:05');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `main_image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `price_per_night`, `capacity`, `main_image_url`) VALUES
(1, 'اتاق دلوکس', 'یک انتخاب عالی برای سفرهای کاری یا تفریحی با منظره‌ای زیبا از شهر. این اتاق دارای یک تخت دو نفره، میز کار، و حمام مدرن است.', 2500000.00, 2, 'https://placehold.co/800x600/a0d8ef/FFFFFF?text=Deluxe+Room'),
(2, 'سوئیت رویال', 'فضایی وسیع و مجلل با امکانات کامل برای یک اقامت فراموش‌نشدنی. این سوئیت شامل یک اتاق خواب جداگانه، فضای نشیمن، و بالکن اختصاصی است.', 5000000.00, 4, 'https://placehold.co/800x600/87CEEB/FFFFFF?text=Royal+Suite'),
(3, 'اتاق خانوادگی', 'راحت و جادار، بهترین گزینه برای اقامت به همراه خانواده و عزیزان. شامل یک تخت دو نفره و دو تخت یک نفره.', 3800000.00, 4, 'uploads/room_main_3_686ba0be407a9.png');

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `alt_text` varchar(150) DEFAULT 'Image of the room'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`id`, `room_id`, `image_url`, `alt_text`) VALUES
(1, 1, 'https://placehold.co/800x600/a0d8ef/FFFFFF?text=Deluxe+Room', 'اتاق دلوکس'),
(2, 2, 'https://placehold.co/800x600/87CEEB/FFFFFF?text=Royal+Suite', 'سوئیت رویال'),
(4, 1, 'https://placehold.co/800x600/a5ddee/FFFFFF?text=Deluxe+View', 'View from Deluxe Room'),
(5, 1, 'https://placehold.co/800x600/add8e6/FFFFFF?text=Deluxe+Bathroom', 'Bathroom of Deluxe Room'),
(6, 2, 'https://placehold.co/800x600/87CEEB/FFFFFF?text=Royal+Suite+Living', 'Living area of Royal Suite'),
(7, 3, 'uploads/room3_686b942a494f83.79519889.jpg', 'Image of the room'),
(8, 3, 'uploads/room3_686b943f88e5c8.81400195.jpg', 'Image of the room');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('hero_image_url', 'uploads/hero_686b9ea102ede.jpg'),
('hotel_address', 'تهران، خیابان فرشته، کوچه شبنم، پلاک ۱۰'),
('hotel_email', 'info@blueskyhotel.ir'),
('hotel_name', 'هتل آسمان آبی'),
('hotel_phone', '021-12345678'),
('hotel_welcome_subtitle', 'تجربه‌ای بی‌نظیر از آرامش و راحتی در قلب شهر با بهترین خدمات و امکانات مدرن.'),
('hotel_welcome_title', 'اقامتی رویایی در هتل آسمان آبی');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
