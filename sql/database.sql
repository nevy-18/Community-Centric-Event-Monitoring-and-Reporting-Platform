-- Youth on the Rock (YTR)
-- Community-Centric Event Monitoring and Reporting Platform
-- MySQL / MariaDB schema for phpMyAdmin

CREATE DATABASE IF NOT EXISTS ytr_event_monitoring
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ytr_event_monitoring;

-- 1. Partner chapters
CREATE TABLE IF NOT EXISTS chapters (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  chapter_code VARCHAR(30) NOT NULL,
  chapter_name VARCHAR(150) NOT NULL,
  generation_name VARCHAR(100) NULL,
  region VARCHAR(100) NULL,
  province VARCHAR(100) NULL,
  city_municipality VARCHAR(100) NULL,
  address VARCHAR(255) NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_chapters_code (chapter_code),
  KEY idx_chapters_name (chapter_name),
  KEY idx_chapters_status (status)
) ENGINE=InnoDB;

-- 2. User accounts and Manage Account approvals.
-- A newly requested account is saved with account_status = 'pending'.
-- Store a secure password hash here, never a plain-text password.
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  chapter_id BIGINT UNSIGNED NULL,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('national_admin','chapter_admin') NOT NULL,
  position VARCHAR(100) NULL,
  account_status ENUM('pending','active','rejected','suspended') NOT NULL DEFAULT 'pending',
  requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reviewed_by BIGINT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  review_note TEXT NULL,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  KEY idx_users_chapter (chapter_id),
  KEY idx_users_role_status (role, account_status),
  CONSTRAINT fk_users_chapter
    FOREIGN KEY (chapter_id) REFERENCES chapters(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_users_reviewer
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. Events
CREATE TABLE IF NOT EXISTS events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  chapter_id BIGINT UNSIGNED NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  event_type ENUM('national','chapter','community','other') NOT NULL DEFAULT 'chapter',
  start_datetime DATETIME NULL,
  end_datetime DATETIME NULL,
  venue VARCHAR(255) NULL,
  status ENUM('draft','pending_approval','approved','published','ongoing','completed','cancelled') NOT NULL DEFAULT 'draft',
  created_by BIGINT UNSIGNED NOT NULL,
  reviewed_by BIGINT UNSIGNED NULL,
  review_note TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_events_chapter (chapter_id),
  KEY idx_events_start (start_datetime),
  KEY idx_events_status (status),
  CONSTRAINT fk_events_chapter
    FOREIGN KEY (chapter_id) REFERENCES chapters(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_events_creator
    FOREIGN KEY (created_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_events_reviewer
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Poster requests submitted by a chapter to National.
CREATE TABLE IF NOT EXISTS poster_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  chapter_id BIGINT UNSIGNED NOT NULL,
  event_id BIGINT UNSIGNED NULL,
  requested_by BIGINT UNSIGNED NOT NULL,
  assigned_to BIGINT UNSIGNED NULL,
  event_title VARCHAR(200) NOT NULL,
  event_date DATE NULL,
  event_time VARCHAR(80) NULL,
  venue VARCHAR(255) NULL,
  theme_or_concept VARCHAR(255) NULL,
  required_text TEXT NULL,
  caption TEXT NULL,
  additional_notes TEXT NULL,
  requested_deadline DATE NULL,
  status ENUM('submitted','under_review','in_progress','revision_requested','completed','rejected') NOT NULL DEFAULT 'submitted',
  reviewed_by BIGINT UNSIGNED NULL,
  review_note TEXT NULL,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_poster_chapter_status (chapter_id, status),
  KEY idx_poster_event (event_id),
  KEY idx_poster_assigned_to (assigned_to),
  KEY idx_poster_submitted_at (submitted_at),
  CONSTRAINT fk_poster_chapter
    FOREIGN KEY (chapter_id) REFERENCES chapters(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_poster_event
    FOREIGN KEY (event_id) REFERENCES events(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_poster_requester
    FOREIGN KEY (requested_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_poster_assignee
    FOREIGN KEY (assigned_to) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_poster_reviewer
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. The six default steps shown in the poster progress timeline.
CREATE TABLE IF NOT EXISTS poster_workflow_templates (
  id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  step_key VARCHAR(50) NOT NULL,
  step_order TINYINT UNSIGNED NOT NULL,
  step_title VARCHAR(150) NOT NULL,
  description VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_workflow_step_key (step_key),
  UNIQUE KEY uq_workflow_step_order (step_order)
) ENGINE=InnoDB;

INSERT INTO poster_workflow_templates (step_key, step_order, step_title, description, is_active)
VALUES
  ('poster_created', 1, 'Poster created', 'National prepares the event poster.', 1),
  ('waiting_for_caption', 2, 'Waiting for caption', 'The event caption is requested or being finalized.', 1),
  ('posted_on_page', 3, 'Posted on page', 'The event poster has been published on the page.', 1),
  ('promotional_video', 4, 'Promotional video', 'Promotional video preparation or publication.', 1),
  ('documentation_and_report', 5, 'Documentation and report', 'Event documentation and report are being prepared.', 1),
  ('event_conducted_and_reported', 6, 'Event conducted and reported', 'The event has taken place and its report has been submitted.', 1)
ON DUPLICATE KEY UPDATE
  step_title = VALUES(step_title),
  description = VALUES(description),
  is_active = VALUES(is_active);

-- 6. Current status of each poster timeline step.
CREATE TABLE IF NOT EXISTS poster_request_steps (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  poster_request_id BIGINT UNSIGNED NOT NULL,
  step_key VARCHAR(50) NOT NULL,
  step_order TINYINT UNSIGNED NOT NULL,
  step_title VARCHAR(150) NOT NULL,
  status ENUM('pending','in_progress','completed','revision_needed','skipped') NOT NULL DEFAULT 'pending',
  notes TEXT NULL,
  proof_file_path VARCHAR(500) NULL,
  updated_by BIGINT UNSIGNED NULL,
  completed_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_request_step (poster_request_id, step_key),
  UNIQUE KEY uq_request_step_order (poster_request_id, step_order),
  KEY idx_steps_status (status),
  CONSTRAINT fk_steps_request
    FOREIGN KEY (poster_request_id) REFERENCES poster_requests(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_steps_updater
    FOREIGN KEY (updated_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. Activity/timeline history for a poster request (comments, status changes, proofs).
CREATE TABLE IF NOT EXISTS poster_request_updates (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  poster_request_id BIGINT UNSIGNED NOT NULL,
  step_id BIGINT UNSIGNED NULL,
  updated_by BIGINT UNSIGNED NULL,
  update_type ENUM('request_submitted','status_changed','step_updated','comment','proof_uploaded','revision_requested') NOT NULL,
  message TEXT NULL,
  proof_file_path VARCHAR(500) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_poster_updates_request_date (poster_request_id, created_at),
  CONSTRAINT fk_poster_updates_request
    FOREIGN KEY (poster_request_id) REFERENCES poster_requests(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_poster_updates_step
    FOREIGN KEY (step_id) REFERENCES poster_request_steps(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_poster_updates_user
    FOREIGN KEY (updated_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 8. Event reports submitted by chapters.
-- Counts support the Chapter Reports screen: total participants, first-timers, and existing members.
CREATE TABLE IF NOT EXISTS event_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_id BIGINT UNSIGNED NOT NULL,
  chapter_id BIGINT UNSIGNED NOT NULL,
  submitted_by BIGINT UNSIGNED NOT NULL,
  total_participants INT UNSIGNED NOT NULL DEFAULT 0,
  first_timers INT UNSIGNED NOT NULL DEFAULT 0,
  existing_members INT UNSIGNED NOT NULL DEFAULT 0,
  report_summary TEXT NULL,
  highlights TEXT NULL,
  challenges TEXT NULL,
  recommendations TEXT NULL,
  status ENUM('draft','submitted','under_review','approved','revision_requested','rejected') NOT NULL DEFAULT 'draft',
  submitted_at DATETIME NULL,
  reviewed_by BIGINT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  review_note TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_reports_event (event_id),
  KEY idx_reports_chapter_status (chapter_id, status),
  KEY idx_reports_submitted_at (submitted_at),
  CONSTRAINT fk_reports_event
    FOREIGN KEY (event_id) REFERENCES events(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_reports_chapter
    FOREIGN KEY (chapter_id) REFERENCES chapters(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_reports_submitter
    FOREIGN KEY (submitted_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_reports_reviewer
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 9. Multiple photos/documents can be attached to an event report.
CREATE TABLE IF NOT EXISTS event_report_files (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_report_id BIGINT UNSIGNED NOT NULL,
  uploaded_by BIGINT UNSIGNED NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  file_type VARCHAR(100) NULL,
  caption VARCHAR(255) NULL,
  uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_report_files_report (event_report_id),
  CONSTRAINT fk_report_files_report
    FOREIGN KEY (event_report_id) REFERENCES event_reports(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_report_files_uploader
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 10. National or chapter announcements.
CREATE TABLE IF NOT EXISTS announcements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  created_by BIGINT UNSIGNED NOT NULL,
  target_audience ENUM('everyone','national_admins','chapter_admins','specific_chapter') NOT NULL DEFAULT 'everyone',
  target_chapter_id BIGINT UNSIGNED NULL,
  title VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATETIME NULL,
  expires_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_announcements_published (is_published, published_at),
  KEY idx_announcements_chapter (target_chapter_id),
  CONSTRAINT fk_announcements_creator
    FOREIGN KEY (created_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_announcements_chapter
    FOREIGN KEY (target_chapter_id) REFERENCES chapters(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 11. In-app notifications for request statuses, reports, and announcements.
CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  created_by BIGINT UNSIGNED NULL,
  title VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  related_type ENUM('poster_request','event_report','event','announcement','account') NULL,
  related_id BIGINT UNSIGNED NULL,
  target_url VARCHAR(500) NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  read_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notifications_user_read_date (user_id, is_read, created_at),
  CONSTRAINT fk_notifications_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_notifications_creator
    FOREIGN KEY (created_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 12. Optional audit trail for administrative actions.
CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(100) NULL,
  entity_id BIGINT UNSIGNED NULL,
  details TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_audit_user_date (user_id, created_at),
  KEY idx_audit_entity (entity_type, entity_id),
  CONSTRAINT fk_audit_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- Helpful dashboard views
CREATE OR REPLACE VIEW vw_poster_request_progress AS
SELECT
  pr.id AS poster_request_id,
  pr.chapter_id,
  c.chapter_name,
  pr.event_title,
  pr.status AS request_status,
  pr.submitted_at,
  COUNT(prs.id) AS total_steps,
  SUM(CASE WHEN prs.status = 'completed' THEN 1 ELSE 0 END) AS completed_steps,
  CASE
    WHEN COUNT(prs.id) = 0 THEN 0
    ELSE ROUND(100 * SUM(CASE WHEN prs.status = 'completed' THEN 1 ELSE 0 END) / COUNT(prs.id))
  END AS progress_percent
FROM poster_requests pr
JOIN chapters c ON c.id = pr.chapter_id
LEFT JOIN poster_request_steps prs ON prs.poster_request_id = pr.id
GROUP BY pr.id, pr.chapter_id, c.chapter_name, pr.event_title, pr.status, pr.submitted_at;

CREATE OR REPLACE VIEW vw_event_report_summary AS
SELECT
  er.id AS event_report_id,
  er.event_id,
  e.title AS event_title,
  er.chapter_id,
  c.chapter_name,
  er.total_participants,
  er.first_timers,
  er.existing_members,
  er.status,
  er.submitted_at
FROM event_reports er
JOIN events e ON e.id = er.event_id
JOIN chapters c ON c.id = er.chapter_id;
