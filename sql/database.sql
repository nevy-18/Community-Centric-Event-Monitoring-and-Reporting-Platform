-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ytr_event_monitoring
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` bigint(20) unsigned NOT NULL,
  `target_audience` enum('everyone','national_admins','chapter_admins','specific_chapter') NOT NULL DEFAULT 'everyone',
  `target_chapter_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `body` text NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_announcements_published` (`is_published`,`published_at`),
  KEY `idx_announcements_chapter` (`target_chapter_id`),
  KEY `fk_announcements_creator` (`created_by`),
  CONSTRAINT `fk_announcements_chapter` FOREIGN KEY (`target_chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_announcements_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;

/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_user_date` (`user_id`,`created_at`),
  KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;

/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chapters`
--

DROP TABLE IF EXISTS `chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chapters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_code` varchar(30) NOT NULL,
  `chapter_name` varchar(150) NOT NULL,
  `generation_name` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city_municipality` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `regional_director_id` bigint(20) unsigned DEFAULT NULL,
  `regional_director_name` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_chapters_code` (`chapter_code`),
  KEY `idx_chapters_name` (`chapter_name`),
  KEY `idx_chapters_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapters`
--

LOCK TABLES `chapters` WRITE;
/*!40000 ALTER TABLE `chapters` DISABLE KEYS */;
INSERT INTO `chapters` VALUES (1,'YTR-LAG','Laguna Chapter','Gen One','Region IV-A','Laguna','Los Ba├▒os',NULL,'active','2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,NULL),(2,'YTR-NCR','NCR Chapter','Gen Two','NCR','Metro Manila','Quezon City',NULL,'active','2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,NULL),(3,'YTR-CAV','Cavite Chapter','Gen One','Region IV-A','Cavite','Tagaytay',NULL,'active','2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,NULL),(4,'YTR-PAM','Pampanga Chapter','Gen Three','Region III','Pampanga','San Fernando',NULL,'active','2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,NULL),(5,'YTR-BAT','Batangas Chapter','Gen Two','Region IV-A','Batangas','Batangas City',NULL,'active','2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,NULL),(6,'YTR-UMAK','University of Makati Chapter','Gen Two','NCR','Metro Manila','Makati City',NULL,'active','2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,NULL);
/*!40000 ALTER TABLE `chapters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_report_files`
--

DROP TABLE IF EXISTS `event_report_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_report_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_report_id` bigint(20) unsigned NOT NULL,
  `uploaded_by` bigint(20) unsigned DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_report_files_report` (`event_report_id`),
  KEY `fk_report_files_uploader` (`uploaded_by`),
  CONSTRAINT `fk_report_files_report` FOREIGN KEY (`event_report_id`) REFERENCES `event_reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_report_files_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_report_files`
--

LOCK TABLES `event_report_files` WRITE;
/*!40000 ALTER TABLE `event_report_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_report_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_reports`
--

DROP TABLE IF EXISTS `event_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `chapter_id` bigint(20) unsigned NOT NULL,
  `submitted_by` bigint(20) unsigned NOT NULL,
  `total_participants` int(10) unsigned NOT NULL DEFAULT 0,
  `first_timers` int(10) unsigned NOT NULL DEFAULT 0,
  `existing_members` int(10) unsigned NOT NULL DEFAULT 0,
  `report_summary` text DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `challenges` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `status` enum('draft','submitted','under_review','approved','revision_requested','rejected') NOT NULL DEFAULT 'draft',
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `youth_leaders_count` int(10) unsigned DEFAULT 0,
  `attendee_roster` mediumtext DEFAULT NULL,
  `attendance_sheet_path` varchar(255) DEFAULT NULL,
  `photos_drive_link` varchar(1000) DEFAULT NULL,
  `e_signature` longtext DEFAULT NULL,
  `signed_by_name` varchar(150) DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `budget_spent` decimal(12,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_reports_event` (`event_id`),
  KEY `idx_reports_chapter_status` (`chapter_id`,`status`),
  KEY `idx_reports_submitted_at` (`submitted_at`),
  KEY `fk_reports_submitter` (`submitted_by`),
  KEY `fk_reports_reviewer` (`reviewed_by`),
  CONSTRAINT `fk_reports_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reports_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reports_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_reports_submitter` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_reports`
--

LOCK TABLES `event_reports` WRITE;
/*!40000 ALTER TABLE `event_reports` DISABLE KEYS */;

