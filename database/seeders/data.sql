INSERT INTO `address` (`id`, `location`, `country`, `province`, `city`, `postal_code`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 08:50:27', '2025-02-28 09:42:44'),
(2, 'Clifton Beach', 'Pakistan', 'Sindh', 'Karachi', '72000', NULL, NULL, '2025-02-28 08:50:27', '2025-02-28 09:43:48'),
(3, 'Quetta Cantt', 'Pakistan', 'Balochistan', 'Quetta', '87800', NULL, NULL, '2025-02-28 08:50:27', '2025-02-28 08:50:27'),
(4, 'Daman-e-Koh', 'Pakistan', 'Capital', 'Islamabad', '44050', NULL, NULL, '2025-02-28 08:50:27', '2025-03-20 12:18:15'),
(5, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 09:16:35', '2025-02-28 09:42:44'),
(6, 'Clifton Beach', 'Pakistan', 'Sindh', 'Karachi', '72000', NULL, NULL, '2025-02-28 09:16:35', '2025-02-28 09:43:48'),
(7, 'Quetta Cantt', 'Pakistan', 'Balochistan', 'Quetta', '87800', NULL, NULL, '2025-02-28 09:16:35', '2025-03-20 06:46:07'),
(8, 'Daman-e-Koh', 'Pakistan', 'Capital', 'Islamabad', '44050', NULL, NULL, '2025-02-28 09:16:35', '2025-03-20 06:46:20'),
(9, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 09:16:35', '2025-02-28 09:42:44'),
(10, 'Clifton Beach', 'Pakistan', 'Sindh', 'Karachi', '72000', NULL, NULL, '2025-02-28 09:16:35', '2025-02-28 09:43:48'),
(11, 'Quetta Cantt', 'Pakistan', 'Balochistan', 'Quetta', '87800', NULL, NULL, '2025-02-28 09:16:35', '2025-02-28 09:16:35'),
(12, 'Daman-e-Koh', 'Pakistan', 'Capital', 'Islamabad', '44050', NULL, NULL, '2025-02-28 09:16:35', '2025-03-20 06:45:47'),
(13, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 09:16:35', '2025-02-28 09:42:44'),
(14, 'Clifton Beach', 'Pakistan', 'Sindh', 'Karachi', '72000', NULL, NULL, '2025-02-28 09:16:35', '2025-02-28 09:43:48'),
(15, 'Quetta Cantt', 'Pakistan', 'Balochistan', 'Quetta', '87800', NULL, NULL, '2025-02-28 09:16:35', '2025-02-28 09:16:35'),
(16, 'Daman-e-Koh', 'Pakistan', 'Capital', 'Islamabad', '44050', NULL, NULL, '2025-02-28 09:16:35', '2025-03-20 12:18:15'),
(17, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 09:42:17', '2025-02-28 09:42:44'),
(18, 'Ali Town', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-03-20 04:28:27', '2025-03-20 04:28:27'),
(19, 'Ali Town', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-03-20 04:36:49', '2025-03-20 04:36:49'),
(20, 'Ali Town', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-03-20 11:57:07', '2025-03-20 11:57:07'),
(21, 'Ali Town', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-03-20 11:57:07', '2025-03-20 11:57:07'),
(22, 'Ali Town', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-03-20 11:57:07', '2025-03-20 11:57:07');


INSERT INTO `roles` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Administrator with full access', 1, '2025-02-28 13:53:22', '2025-02-28 13:53:22'),
(2, 'Owner', 'NO Description', 1, '2025-02-28 13:53:22', '2025-02-28 13:53:22'),
(3, 'Manager', 'Helpers of the owner.', 1, '2025-02-28 13:54:32', '2025-03-20 07:49:01'),
(4, 'Staff', 'Building Operational Staff Members', 1, '2025-02-28 13:54:32', '2025-03-20 07:49:22'),
(5, 'User', 'Tanents and buyers.', 1, '2025-03-20 04:17:02', '2025-03-20 07:49:49');


INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone_no`, `cnic`, `date_of_birth`, `gender`, `picture`, `role_id`, `address_id`, `status`, `reset_token`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Syed Ibrahim', 'admin@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000001', '35202-0000000-1', '2002-07-29', 'Male', 'uploads/users/images/1738263413_User_1.jpg', 1, 8, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:11:20'),
(2, 'Jannat Naeem', 'owner1@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000002', '35202-0000000-2', '2002-07-29', 'Female', 'uploads/users/images/1738263414_User_2.jpg', 2, 9, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:12:28'),
(3, 'Abdullah Butt', 'owner2@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000003', '35202-0000000-3', '2002-07-29', 'Male', 'uploads/users/images/1738263430_User_3.jpg', 2, 10, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:11:20'),
(4, 'Arham Nasir', 'owner3@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000004', '35202-0000000-4', '2002-07-29', 'Male', 'uploads/users/images/1738263443_User_4.jpg', 2, 11, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:11:20'),
(5, 'Hira Gul', 'manager1@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000005', '35202-0000000-5', '2002-07-29', 'Female', 'uploads/users/images/1738263454_User_5.jpeg', 3, 12, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:23:10'),
(6, 'Usman Iqbal', 'manager2@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000006', '35202-0000000-6', '2002-07-29', 'Male', 'uploads/users/images/1739383093_User_6.jpg', 3, 13, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:11:20'),
(7, 'Burhan Khan', 'burhan@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000007', '35202-0000000-7', '2002-07-29', 'Male', 'uploads/users/images/1740824784_User_7.jpg', 4, 14, 1, NULL, 1, 7, '2025-02-28 09:17:27', '2025-03-20 16:45:05'),
(8, 'Shiza Butt', 'jannat@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000008', '35202-0000000-8', '2002-07-29', 'Female', 'uploads/users/images/1740824785_User_8.jpg', 4, 15, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:12:28'),
(9, 'Zeeshan Khan', 'zeeshan@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000009', '35202-0000000-9', '2002-07-29', 'Male', 'uploads/users/images/1740824786_User_9.jpg', 4, 16, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:11:20'),
(10, 'Shanza Malik', 'shanza@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000010', '35202-0000001-0', '2002-07-29', 'Female', 'uploads/users/images/1740824787_User_10.jpg', 4, 17, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:13:39'),
(11, 'Syed Abdullah', 'smibrahim297@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000011', '35202-0000001-1', '2002-07-29', 'Male', 'uploads/users/images/1740824788_User_11.jpg', 4, 18, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:15:06'),
(12, 'Rehan Manzoor', 'rh7081789@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000012', '35202-0000001-2', '2002-07-29', 'Male', 'uploads/users/images/1740824789_User_12.jpg', 4, 19, 1, NULL, 1, 1, '2025-02-28 09:17:27', '2025-03-20 12:15:06'),
(13, 'Neha Kashif', 'neha@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000013', '35202-0000001-3', '2002-07-29', 'Female', 'uploads/users/images/1740824789_User_13.jpg', 5, 20, 1, NULL, 1, 1, '2025-03-20 12:00:17', '2025-03-20 12:01:49'),
(14, 'Mahnoor Sheikh', 'mahnoor@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000014', '35202-0000001-4', '2002-07-29', 'Female', 'uploads/users/images/1740824789_User_14.jpg', 5, 21, 1, NULL, 1, 1, '2025-03-20 12:00:17', '2025-03-20 12:01:49'),
(15, 'Minahil Pathan', 'minahil@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000015', '35202-0000001-5', '2002-07-29', 'Female', 'uploads/users/images/1740824789_User_15.jpg', 5, 22, 1, NULL, 1, 1, '2025-03-20 12:00:17', '2025-03-20 12:01:49');


INSERT INTO `organizations` (`id`, `name`, `address_id`, `status`, `membership_start_date`, `membership_end_date`, `owner_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Bahria Town', 1, 'Enable', '2025-03-01', '2025-03-01', 2, 1, 1, '2025-02-28 14:50:35', '2025-02-28 14:50:35'),
(2, 'Etihad Town', 2, 'Enable', '2025-05-31', '2025-05-31', 3, 1, 1, '2025-02-28 14:50:35', '2025-02-28 14:50:35'),
(3, 'AL Noor', 3, 'Enable', '2025-03-20', '2026-12-20', 4, 1, 1, '2025-03-20 04:28:27', '2025-03-20 13:46:41');

INSERT INTO `organizationpictures` (`id`, `organization_id`, `file_path`, `file_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'uploads/organizations/images/1734415026_Organization_1.jpeg', '1734415026_Organization_1.jpeg', '2025-02-28 14:53:27', '2025-02-28 14:53:27'),
(2, 2, 'uploads/organizations/images/1734415045_Organization_2.png', '1734415045_Organization_2.png', '2025-02-28 14:53:27', '2025-02-28 14:53:27'),
(3, 3, 'uploads/organizations/images/1742462907_Organization_3.png', '1742462907_Organization_3.png', '2025-03-20 04:28:27', '2025-03-20 11:38:17');


INSERT INTO `buildings` (`id`, `organization_id`, `name`, `building_type`, `status`, `remarks`, `area`, `construction_year`, `address_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bahria Prime', 'Residential', 'Approved', 'Approved building for office use.', 100000.00, '2001', 4, 1, 1, '2025-02-28 15:01:47', '2025-03-20 13:44:47'),
(2, 1, 'HMY Heights', 'Commercial', 'Approved', 'Under review for new office setup.', 100000.00, '2002', 5, 1, 1, '2025-02-28 15:01:47', '2025-03-20 13:44:42'),
(3, 2, 'Islamabad SKY Apartments', 'Commercial', 'Approved', 'Under review for new office setup.', 100000.00, '2003', 6, 1, 1, '2025-02-28 15:01:47', '2025-03-20 13:44:37'),
(4, 1, 'J Heights', 'Residential', 'Under Processing', NULL, 100000.00, '2020', 7, 2, 2, '2025-03-20 04:36:49', '2025-03-20 18:53:13');


INSERT INTO `buildingpictures` (`id`, `building_id`, `file_path`, `file_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'uploads/buildings/images/1734379577_Building_1.jpg', '1734379577_Building_1.jpeg', '2025-02-28 15:05:35', '2025-03-21 09:03:32'),
(2, 2, 'uploads/buildings/images/1734379590_Building_2.jpg', '1734379590_Building_2.jpeg', '2025-02-28 15:05:35', '2025-03-21 09:03:32'),
(3, 3, 'uploads/buildings/images/1734379603_Building_3.jpg', '1734379603_Building_3.jpeg', '2025-02-28 15:05:35', '2025-03-21 09:03:32'),
(4, 4, 'uploads/buildings/images/1742463409_Building_4.jpg', '1742463409_Building_4.jpeg', '2025-03-20 04:36:49', '2025-03-21 09:03:32');


INSERT INTO `buildinglevels` (`id`, `organization_id`, `building_id`, `level_name`, `description`, `level_number`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'First Floor', 'Office space for rent', 1, 'Approved', 1, 1, '2025-02-28 10:12:11', '2025-03-28 11:22:34'),
(2, 1, 2, 'Second Floor', 'Meeting rooms and conference spaces', 2, 'Approved', 1, 1, '2025-02-28 10:12:11', '2025-03-28 11:22:34'),
(3, 2, 3, 'Ground Floor', 'Office reception area', 0, 'Approved', 1, 1, '2025-02-28 10:12:11', '2025-03-28 11:22:46'),
(4, 1, 4, 'First Floor', 'No Description', 1, 'Rejected', 2, 2, '2025-03-20 04:42:48', '2025-03-28 11:22:34'),
(5, 1, 4, 'Second Floor', 'No Description', 2, 'Rejected', 2, 2, '2025-03-20 04:42:06', '2025-03-28 11:22:34'),
(6, 1, 4, 'Third First Floor', 'No Description', 3, 'Rejected', 2, 2, '2025-03-20 04:42:48', '2025-03-28 11:22:34'),
(7, 1, 2, 'First Floor', 'No Description', 1, 'Approved', 1, 1, '2025-03-20 13:00:48', '2025-03-28 11:22:34'),
(8, 1, 1, 'Second Floor', 'No Description', 2, 'Approved', 1, 1, '2025-03-20 13:02:15', '2025-03-28 11:22:34'),
(9, 1, 1, 'Third Floor', 'No Description', 3, 'Approved', 1, 1, '2025-03-20 13:03:28', '2025-03-28 11:22:34'),
(10, 2, 3, 'First Floor', 'No Description', 1, 'Approved', 1, 1, '2025-03-20 13:08:47', '2025-03-28 11:22:46'),
(11, 2, 3, 'Second Floor', 'No Description', 2, 'Approved', 1, 1, '2025-03-20 13:10:36', '2025-03-28 11:22:46');


INSERT INTO `buildingunits` (`id`, `level_id`, `building_id`, `organization_id`, `unit_name`, `unit_type`, `availability_status`, `sale_or_rent`, `price`, `area`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 9, 1, 1, 'Room 01', 'Room', 'Rented', 'Rent', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:04:10'),
(2, 2, 2, 1, 'Shop 01', 'Shop', 'Rented', 'Sale', 150000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-21 11:49:46'),
(3, 10, 3, 2, 'Apartment 01', 'Apartment', 'Rented', 'Rent', 35000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-21 15:47:14'),
(4, 2, 2, 1, 'Room 02', 'Room', 'Sold', 'Sale', 100000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(5, 2, 2, 1, 'Shop 02', 'Shop', 'Available', 'Rent', 15000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(6, 9, 1, 1, 'Apartment 02', 'Apartment', 'Rented', 'Rent', 25000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:04:10'),
(7, 2, 2, 1, 'Room 03', 'Room', 'Available', 'Rent', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(8, 10, 3, 2, 'Shop 03', 'Shop', 'Available', 'Sale', 135000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:09:22'),
(9, 8, 1, 1, 'Apartment 03', 'Apartment', 'Rented', 'Rent', 20000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:05:41'),
(10, 3, 3, 2, 'Room 04', 'Room', 'Available', 'Rent', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(11, 8, 1, 1, 'Shop 04', 'Shop', 'Rented', 'Rent', 150000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:05:41'),
(12, 2, 2, 1, 'Apartment 04', 'Apartment', 'Available', 'Sale', 35000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(13, 1, 1, 1, 'Room 05', 'Room', 'Available', 'Rent', 100000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(14, 1, 1, 1, 'Shop 05', 'Shop', 'Rented', 'Rent', 15000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(15, 7, 2, 1, 'Apartment 05', 'Apartment', 'Available', 'Rent', 25000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:01:32'),
(16, 7, 2, 1, 'Room 06', 'Room', 'Sold', 'Sale', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:01:32'),
(17, 1, 1, 1, 'Shop 06', 'Shop', 'Available', 'Rent', 135000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(18, 7, 2, 1, 'Apartment 06', 'Apartment', 'Available', 'Sale', 20000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:01:32'),
(19, 8, 1, 1, 'Room 07', 'Room', 'Sold', 'Sale', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-03-20 18:02:56'),
(20, 3, 3, 2, 'Shop 07', 'Shop', 'Available', 'Sale', 150000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(21, 3, 3, 2, 'Apartment 07', 'Apartment', 'Available', 'Sale', 35000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 15:19:46', '2025-02-28 15:19:46'),
(22, 4, 4, 1, 'Shop 08', 'Shop', 'Available', 'Sale', 100000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Rejected', 2, 2, '2025-03-20 10:04:26', '2025-03-21 15:56:06'),
(23, 5, 4, 1, 'Shop 09', 'Shop', 'Available', 'Sale', 100000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Rejected', 2, 2, '2025-03-20 10:04:26', '2025-03-20 10:04:26'),
(24, 4, 4, 1, 'Room 08', 'Room', 'Available', 'Sale', 100000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Rejected', 2, 2, '2025-03-20 10:04:26', '2025-03-20 10:04:26'),
(25, 6, 4, 1, 'Room 09', 'Room', 'Available', 'Sale', 100000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Rejected', 2, 2, '2025-03-20 10:04:26', '2025-03-20 10:04:26'),
(26, 6, 4, 1, 'Room 10', 'Room', 'Available', 'Sale', 100000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Rejected', 2, 2, '2025-03-20 10:04:26', '2025-03-20 10:04:26'),
(27, 5, 4, 1, 'Room 11', 'Room', 'Available', 'Sale', 100000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Rejected', 2, 2, '2025-03-20 10:04:26', '2025-03-20 10:04:26'),
(28, 4, 4, 1, 'Apartment 08', 'Apartment', 'Available', 'Sale', 100000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Rejected', 2, 2, '2025-03-20 10:04:26', '2025-03-21 15:55:24'),
(29, 11, 3, 2, 'Apartment 09', 'Apartment', 'Available', 'Sale', 150000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-03-20 18:13:06', '2025-03-21 15:57:48');


INSERT INTO `unitpictures` (`id`, `unit_id`, `file_path`, `file_name`, `created_at`, `updated_at`) VALUES
(1, 3, 'uploads/units/images/Apartment_1.jpg', 'Apartment1', '2025-02-28 15:29:12', '2025-03-20 19:06:40'),
(2, 3, 'uploads/units/images/Apartment_2.jpg', 'Apartment2', '2025-02-28 15:29:12', '2025-03-20 19:06:40'),
(3, 3, 'uploads/units/images/Apartment_3.jpg', 'Apartment3', '2025-02-28 15:29:12', '2025-03-20 19:06:40'),
(4, 6, 'uploads/units/images/Apartment_4.jpg', 'Apartment4', '2025-02-28 15:29:12', '2025-03-20 19:14:15'),
(5, 6, 'uploads/units/images/Apartment_5.jpg', 'Apartment5', '2025-02-28 15:29:12', '2025-03-20 19:14:15'),
(6, 6, 'uploads/units/images/Apartment_6.jpg', 'Apartment6', '2025-02-28 15:29:12', '2025-03-20 19:14:15'),
(7, 9, 'uploads/units/images/Apartment_7.jpg', 'Apartment7', '2025-02-28 15:29:12', '2025-03-20 19:17:59'),
(8, 9, 'uploads/units/images/Apartment_8.jpg', 'Apartment8', '2025-02-28 15:29:12', '2025-03-20 19:17:59'),
(9, 9, 'uploads/units/images/Apartment_9.jpg', 'Apartment9', '2025-02-28 15:29:12', '2025-03-20 19:17:59'),
(10, 12, 'uploads/units/images/Apartment_10.jpg', 'Apartment10', '2025-02-28 15:29:12', '2025-03-20 19:25:03'),
(11, 12, 'uploads/units/images/Apartment_11.jpg', 'Apartment11', '2025-02-28 15:29:12', '2025-03-20 19:25:03'),
(12, 12, 'uploads/units/images/Apartment_12.jpg', 'Apartment12', '2025-02-28 15:29:12', '2025-03-20 19:25:03'),
(13, 15, 'uploads/units/images/Apartment_13.jpg', 'Apartment13', '2025-02-28 15:29:12', '2025-03-20 19:42:38'),
(14, 15, 'uploads/units/images/Apartment_14.jpg', 'Apartment14', '2025-02-28 15:29:12', '2025-03-20 19:42:38'),
(15, 15, 'uploads/units/images/Apartment_15.jpg', 'Apartment15', '2025-02-28 15:29:12', '2025-03-20 19:42:38'),
(16, 18, 'uploads/units/images/Apartment_16.jpg', 'Apartment16', '2025-02-28 15:29:12', '2025-03-20 19:47:40'),
(17, 18, 'uploads/units/images/Apartment_17.jpg', 'Apartment17', '2025-02-28 15:29:12', '2025-03-20 19:47:40'),
(18, 18, 'uploads/units/images/Apartment_18.jpg', 'Apartment18', '2025-02-28 15:29:12', '2025-03-20 19:47:40'),
(19, 21, 'uploads/units/images/Apartment_19.jpg', 'Apartment19', '2025-02-28 15:29:12', '2025-03-20 19:56:14'),
(20, 21, 'uploads/units/images/Apartment_20.jpg', 'Apartment20', '2025-02-28 15:29:12', '2025-03-20 18:50:35'),
(21, 21, 'uploads/units/images/Apartment_21.jpg', 'Apartment21', '2025-02-28 15:29:12', '2025-03-20 19:56:14'),
(22, 2, 'uploads/units/images/Shop_1.jpg', 'Shop1', '2025-02-28 15:29:12', '2025-03-20 20:02:49'),
(23, 5, 'uploads/units/images/Shop_2.jpg', 'Shop2', '2025-02-28 15:29:12', '2025-03-20 20:02:49'),
(24, 8, 'uploads/units/images/Shop_3.jpg', 'Shop3', '2025-02-28 15:29:12', '2025-03-20 20:02:49'),
(25, 11, 'uploads/units/images/Shop_4.jpg', 'Shop4', '2025-02-28 15:29:12', '2025-03-20 20:02:49'),
(26, 14, 'uploads/units/images/Shop_5.jpg', 'Shop5', '2025-02-28 15:29:12', '2025-03-20 20:02:49'),
(27, 17, 'uploads/units/images/Shop_6.jpg', 'Shop6', '2025-02-28 15:29:12', '2025-03-20 20:02:49'),
(28, 20, 'uploads/units/images/Shop_7.jpg', 'Shop7', '2025-02-28 15:29:12', '2025-03-20 20:02:49'),
(29, 1, 'uploads/units/images/Room_1.jpg', 'Room1', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(30, 4, 'uploads/units/images/Room_2.jpg', 'Room2', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(31, 7, 'uploads/units/images/Room_3.jpg', 'Room3', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(32, 10, 'uploads/units/images/Room_4.jpg', 'Room4', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(33, 13, 'uploads/units/images/Room_5.jpg', 'Room5', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(34, 16, 'uploads/units/images/Room_6.jpg', 'Room6', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(35, 19, 'uploads/units/images/Room_7.jpg', 'Room7', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(36, 1, 'uploads/units/images/Room_8.jpg', 'Room8', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(37, 4, 'uploads/units/images/Room_9.jpg', 'Room9', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(38, 7, 'uploads/units/images/Room_10.jpg', 'Room10', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(39, 10, 'uploads/units/images/Room_11.jpg', 'Room11', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(40, 13, 'uploads/units/images/Room_12.jpg', 'Room12', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(41, 16, 'uploads/units/images/Room_13.jpg', 'Room13', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(42, 19, 'uploads/units/images/Room_14.jpg', 'Room14', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(43, 1, 'uploads/units/images/Room_15.jpg', 'Room15', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(44, 4, 'uploads/units/images/Room_16.jpg', 'Room16', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(45, 7, 'uploads/units/images/Room_17.jpg', 'Room17', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(46, 10, 'uploads/units/images/Room_18.jpg', 'Room18', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(47, 13, 'uploads/units/images/Room_19.jpg', 'Room19', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(48, 16, 'uploads/units/images/Room_20.jpg', 'Room20', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(49, 19, 'uploads/units/images/Room_21.jpg', 'Room21', '2025-02-28 15:29:12', '2025-03-20 20:17:16'),
(50, 22, 'uploads/units/images/Shop_8.jpg', 'Shop8', '2025-03-20 10:09:19', '2025-03-20 20:13:42'),
(51, 23, 'uploads/units/images/Shop_9.jpg', 'Shop9', '2025-03-20 10:09:19', '2025-03-20 20:13:42'),
(52, 24, 'uploads/units/images/Room_22.jpg', 'Room22', '2025-03-20 10:09:19', '2025-03-20 20:17:16'),
(53, 25, 'uploads/units/images/Room_23.jpg', 'Room23', '2025-03-20 10:09:19', '2025-03-20 20:17:16'),
(54, 26, 'uploads/units/images/Room_24.jpg', 'Room24', '2025-03-20 10:09:19', '2025-03-20 20:17:16'),
(55, 27, 'uploads/units/images/Room_25.jpg', 'Room25', '2025-03-20 10:09:19', '2025-03-20 20:17:16'),
(56, 28, 'uploads/units/images/Apartment_22.jpg', 'Apartment22', '2025-03-20 10:09:19', '2025-03-20 19:57:27'),
(57, 29, 'uploads/units/images/Apartment_23.jpg', 'Apartment23', '2025-03-20 18:15:01', '2025-03-20 18:46:27'),
(58, 29, 'uploads/units/images/Apartment_24.jpg', 'Apartment24', '2025-03-20 18:15:01', '2025-03-20 18:46:27'),
(59, 29, 'uploads/units/images/Apartment_25.jpg', 'Apartment25', '2025-03-20 18:15:01', '2025-03-20 18:46:27');


INSERT INTO `userbuildingunits` (`id`, `user_id`, `unit_id`, `rent_start_date`, `rent_end_date`, `purchase_date`, `contract_status`, `type`, `price`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 14, 1, '2023-01-01', '2023-12-31', NULL, 1, 'Rented', 25000.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:36'),
(2, 13, 2, '2023-06-01', '2023-12-31', NULL, 1, 'Rented', 20000.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:36'),
(3, 15, 4, NULL, NULL, '2024-12-15', 1, 'Sold', 100000.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:37'),
(4, 15, 9, '2023-01-01', '2023-12-31', NULL, 1, 'Rented', 10000.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:36'),
(5, 15, 14, '2023-06-01', '2023-12-31', NULL, 1, 'Rented', 10000.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:36'),
(6, 13, 19, NULL, NULL, '2024-12-15', 1, 'Sold', 500000.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:36'),
(7, 13, 6, '2023-01-01', '2023-12-31', NULL, 1, 'Rented', 15000.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:36'),
(8, 13, 11, '2023-06-01', '2023-12-31', NULL, 1, 'Rented', 25000.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:37'),
(9, 14, 3, '2025-03-01', '2025-09-01', NULL, 1, 'Rented', 1500.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:36'),
(10, 14, 4, '2025-03-01', '2025-09-01', NULL, 1, 'Rented', 1500.00, 1, 1, '2025-02-28 15:52:41', '2025-03-20 12:46:36');



INSERT INTO `departments` (`id`, `name`, `description`, `organization_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Electric', 'null', 1, 1, 1, '2025-02-28 15:58:33', '2025-02-28 15:58:33'),
(2, 'Water', 'null', 1, 1, 1, '2025-02-28 15:58:33', '2025-02-28 15:58:33'),
(3, 'Management', 'null', 1, 1, 1, '2025-02-28 15:58:33', '2025-02-28 15:58:33'),
(4, 'Electric', 'null', 2, 1, 1, '2025-02-28 15:58:33', '2025-02-28 15:58:33'),
(5, 'Water', 'null', 2, 1, 1, '2025-02-28 15:58:33', '2025-02-28 15:58:33'),
(6, 'Management', 'null', 2, 1, 1, '2025-02-28 15:58:33', '2025-02-28 15:58:33');


INSERT INTO `staffmembers` (`id`, `user_id`, `department_id`, `building_id`, `organization_id`, `salary`, `active_load`, `accept_queries`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 1, 1, 0.0, 2, 1, 1, 1, 1, '2025-02-28 16:02:40', '2025-03-21 12:39:17'),
(2, 8, 1, 1, 1, 0.0, 1, 1, 1, 1, 1, '2025-02-28 16:02:40', '2025-03-21 15:42:37'),
(3, 9, 4, 2, 1, 0.0, 0, 1, 1, 1, 1, '2025-02-28 16:02:40', '2025-03-21 12:12:16'),
(4, 10, 4, 2, 1, 0.0, 0, 1, 1, 1, 1, '2025-02-28 16:02:40', '2025-03-21 11:54:38'),
(5, 11, 4, 3, 2, 0.0, 0, 1, 1, 1, 1, '2025-02-28 16:02:40', '2025-03-20 12:16:57'),
(6, 12, 4, 3, 2, 0.0, 0, 1, 1, 1, 1, '2025-03-20 12:16:45', '2025-03-20 12:16:45'),
(8, 5, 3, NULL, 1, 0.0, 0, 0, 1, 2, 2, '2025-03-22 22:29:33', '2025-03-22 22:32:06'),
(9, 6, 3, NULL, 1, 0.0, 0, 0, 1, 2, 2, '2025-03-22 22:40:23', '2025-03-22 22:40:23');


INSERT INTO `managerbuildings` (`id`, `user_id`, `staff_id`, `building_id`, `created_at`, `updated_at`) VALUES
(1, 5, 8, 1, '2025-03-22 22:41:28', '2025-03-22 22:46:05');


INSERT INTO `queries` (`id`, `user_id`, `unit_id`, `building_id`, `department_id`, `staff_member_id`, `description`, `status`, `expected_closure_date`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 13, 19, 1, 1, 2, 'I am experiencing a problem with my fan. It is not turning on, and I have already checked the power connection and switch. The fan does not respond at all.', 'Closed', '2024-12-25 14:28:16', 'Your issue has been resolved, thanks for your patience.', '2024-12-20 09:28:16', '2025-03-21 12:13:03'),
(2, 13, 2, 2, 1, 3, 'I am facing an issue with my air conditioner. It is not cooling properly, and sometimes it does not turn on at all. I have checked the power supply and remote settings, but the problem persists. Please assist me in resolving this issue as soon as possible.', 'Closed', '2024-12-27 14:28:16', 'Your issue has been resolved, thanks for your patience.', '2024-12-21 09:28:16', '2024-12-20 09:28:16'),
(3, 13, 6, 1, 1, 1, 'My fan is not working, but it keeps spinning faster every time I turn it off. When I unplug it, it starts working on its own. Also, the fan speed increases when I lower the setting and decreases when I increase it. Please look into this issue. ', 'Rejected', '2024-12-20 20:28:16', 'This query has been rejected because it describes an unrealistic and technically impossible scenario. A fan cannot spin faster when turned off or operate without being plugged in. Additionally, speed settings do not work in reverse. Please provide an accurate description of the issue for proper assistance.', '2024-12-20 09:28:16', '2025-03-21 15:34:18'),
(4, 13, 11, 1, 1, 1, 'My air conditioner was working fine, but now it\'s making my room hotter instead of cooling it. Even when I unplug it, I can still hear it running. I tried turning it off, but it keeps increasing the temperature. Please fix this issue immediately. ', 'Rejected', '2024-12-21 20:28:16', 'This query has been rejected because it describes an unrealistic scenario. An air conditioner cannot increase room temperature when turned off, nor can it continue running after being unplugged. Please provide a genuine issue with accurate details so we can assist you properly.', '2024-12-21 09:28:16', '2025-03-21 15:34:18'),
(5, 13, 6, 1, 1, 1, 'I am experiencing a problem with my fan. It is not turning on, and I have already checked the power connection and switch. The fan does not respond at all.', 'Closed', '2025-01-25 14:28:16', 'Your issue has been resolved, thanks for your patience.', '2025-01-20 09:28:16', '2025-01-20 09:28:16'),
(6, 13, 11, 1, 1, 2, 'I am facing an issue with my air conditioner. It is not cooling properly, and sometimes it does not turn on at all. I have checked the power supply and remote settings, but the problem persists. Please assist me in resolving this issue as soon as possible.', 'Closed', '2025-01-27 14:28:16', 'Your issue has been resolved, thanks for your patience.', '2025-01-21 09:28:16', '2025-01-21 09:28:16'),
(7, 13, 19, 1, 1, 2, 'I am experiencing a problem with my fan. It is not turning on, and I have already checked the power connection and switch. The fan does not respond at all.', 'Closed', '2025-02-25 14:28:16', 'Your issue has been resolved, thanks for your patience.', '2025-02-20 09:28:16', '2025-02-20 09:28:16'),
(8, 13, 2, 2, 1, 3, 'I am facing an issue with my air conditioner. It is not cooling properly, and sometimes it does not turn on at all. I have checked the power supply and remote settings, but the problem persists. Please assist me in resolving this issue as soon as possible.', 'Closed', '2025-02-27 14:28:16', 'Your issue has been resolved, thanks for your patience.', '2025-02-21 09:28:16', '2025-02-21 09:28:16'),
(9, 13, 6, 1, 1, 1, 'My fan is not working, but it keeps spinning faster every time I turn it off. When I unplug it, it starts working on its own. Also, the fan speed increases when I lower the setting and decreases when I increase it. Please look into this issue. ', 'Rejected', '2025-02-20 18:28:16', 'This query has been rejected because it describes an unrealistic and technically impossible scenario. A fan cannot spin faster when turned off or operate without being plugged in. Additionally, speed settings do not work in reverse. Please provide an accurate description of the issue for proper assistance.', '2025-02-20 09:28:16', '2025-02-20 09:28:16'),
(10, 13, 11, 1, 1, 1, 'My air conditioner was working fine, but now it\'s making my room hotter instead of cooling it. Even when I unplug it, I can still hear it running. I tried turning it off, but it keeps increasing the temperature. Please fix this issue immediately. ', 'Rejected', '2025-02-21 18:28:16', 'This query has been rejected because it describes an unrealistic scenario. An air conditioner cannot increase room temperature when turned off, nor can it continue running after being unplugged. Please provide a genuine issue with accurate details so we can assist you properly.', '2025-02-21 09:28:16', '2025-02-21 09:28:16'),
(11, 13, 6, 1, 1, 1, 'I am experiencing a problem with my fan. It is not turning on, and I have already checked the power connection and switch. The fan does not respond at all.', 'In Progress', '2025-03-30 14:28:16', NULL, '2025-03-20 09:28:16', '2025-03-20 09:28:16'),
(12, 13, 11, 1, 1, 2, 'I am facing an issue with my air conditioner. It is not cooling properly, and sometimes it does not turn on at all. I have checked the power supply and remote settings, but the problem persists. Please assist me in resolving this issue as soon as possible.', 'Open', NULL, NULL, '2025-03-21 09:28:16', '2025-03-20 09:28:16'),
(13, 14, 1, 1, 1, 1, 'I am experiencing a problem with my fan. It is not turning on, and I have already checked the power connection and switch. The fan does not respond at all.', 'Open', NULL, NULL, '2025-03-21 12:39:17', '2025-03-22 05:17:47');


INSERT INTO `querypictures` (`id`, `query_id`, `file_path`, `file_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'uploads/query/images/Query_1_1.jpeg', 'Query_1_1', '2025-03-21 12:01:26', '2025-03-21 12:01:26'),
(2, 1, 'uploads/query/images/Query_1_2.jpeg', 'Query_1_2', '2025-03-21 12:01:26', '2025-03-21 12:01:26'),
(3, 1, 'uploads/query/images/Query_1_3.jpeg', 'Query_1_3', '2025-03-21 12:01:26', '2025-03-21 12:01:26'),
(4, 2, 'uploads/query/images/Query_2_1.jpeg', 'Query_2_1', '2025-03-21 12:01:26', '2025-03-21 12:01:26'),
(5, 2, 'uploads/query/images/Query_2_2.jpeg', 'Query_2_2', '2025-03-21 12:01:26', '2025-03-21 12:01:26'),
(6, 2, 'uploads/query/images/Query_2_3.jpeg', 'Query_2_3', '2025-03-21 12:01:26', '2025-03-21 12:01:26'),
(7, 2, 'uploads/query/images/Query_2_4.jpeg', 'Query_2_4', '2025-03-21 12:01:26', '2025-03-21 12:01:26'),
(8, 3, 'uploads/query/images/Query_3_1.jpeg', 'Query_3_1', '2025-03-21 12:13:13', '2025-03-21 12:13:13'),
(9, 4, 'uploads/query/images/Query_3_1.jpeg', 'Query_3_1', '2025-03-21 12:13:13', '2025-03-21 12:13:13'),
(10, 11, 'uploads/query/images/Query_1_1.jpeg', 'Query_1_1', '2025-03-21 15:36:26', '2025-03-21 15:36:26'),
(11, 11, 'uploads/query/images/Query_1_2.jpeg', 'Query_1_2', '2025-03-21 15:36:26', '2025-03-21 15:36:26'),
(12, 11, 'uploads/query/images/Query_1_3.jpeg', 'Query_1_3', '2025-03-21 15:36:26', '2025-03-21 15:36:26'),
(13, 12, 'uploads/query/images/Query_2_1.jpeg', 'Query_2_1', '2025-03-21 15:36:26', '2025-03-21 15:36:26'),
(14, 12, 'uploads/query/images/Query_2_2.jpeg', 'Query_2_2', '2025-03-21 15:36:26', '2025-03-21 15:36:26'),
(15, 12, 'uploads/query/images/Query_2_3.jpeg', 'Query_2_3', '2025-03-21 15:36:26', '2025-03-21 15:36:26'),
(16, 12, 'uploads/query/images/Query_2_4.jpeg', 'Query_2_4', '2025-03-21 15:36:26', '2025-03-21 15:36:26'),
(17, 9, 'uploads/query/images/Query_3_1.jpeg', 'Query_3_1', '2025-03-21 15:36:26', '2025-03-21 15:36:26'),
(18, 10, 'uploads/query/images/Query_3_1.jpeg', 'Query_3_1', '2025-03-21 15:36:26', '2025-03-21 15:36:26');


INSERT INTO `dropdowntypes` (`id`, `type_name`, `description`, `parent_type_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Country', 'null', NULL, 1, '2025-02-28 15:34:53', '2025-02-28 15:34:53'),
(2, 'Province', 'null', 1, 1, '2025-02-28 15:34:53', '2025-02-28 15:34:53'),
(3, 'City', 'null', 2, 1, '2025-02-28 15:34:53', '2025-02-28 15:34:53'),
(4, 'Building-type', 'null', NULL, 1, '2025-02-28 15:34:53', '2025-02-28 15:34:53'),
(5, 'Building-document-type', 'null', NULL, 1, '2025-02-28 15:34:53', '2025-02-28 15:34:53'),
(6, 'Unit-type', 'null', NULL, 1, '2025-02-28 15:34:53', '2025-02-28 15:34:53');


INSERT INTO `dropdownvalues` (`id`, `value_name`, `description`, `dropdown_type_id`, `parent_value_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Pakistan', 'null', 1, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(2, 'Punjab', 'null', 2, 1, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(3, 'Lahore', 'null', 3, 2, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(4, 'Sindh', 'null', 2, 1, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(5, 'Karachi', 'null', 3, 4, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(6, 'Balochistan', 'null', 2, 1, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(7, 'Khyber Pakhtunkhwa', 'null', 2, 1, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(8, 'Capital', 'null', 2, 1, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(9, 'Multan', 'null', 3, 2, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(10, 'Faisalabad', 'null', 3, 2, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(11, 'Okara', 'null', 3, 2, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(12, 'Hyderabad', 'null', 3, 4, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(13, 'Sukkur', 'null', 3, 4, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(14, 'Quetta', 'null', 3, 6, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(15, 'Turbat', 'null', 3, 6, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(16, 'Abbottabad', 'null', 3, 7, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(17, 'Dera Ismail Khan', 'null', 3, 7, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(18, 'Peshawar', 'null', 3, 7, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(19, 'Islamabad', 'null', 3, 8, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(20, 'Residential', 'null', 4, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(21, 'Commercial', 'null', 4, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(22, 'Industrial', 'null', 4, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(23, 'Mixed-Use', 'null', 4, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(24, 'Building Permit', 'null', 5, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(25, 'Occupancy Certificate', 'null', 5, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(26, 'Completion Certificate', 'null', 5, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(27, 'Room', 'null', 6, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(28, 'Shop', 'null', 6, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(29, 'Apartment', 'null', 6, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(30, 'Restaurant', 'null', 6, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57'),
(31, 'Gym', 'null', 6, NULL, 1, '2025-02-28 15:35:57', '2025-02-28 15:35:57');


INSERT INTO `permissions` (`id`, `name`, `header`, `description`, `status`, `created_at`, `updated_at`, `parent_id`) VALUES
(1, 'View User Profile', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-22 06:27:19', NULL),
(2, 'Update User Profile', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-22 05:21:10', 1),
(3, 'Remove User Profile Picture', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-22 05:21:10', 1),
(4, 'Upload User Profile Picture', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-22 05:21:10', 1),
(5, 'User Homepage', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:54:17', NULL),
(6, 'Show Favorites', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(7, 'Add Favorites', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(8, 'Remove Favorites', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(9, 'Show My Properties', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(10, 'Log Queries', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(11, 'View User Queries', 'User Application', 'User Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(12, 'View Staff Profile', 'Staff Application', 'Staff Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(13, 'Update Staff Profile', 'Staff Application', 'Staff Application', 1, '2025-03-06 22:46:27', '2025-03-22 05:21:10', 12),
(14, 'Remove Staff Profile Picture', 'Staff Application', 'Staff Application', 1, '2025-03-06 22:46:27', '2025-03-22 05:21:10', 12),
(15, 'Upload Staff Profile Picture', 'Staff Application', 'Staff Application', 1, '2025-03-06 22:46:27', '2025-03-22 05:21:10', 12),
(16, 'View Staff Queries', 'Staff Application', 'Staff Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(17, 'Accept Queries', 'Staff Application', 'Staff Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL),
(18, 'Reject Queries', 'Staff Application', 'Staff Application', 1, '2025-03-06 22:46:27', '2025-03-06 22:46:27', NULL);
