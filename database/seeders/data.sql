INSERT INTO `address` (`id`, `location`, `country`, `province`, `city`, `postal_code`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 13:50:27', '2025-02-28 14:42:44'),
(2, 'Clifton Beach', 'Pakistan', 'Sindh', 'Karachi', '72000', NULL, NULL, '2025-02-28 13:50:27', '2025-02-28 14:43:48'),
(3, 'Quetta Cantt', 'Pakistan', 'Balochistan', 'Queta', '87800', NULL, NULL, '2025-02-28 13:50:27', '2025-02-28 13:50:27'),
(4, 'Daman-e-Koh', 'Pakistan', 'Punjab', 'Islamabad', '44050', NULL, NULL, '2025-02-28 13:50:27', '2025-02-28 13:50:27'),
(5, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:42:44'),
(6, 'Clifton Beach', 'Pakistan', 'Sindh', 'Karachi', '72000', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:43:48'),
(7, 'Quetta Cantt', 'Pakistan', 'Balochistan', 'Queta', '87800', NULL, NULL, '2025-02-28 14:16:35', '2025-03-01 10:39:26'),
(8, 'Daman-e-Koh', 'Pakistan', 'Punjab', 'Islamabad', '44050', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:16:35'),
(9, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:42:44'),
(10, 'Clifton Beach', 'Pakistan', 'Sindh', 'Karachi', '72000', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:43:48'),
(11, 'Quetta Cantt', 'Pakistan', 'Balochistan', 'Queta', '87800', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:16:35'),
(12, 'Daman-e-Koh', 'Pakistan', 'Punjab', 'Islamabad', '44050', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:16:35'),
(13, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:42:44'),
(14, 'Clifton Beach', 'Pakistan', 'Sindh', 'Karachi', '72000', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:43:48'),
(15, 'Quetta Cantt', 'Pakistan', 'Balochistan', 'Queta', '87800', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:16:35'),
(16, 'Daman-e-Koh', 'Pakistan', 'Punjab', 'Islamabad', '44050', NULL, NULL, '2025-02-28 14:16:35', '2025-02-28 14:16:35'),
(17, 'Gulberg', 'Pakistan', 'Punjab', 'Lahore', '54000', NULL, NULL, '2025-02-28 14:42:17', '2025-02-28 14:42:44');


INSERT INTO `roles` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Administrator with full access', 1, '2025-02-28 18:53:22', '2025-02-28 18:53:22'),
(2, 'Owner', 'NO Description', 1, '2025-02-28 18:53:22', '2025-02-28 18:53:22'),
(3, 'Staff', 'Building Operational Staff Members', 1, '2025-02-28 18:54:32', '2025-02-28 18:54:32'),
(4, 'User', 'Tanents and buyers.', 1, '2025-02-28 18:54:32', '2025-02-28 18:54:32');


INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone_no`, `cnic`, `date_of_birth`, `gender`, `picture`, `role_id`, `address_id`, `status`, `reset_token`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Rehan Manzoor', 'admin@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000001', '35202-0000000-1', '2024-12-01', 'Male', 'uploads/users/images/1738263413_User_1.jpg', 1, 6, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-02-28 14:44:21'),
(2, 'Usman Iqbal', 'owner1@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000002', '35202-0000000-2', '2019-07-03', 'Male', 'uploads/users/images/1738263414_User_2.jpg', 2, 7, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-03-01 10:38:33'),
(3, 'Abdullah Aasir', 'owner2@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000003', '35202-0000000-3', '2019-07-03', 'Male', 'uploads/users/images/1738263430_User_3.jpg', 2, 8, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-02-28 16:10:49'),
(4, 'Arham Nasir', 'fa21-bse-062@example.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000004', '35202-0000000-4', '2019-07-03', 'Male', 'uploads/users/images/1738263443_User_4.jpg', 3, 9, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-02-28 14:51:49'),
(5, 'Syed Ibrahim', 'smibrahim297@example.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000005', '35202-0000000-5', '2024-12-15', 'Male', 'uploads/users/images/1738263454_User_5.jpg', 3, 10, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-02-28 14:44:21'),
(6, 'Rehan Manzoor', 'rh7081789@example.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000006', '35202-0000000-6', '2024-12-16', 'Male', 'uploads/users/images/1739383093_User_6.jpg', 3, 11, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-03-01 10:37:36'),
(7, 'Rehan Manzoor', 'fa21-bse-002@cuilahore.edu.pk', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000007', '35202-0000000-7', '2024-12-01', 'Male', 'uploads/users/images/1740824784_User_7.jpg', 3, 12, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-03-01 10:37:36'),
(8, 'Usman Iqbal', 'fa21-bse-014@cuilahore.edu.pk', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000008', '35202-0000000-8', '2019-07-03', 'Male', 'uploads/users/images/1740824785_User_8.jpg', 3, 13, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-03-01 10:37:36'),
(9, 'Abdullah Aasir', 'fa21-bse-119@cuilahore.edu.pk', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000009', '35202-0000000-9', '2019-07-03', 'Male', 'uploads/users/images/1740824786_User_9.jpg', 4, 14, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-03-01 10:37:36'),
(10, 'Arham Nasir', 'Arham@example.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000010', '35202-0000001-0', '2019-07-03', 'Male', 'uploads/users/images/1740824787_User_10.jpg', 4, 15, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-03-01 10:37:36'),
(11, 'Syed Ibrahim', 'smibrahim297@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000011', '35202-0000001-1', '2024-12-15', 'Male', 'uploads/users/images/1740824788_User_11.jpg', 4, 16, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-03-01 10:37:36'),
(12, 'Rehan Manzoor', 'rh7081789@gmail.com', '$2y$12$yk.kQEOK1BYrrYEW1yG1Z.FmvVDAKFXMD.TWmsOsMy9Pmwh4HDcnS', '0300-0000012', '35202-0000001-2', '2024-12-16', 'Male', 'uploads/users/images/1740824789_User_12.jpg', 4, 17, 1, NULL, 1, 1, '2025-02-28 14:17:27', '2025-03-01 10:35:18');



INSERT INTO `organizations` (`id`, `name`, `address_id`, `status`, `membership_start_date`, `membership_end_date`, `owner_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Bahria Town', 1, 'Enable', '2025-03-01', '2025-03-01', 2, 1, 1, '2025-02-28 19:50:35', '2025-02-28 19:50:35'),
(2, 'Etihad Town', 2, 'Enable', '2025-05-31', '2025-05-31', 3, 1, 1, '2025-02-28 19:50:35', '2025-02-28 19:50:35');


INSERT INTO `organizationpictures` (`id`, `organization_id`, `file_path`, `file_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'uploads/organizations/images/1734415026_Organization_1.jpeg', '1734415026_Organization_1.jpeg', '2025-02-28 19:53:27', '2025-02-28 19:53:27'),
(2, 2, 'uploads/organizations/images/1734415045_Organization_2.png', '1734415045_Organization_2.png', '2025-02-28 19:53:27', '2025-02-28 19:53:27');


INSERT INTO `buildings` (`id`, `organization_id`, `name`, `building_type`, `status`, `remarks`, `area`, `construction_year`, `address_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bahria Prime', 'Residential', 'Approved', 'Approved building for office use.', 100000.00, '2001', 3, 1, 1, '2025-02-28 20:01:47', '2025-02-28 20:02:39'),
(2, 1, 'HMY Heights', 'Commercial', 'Approved', 'Under review for new office setup.', 100000.00, '2002', 4, 1, 1, '2025-02-28 20:01:47', '2025-02-28 20:02:39'),
(3, 2, 'Islamabad SKY Apartments', 'Commercial', 'Approved', 'Under review for new office setup.', 100000.00, '2003', 5, 1, 1, '2025-02-28 20:01:47', '2025-02-28 20:02:39');


INSERT INTO `buildingpictures` (`id`, `building_id`, `file_path`, `file_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'uploads/buildings/images/1734379577_Building_1.jpeg', '1734379577_Building_1.jpeg', '2025-02-28 20:05:35', '2025-02-28 20:05:35'),
(2, 2, 'uploads/buildings/images/1734379590_Building_2.jpeg', '1734379590_Building_2.jpeg', '2025-02-28 20:05:35', '2025-02-28 20:05:35'),
(3, 3, 'uploads/buildings/images/1734379603_Building_3.jpeg', '1734379603_Building_3.jpeg', '2025-02-28 20:05:35', '2025-02-28 20:05:35');


INSERT INTO `buildinglevels` (`id`, `building_id`, `level_name`, `description`, `level_number`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'First Floor', 'Office space for rent', 1, 'Approved', 1, 1, '2025-02-28 20:12:11', '2025-02-28 20:12:11'),
(2, 2, 'Second Floor', 'Meeting rooms and conference spaces', 2, 'Approved', 1, 1, '2025-02-28 20:12:11', '2025-02-28 20:12:11'),
(3, 3, 'Ground Floor', 'Office reception area', 1, 'Approved', 1, 1, '2025-02-28 20:12:11', '2025-02-28 20:12:11');


INSERT INTO `buildingunits` (`id`, `level_id`, `building_id`, `organization_id`, `unit_name`, `unit_type`, `availability_status`, `sale_or_rent`, `price`, `area`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Room 01', 'Room', 'Rented', 'Rent', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(2, 2, 2, 2, 'Shop 01', 'Shop', 'Available', 'Sale', 150000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(3, 3, 3, 2, 'Apartment 01', 'Apartment', 'Available', 'Rent', 35000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(4, 2, 2, 1, 'Room 02', 'Room', 'Sold', 'Sale', 100000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(5, 2, 2, 1, 'Shop 02', 'Shop', 'Available', 'Rent', 15000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(6, 1, 1, 1, 'Apartment 02', 'Apartment', 'Rented', 'Rent', 25000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(7, 2, 2, 1, 'Room 03', 'Room', 'Available', 'Rent', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(8, 3, 3, 2, 'Shop 03', 'Shop', 'Available', 'Sale', 135000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(9, 1, 1, 1, 'Apartment 03', 'Apartment', 'Rented', 'Rent', 20000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(10, 3, 3, 2, 'Room 04', 'Room', 'Available', 'Rent', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(11, 1, 1, 1, 'Shop 04', 'Shop', 'Rented', 'Rent', 150000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(12, 2, 2, 1, 'Apartment 04', 'Apartment', 'Available', 'Sale', 35000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(13, 1, 1, 1, 'Room 05', 'Room', 'Available', 'Rent', 100000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(14, 1, 1, 1, 'Shop 05', 'Shop', 'Rented', 'Rent', 15000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(15, 2, 2, 1, 'Apartment 05', 'Apartment', 'Available', 'Rent', 25000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(16, 2, 2, 1, 'Room 06', 'Room', 'Sold', 'Sale', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(17, 1, 1, 1, 'Shop 06', 'Shop', 'Available', 'Rent', 135000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(18, 2, 2, 1, 'Apartment 06', 'Apartment', 'Available', 'Sale', 20000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(19, 1, 1, 1, 'Room 07', 'Room', 'Sold', 'Sale', 10000.00, 100.00, 'A cozy room with large windows, neutral walls, hardwood floors, a queen-sized bed, a desk, and built-in closets. Perfect for relaxation and productivity.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(20, 3, 3, 2, 'Shop 07', 'Shop', 'Available', 'Sale', 150000.00, 100.00, 'Vintage Treasures offers handpicked antiques, unique collectibles, and retro décor in a charming, rustic setting.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46'),
(21, 3, 3, 2, 'Apartment 07', 'Apartment', 'Available', 'Sale', 35000.00, 100.00, 'A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.', 'Approved', 1, 1, '2025-02-28 20:19:46', '2025-02-28 20:19:46');


INSERT INTO `unitpictures` (`id`, `unit_id`, `file_path`, `file_name`, `created_at`, `updated_at`) VALUES
(1, 3, 'uploads/units/images/Apartment_1.jpeg', 'Apartment1', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(2, 3, 'uploads/units/images/Apartment_2.jpeg', 'Apartment2', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(3, 3, 'uploads/units/images/Apartment_3.jpeg', 'Apartment3', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(4, 6, 'uploads/units/images/Apartment_4.jpeg', 'Apartment4', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(5, 6, 'uploads/units/images/Apartment_5.jpeg', 'Apartment5', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(6, 6, 'uploads/units/images/Apartment_6.jpeg', 'Apartment6', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(7, 9, 'uploads/units/images/Apartment_7.jpeg', 'Apartment7', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(8, 9, 'uploads/units/images/Apartment_8.jpeg', 'Apartment8', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(9, 9, 'uploads/units/images/Apartment_9.jpeg', 'Apartment9', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(10, 12, 'uploads/units/images/Apartment_10.jpeg', 'Apartment10', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(11, 12, 'uploads/units/images/Apartment_11.jpeg', 'Apartment11', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(12, 12, 'uploads/units/images/Apartment_12.jpeg', 'Apartment12', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(13, 15, 'uploads/units/images/Apartment_13.jpeg', 'Apartment13', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(14, 15, 'uploads/units/images/Apartment_14.jpeg', 'Apartment14', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(15, 15, 'uploads/units/images/Apartment_15.jpeg', 'Apartment15', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(16, 18, 'uploads/units/images/Apartment_16.jpeg', 'Apartment16', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(17, 18, 'uploads/units/images/Apartment_17.jpeg', 'Apartment17', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(18, 18, 'uploads/units/images/Apartment_18.jpeg', 'Apartment18', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(19, 21, 'uploads/units/images/Apartment_19.jpeg', 'Apartment19', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(20, 21, 'uploads/units/images/Apartment_20.jpeg', 'Apartment20', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(21, 21, 'uploads/units/images/Apartment_21.jpeg', 'Apartment21', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(22, 2, 'uploads/units/images/Shop_1.jpeg', 'Shop1', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(23, 5, 'uploads/units/images/Shop_2.jpeg', 'Shop2', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(24, 8, 'uploads/units/images/Shop_3.jpeg', 'Shop3', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(25, 11, 'uploads/units/images/Shop_4.jpeg', 'Shop4', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(26, 14, 'uploads/units/images/Shop_5.jpeg', 'Shop5', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(27, 17, 'uploads/units/images/Shop_6.jpeg', 'Shop6', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(28, 20, 'uploads/units/images/Shop_7.jpeg', 'Shop7', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(29, 1, 'uploads/units/images/Room_1.jpeg', 'Room1', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(30, 4, 'uploads/units/images/Room_2.jpeg', 'Room2', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(31, 7, 'uploads/units/images/Room_3.jpeg', 'Room3', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(32, 10, 'uploads/units/images/Room_4.jpeg', 'Room4', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(33, 13, 'uploads/units/images/Room_5.jpeg', 'Room5', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(34, 16, 'uploads/units/images/Room_6.jpeg', 'Room6', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(35, 19, 'uploads/units/images/Room_7.jpeg', 'Room7', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(36, 1, 'uploads/units/images/Room_8.jpeg', 'Room8', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(37, 4, 'uploads/units/images/Room_9.jpeg', 'Room9', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(38, 7, 'uploads/units/images/Room_10.jpeg', 'Room10', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(39, 10, 'uploads/units/images/Room_11.jpeg', 'Room11', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(40, 13, 'uploads/units/images/Room_12.jpeg', 'Room12', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(41, 16, 'uploads/units/images/Room_13.jpeg', 'Room13', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(42, 19, 'uploads/units/images/Room_14.jpeg', 'Room14', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(43, 1, 'uploads/units/images/Room_15.jpeg', 'Room15', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(44, 4, 'uploads/units/images/Room_16.jpeg', 'Room16', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(45, 7, 'uploads/units/images/Room_17.jpeg', 'Room17', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(46, 10, 'uploads/units/images/Room_18.jpeg', 'Room18', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(47, 13, 'uploads/units/images/Room_19.jpeg', 'Room19', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(48, 16, 'uploads/units/images/Room_20.jpeg', 'Room20', '2025-02-28 20:29:12', '2025-02-28 20:29:12'),
(49, 19, 'uploads/units/images/Room_21.jpeg', 'Room21', '2025-02-28 20:29:12', '2025-02-28 20:29:12');


INSERT INTO `userbuildingunits` (`id`, `user_id`, `unit_id`, `rent_start_date`, `rent_end_date`, `purchase_date`, `contract_status`, `type`, `price`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 10, 1, '2023-01-01', '2023-12-31', NULL, 1, 'Rented', 25000.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:53:52'),
(2, 9, 2, '2023-06-01', '2023-12-31', NULL, 1, 'Rented', 20000.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:52:41'),
(3, 12, 4, NULL, NULL, '2024-12-15', 1, 'Sold', 100000.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:52:41'),
(4, 12, 9, '2023-01-01', '2023-12-31', NULL, 1, 'Rented', 10000.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:52:41'),
(5, 12, 14, '2023-06-01', '2023-12-31', NULL, 1, 'Rented', 10000.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:52:41'),
(6, 9, 19, NULL, NULL, '2024-12-15', 1, 'Sold', 500000.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:52:41'),
(7, 9, 6, '2023-01-01', '2023-12-31', NULL, 1, 'Rented', 15000.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:52:41'),
(8, 9, 11, '2023-06-01', '2023-12-31', NULL, 1, 'Rented', 25000.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:52:41'),
(9, 10, 3, '2025-03-01', '2025-09-01', NULL, 1, 'Rented', 1500.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:53:52'),
(10, 10, 4, '2025-03-01', '2025-09-01', NULL, 1, 'Rented', 1500.00, 1, 1, '2025-02-28 20:52:41', '2025-02-28 20:53:52');



INSERT INTO `departments` (`id`, `name`, `description`, `organization_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Electric', 'null', 1, 1, 1, '2025-02-28 20:58:33', '2025-02-28 20:58:33'),
(2, 'Water', 'null', 1, 1, 1, '2025-02-28 20:58:33', '2025-02-28 20:58:33'),
(3, 'Management', 'null', 1, 1, 1, '2025-02-28 20:58:33', '2025-02-28 20:58:33'),
(4, 'Electric', 'null', 2, 1, 1, '2025-02-28 20:58:33', '2025-02-28 20:58:33'),
(5, 'Water', 'null', 2, 1, 1, '2025-02-28 20:58:33', '2025-02-28 20:58:33'),
(6, 'Management', 'null', 2, 1, 1, '2025-02-28 20:58:33', '2025-02-28 20:58:33');


INSERT INTO `staffmembers` (`id`, `user_id`, `department_id`, `building_id`, `organization_id`, `salary`, `active_load`, `accept_queries`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 1, 1, 0.0, 0, 1, 1, 1, 1, '2025-02-28 21:02:40', '2025-02-28 21:02:40'),
(2, 5, 1, 1, 1, 0.0, 0, 1, 1, 1, 1, '2025-02-28 21:02:40', '2025-02-28 21:02:40'),
(3, 6, 1, 1, 1, 0.0, 0, 1, 1, 1, 1, '2025-02-28 21:02:40', '2025-02-28 21:02:40'),
(4, 7, 4, 3, 2, 0.0, 0, 1, 1, 1, 1, '2025-02-28 21:02:40', '2025-02-28 21:02:40'),
(5, 8, 4, 3, 2, 0.0, 0, 1, 1, 1, 1, '2025-02-28 21:02:40', '2025-02-28 21:03:20');


INSERT INTO `dropdowntypes` (`id`, `type_name`, `description`, `parent_type_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Country', 'null', NULL, 1, '2025-02-28 20:34:53', '2025-02-28 20:34:53'),
(2, 'Province', 'null', 1, 1, '2025-02-28 20:34:53', '2025-02-28 20:34:53'),
(3, 'City', 'null', 2, 1, '2025-02-28 20:34:53', '2025-02-28 20:34:53'),
(4, 'Building-type', 'null', NULL, 1, '2025-02-28 20:34:53', '2025-02-28 20:34:53'),
(5, 'Building-document-type', 'null', NULL, 1, '2025-02-28 20:34:53', '2025-02-28 20:34:53'),
(6, 'Unit-type', 'null', NULL, 1, '2025-02-28 20:34:53', '2025-02-28 20:34:53');


INSERT INTO `dropdownvalues` (`id`, `value_name`, `description`, `dropdown_type_id`, `parent_value_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Pakistan', 'null', 1, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(2, 'Punjab', 'null', 2, 1, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(3, 'Lahore', 'null', 3, 2, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(4, 'Sindh', 'null', 2, 1, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(5, 'Karachi', 'null', 3, 4, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(6, 'Balochistan', 'null', 2, 1, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(7, 'Khyber Pakhtunkhwa', 'null', 2, 1, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(8, 'Capital', 'null', 2, 1, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(9, 'Multan', 'null', 3, 2, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(10, 'Faisalabad', 'null', 3, 2, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(11, 'Okara', 'null', 3, 2, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(12, 'Hyderabad', 'null', 3, 4, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(13, 'Sukkur', 'null', 3, 4, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(14, 'Quetta', 'null', 3, 6, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(15, 'Turbat', 'null', 3, 6, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(16, 'Abbottabad', 'null', 3, 7, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(17, 'Dera Ismail Khan', 'null', 3, 7, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(18, 'Peshawar', 'null', 3, 7, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(19, 'Islamabad', 'null', 3, 8, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(20, 'Residential', 'null', 4, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(21, 'Commercial', 'null', 4, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(22, 'Industrial', 'null', 4, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(23, 'Mixed-Use', 'null', 4, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(24, 'Building Permit', 'null', 5, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(25, 'Occupancy Certificate', 'null', 5, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(26, 'Completion Certificate', 'null', 5, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(27, 'Room', 'null', 6, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(28, 'Shop', 'null', 6, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(29, 'Apartment', 'null', 6, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(30, 'Restaurant', 'null', 6, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57'),
(31, 'Gym', 'null', 6, NULL, 1, '2025-02-28 20:35:57', '2025-02-28 20:35:57');