/*!40000 ALTER TABLE `event_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `event_type` varchar(100) NOT NULL DEFAULT 'chapter',
  `ytr_program` varchar(100) NOT NULL DEFAULT 'Lead up (Youth Leadership)',
  `target_audience` varchar(150) NOT NULL DEFAULT 'Open for All',
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `status` enum('draft','pending_approval','approved','published','ongoing','completed','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned NOT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `target_turnout` int(11) DEFAULT 100,
  `budget_amount` decimal(12,2) DEFAULT 0.00,
  `budget_threshold` decimal(12,2) DEFAULT 25000.00,
  `due_date` date DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `drive_link` varchar(1000) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_events_chapter` (`chapter_id`),
  KEY `idx_events_start` (`start_datetime`),
  KEY `idx_events_status` (`status`),
  KEY `fk_events_creator` (`created_by`),
  KEY `fk_events_reviewer` (`reviewed_by`),
  CONSTRAINT `fk_events_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_events_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_events_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;

/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `related_type` enum('poster_request','event_report','event','announcement','account') DEFAULT NULL,
  `related_id` bigint(20) unsigned DEFAULT NULL,
  `target_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user_read_date` (`user_id`,`is_read`,`created_at`),
  KEY `fk_notifications_creator` (`created_by`),
  CONSTRAINT `fk_notifications_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;

/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `email` varchar(190) NOT NULL,
  `status` enum('pending','resolved','cancelled') DEFAULT 'pending',
  `requested_at` datetime DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `resolved_by` bigint(20) unsigned DEFAULT NULL,
  `temporary_password` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;

/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poster_request_steps`
--

DROP TABLE IF EXISTS `poster_request_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poster_request_steps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poster_request_id` bigint(20) unsigned NOT NULL,
  `step_key` varchar(50) NOT NULL,
  `step_order` tinyint(3) unsigned NOT NULL,
  `step_title` varchar(150) NOT NULL,
  `status` enum('pending','in_progress','completed','revision_needed','skipped') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `proof_file_path` varchar(500) DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_request_step` (`poster_request_id`,`step_key`),
  UNIQUE KEY `uq_request_step_order` (`poster_request_id`,`step_order`),
  KEY `idx_steps_status` (`status`),
  KEY `fk_steps_updater` (`updated_by`),
  CONSTRAINT `fk_steps_request` FOREIGN KEY (`poster_request_id`) REFERENCES `poster_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_steps_updater` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poster_request_steps`
--

LOCK TABLES `poster_request_steps` WRITE;
/*!40000 ALTER TABLE `poster_request_steps` DISABLE KEYS */;

/*!40000 ALTER TABLE `poster_request_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poster_request_updates`
--

DROP TABLE IF EXISTS `poster_request_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poster_request_updates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poster_request_id` bigint(20) unsigned NOT NULL,
  `step_id` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `update_type` enum('request_submitted','status_changed','step_updated','comment','proof_uploaded','revision_requested') NOT NULL,
  `message` text DEFAULT NULL,
  `proof_file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_poster_updates_request_date` (`poster_request_id`,`created_at`),
  KEY `fk_poster_updates_step` (`step_id`),
  KEY `fk_poster_updates_user` (`updated_by`),
  CONSTRAINT `fk_poster_updates_request` FOREIGN KEY (`poster_request_id`) REFERENCES `poster_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_poster_updates_step` FOREIGN KEY (`step_id`) REFERENCES `poster_request_steps` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_poster_updates_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poster_request_updates`
--

LOCK TABLES `poster_request_updates` WRITE;
/*!40000 ALTER TABLE `poster_request_updates` DISABLE KEYS */;

