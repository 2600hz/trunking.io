-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 13, 2013 at 07:53 PM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `trunkingdotio`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(14) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `remaining` int(3) NOT NULL DEFAULT '240',
  `registered` tinyint(4) NOT NULL DEFAULT '0',
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;