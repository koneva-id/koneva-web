-- Migration: add triage_date to client_request_histories
-- Run this in phpMyAdmin or cPanel terminal: php artisan migrate --force

ALTER TABLE `client_request_histories` 
ADD COLUMN IF NOT EXISTS `triage_date` DATE NULL AFTER `client_note`;