/*!40000 ALTER TABLE `poster_request_updates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poster_requests`
--

DROP TABLE IF EXISTS `poster_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poster_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `requested_by` bigint(20) unsigned NOT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `event_title` varchar(200) NOT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` varchar(80) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `theme_or_concept` varchar(255) DEFAULT NULL,
  `required_text` text DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `requested_deadline` date DEFAULT NULL,
  `status` enum('submitted','under_review','in_progress','revision_requested','completed','rejected') NOT NULL DEFAULT 'submitted',
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `target_participants` varchar(100) DEFAULT NULL,
  `target_audience` varchar(150) NOT NULL DEFAULT 'Open for All',
  `registration_link` varchar(500) DEFAULT NULL,
  `reference_poster_path` varchar(500) DEFAULT NULL,
  `final_poster_path` varchar(500) DEFAULT NULL,
  `checklist_verified` text DEFAULT NULL,
  `current_step` tinyint(4) DEFAULT 1,
  `is_event_conducted` tinyint(1) DEFAULT 0,
  `event_conducted_at` datetime DEFAULT NULL,
  `report_due_date` date DEFAULT NULL,
  `documentation_proof_path` varchar(500) DEFAULT NULL,
  `report_summary` text DEFAULT NULL,
  `attendees_count` int(11) DEFAULT NULL,
  `first_timers_count` int(11) DEFAULT NULL,
  `documentation_submitted_at` datetime DEFAULT NULL,
  `documentation_reviewed_at` datetime DEFAULT NULL,
  `documentation_status` varchar(50) DEFAULT 'not_submitted',
  PRIMARY KEY (`id`),
  KEY `idx_poster_chapter_status` (`chapter_id`,`status`),
  KEY `idx_poster_event` (`event_id`),
  KEY `idx_poster_assigned_to` (`assigned_to`),
  KEY `idx_poster_submitted_at` (`submitted_at`),
  KEY `fk_poster_requester` (`requested_by`),
  KEY `fk_poster_reviewer` (`reviewed_by`),
  CONSTRAINT `fk_poster_assignee` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_poster_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_poster_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_poster_requester` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_poster_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poster_requests`
--

LOCK TABLES `poster_requests` WRITE;
/*!40000 ALTER TABLE `poster_requests` DISABLE KEYS */;

/*!40000 ALTER TABLE `poster_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poster_workflow_templates`
--

DROP TABLE IF EXISTS `poster_workflow_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poster_workflow_templates` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `step_key` varchar(50) NOT NULL,
  `step_order` tinyint(3) unsigned NOT NULL,
  `step_title` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_workflow_step_key` (`step_key`),
  UNIQUE KEY `uq_workflow_step_order` (`step_order`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poster_workflow_templates`
--

LOCK TABLES `poster_workflow_templates` WRITE;
/*!40000 ALTER TABLE `poster_workflow_templates` DISABLE KEYS */;
INSERT INTO `poster_workflow_templates` VALUES (1,'poster_created',1,'Poster Request & Design','National prepares and provides the official event poster.',1),(2,'posted_on_page',2,'Posted on Page','Official poster and caption are published on the social media page.',1),(3,'promotional_video',3,'Promotional Video','Chapter / Media team promotional teaser video submission (Optional).',1),(4,'documentation_and_report',4,'Documentation and Report','Post-event documentation, attendees figures, and report submission via Report tab.',1),(5,'event_concluded',5,'Event Concluded and Reported','National Admin validates event report and concludes timeline.',1);
/*!40000 ALTER TABLE `poster_workflow_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` bigint(20) unsigned DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('national_admin','chapter_admin') NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `account_status` enum('pending','active','rejected','suspended') NOT NULL DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `signature_image` longtext DEFAULT NULL,
  `password_reset_pending` tinyint(1) DEFAULT 0,
  `password_reset_requested_at` datetime DEFAULT NULL,
  `temporary_password` varchar(100) DEFAULT NULL,
  `password_reset_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_chapter` (`chapter_id`),
  KEY `idx_users_role_status` (`role`,`account_status`),
  KEY `fk_users_reviewer` (`reviewed_by`),
  CONSTRAINT `fk_users_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'Hamilton Jones Dela','admin@ytr.org','$2b$10$9Pdhay/2E6xQFzazF8pCpu6tjVmARwTnyCR1wmH24GHhj960tc7cK','national_admin','Executive Director','active','2026-10-20 02:05:30',NULL,NULL,NULL,NULL,'2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,0,NULL,NULL,NULL),(2,1,'Joan Dela Cruz','joan.dela@gmail.com','$2b$10$/cIVMSZ/Blq0WlarsTs5y.k51bhtAzAczOvFojcj8cx3EBsrjOGiK','chapter_admin','Chapter Coordinator','active','2026-10-20 02:05:30',NULL,NULL,NULL,'2026-10-20 10:05:32','2026-10-20 02:05:30','2026-10-20 02:05:32',NULL,0,'2026-10-20 10:05:31','Password123!','2026-10-20 10:05:32'),(3,2,'Mark Bautista','mark.bautista@gmail.com','$2b$10$9Pdhay/2E6xQFzazF8pCpu6tjVmARwTnyCR1wmH24GHhj960tc7cK','chapter_admin','Chapter President','pending','2026-10-20 02:05:30',NULL,NULL,NULL,NULL,'2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,0,NULL,NULL,NULL),(7,3,'Elena Reyes','elena.reyes@gmail.com','$2b$10$9Pdhay/2E6xQFzazF8pCpu6tjVmARwTnyCR1wmH24GHhj960tc7cK','chapter_admin','Regional Director (Chapter)','pending','2026-10-20 02:05:30',NULL,NULL,NULL,NULL,'2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,0,NULL,NULL,NULL),(8,4,'Carlos Santos','carlos.santos@gmail.com','$2b$10$9Pdhay/2E6xQFzazF8pCpu6tjVmARwTnyCR1wmH24GHhj960tc7cK','chapter_admin','Chapter Coordinator','rejected','2026-10-20 02:05:30',NULL,NULL,NULL,NULL,'2026-10-20 02:05:30','2026-10-20 02:05:30',NULL,0,NULL,NULL,NULL),(12,1,'rayvhen P Munoz','munozrayvhen@gmail.com','$2b$10$rH73fMU4UaLVzStVKgCJsuJiDAwrioEL3PVx/sdKVOcQ1zWRGFWEG','chapter_admin','Chapter Coordinator','active','2026-10-20 02:05:30',NULL,NULL,NULL,'2026-10-20 10:41:29','2026-10-20 02:05:30','2026-10-20 02:41:29',NULL,0,'2026-10-20 10:39:51','rayvhen1438','2026-10-20 10:41:10'),(13,NULL,'Admin Host','adminhost@gmail.com','$2b$10$9Pdhay/2E6xQFzazF8pCpu6tjVmARwTnyCR1wmH24GHhj960tc7cK','national_admin','Regional Director (National)','active','2026-10-20 02:05:30',NULL,NULL,NULL,'2026-10-20 10:40:02','2026-10-20 02:05:30','2026-10-20 02:40:02',NULL,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `vw_event_report_summary`
--

DROP TABLE IF EXISTS `vw_event_report_summary`;
/*!50001 DROP VIEW IF EXISTS `vw_event_report_summary`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_event_report_summary` AS SELECT
 1 AS `event_report_id`,
  1 AS `event_id`,
  1 AS `event_title`,
  1 AS `chapter_id`,
  1 AS `chapter_name`,
  1 AS `total_participants`,
  1 AS `first_timers`,
  1 AS `existing_members`,
  1 AS `status`,
  1 AS `submitted_at` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_poster_request_progress`
--

DROP TABLE IF EXISTS `vw_poster_request_progress`;
/*!50001 DROP VIEW IF EXISTS `vw_poster_request_progress`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_poster_request_progress` AS SELECT
 1 AS `poster_request_id`,
  1 AS `chapter_id`,
  1 AS `chapter_name`,
  1 AS `event_title`,
  1 AS `request_status`,
  1 AS `submitted_at`,
  1 AS `total_steps`,
  1 AS `completed_steps`,
  1 AS `progress_percent` */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vw_event_report_summary`
--

/*!50001 DROP VIEW IF EXISTS `vw_event_report_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_event_report_summary` AS select `er`.`id` AS `event_report_id`,`er`.`event_id` AS `event_id`,`e`.`title` AS `event_title`,`er`.`chapter_id` AS `chapter_id`,`c`.`chapter_name` AS `chapter_name`,`er`.`total_participants` AS `total_participants`,`er`.`first_timers` AS `first_timers`,`er`.`existing_members` AS `existing_members`,`er`.`status` AS `status`,`er`.`submitted_at` AS `submitted_at` from ((`event_reports` `er` join `events` `e` on(`e`.`id` = `er`.`event_id`)) join `chapters` `c` on(`c`.`id` = `er`.`chapter_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_poster_request_progress`
--

/*!50001 DROP VIEW IF EXISTS `vw_poster_request_progress`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_poster_request_progress` AS select `pr`.`id` AS `poster_request_id`,`pr`.`chapter_id` AS `chapter_id`,`c`.`chapter_name` AS `chapter_name`,`pr`.`event_title` AS `event_title`,`pr`.`status` AS `request_status`,`pr`.`submitted_at` AS `submitted_at`,count(`prs`.`id`) AS `total_steps`,sum(case when `prs`.`status` = 'completed' then 1 else 0 end) AS `completed_steps`,case when count(`prs`.`id`) = 0 then 0 else round(100 * sum(case when `prs`.`status` = 'completed' then 1 else 0 end) / count(`prs`.`id`),0) end AS `progress_percent` from ((`poster_requests` `pr` join `chapters` `c` on(`c`.`id` = `pr`.`chapter_id`)) left join `poster_request_steps` `prs` on(`prs`.`poster_request_id` = `pr`.`id`)) group by `pr`.`id`,`pr`.`chapter_id`,`c`.`chapter_name`,`pr`.`event_title`,`pr`.`status`,`pr`.`submitted_at` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-20 11:33:20
