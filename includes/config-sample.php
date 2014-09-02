<?php
/**
 * File: config.php
 * User: Masterplan
 * Date: 3/15/13
 * Time: 11:36 AM
 * Desc: Configuration file for EOL2 webapp
 */

/*----------------------------------*
 *  All system configurations       *
 *----------------------------------*/

// System version
$config['systemVersion'] = '0.0.1';
// System title
$config['systemTitle'] = 'EOL - Esami On Line';
// System home website (used for emails)
$config['systemHome'] = 'http://localtesteol/';
// System comunication email
$config['systemEmail'] = 'no-reply@eol.org';
// Default system language (watch Languages table in db)
$config['systemLang'] = 'en';
// Default system time zone (watch php documentation from time zone available)
$config['systemTimeZone'] = 'Europe/Rome';
// Default controller for students, teachers and admins
$config['controller']['a'] = 'Admin';
$config['controller']['t'] = 'Teacher';
$config['controller']['s'] = 'Student';
$config['controller']['at'] = 'Teacher';
// Preferred secure type (session | cookie)
$config['systemSecure'] = 'session';
// Cookie deadline (For cookie secure type, default one (1) day [86400 secs])
$config['cookieDeadline'] = time()+86400;
// System directories
$config['systemControllersDir'] = '../controllers/';
$config['systemViewsDir'] = '../views/';
$config['systemLibsDir'] = 'libs/';
$config['systemLangsDir'] = 'langs/';
$config['systemLangsXml'] = '../resources/languages/';
$config['systemExtraDir'] = 'extra/';
$config['systemModulesDir'] = '../modules/';
// System log files directory
$config['logDir'] = '../logs/';
// System log files
$config['systemLog'] = $config['logDir'].'system.log';
// Main upload directory
$config['systemUploadDir'] = '/uploads/';
// Datatable text column length
$config['datatablesTextLength'] = 100;
// Ellipsis
$config['ellipsis'] = ' [...]';

/*----------------------------------*
 *  All database configurations     *
 *----------------------------------*/

// Database type (mysql | ...)
$config['dbType'] = 'mysql';
// Database web address
$config['dbHost'] = 'localhost';
// Database port
$config['dbPort'] = '3306';
// Database name
$config['dbName'] = 'eol';
// Database access username
$config['dbUsername'] = 'username';
// Database access password
$config['dbPassword'] = 'password';

/*----------------------------------*
 *  All themes configurations       *
 *----------------------------------*/

// Themes directory
$config['themesDir'] = 'themes/';
// Theme name (equals to theme folder)
$config['themeName'] = 'default';
// Theme directory
$config['themeDir'] = $config['themesDir'].$config['themeName'].'/';
// Theme's images directory
$config['themeImagesDir'] = $config['themeDir'].'images/';
// Theme's flags directory
$config['themeFlagsDir'] = $config['themeDir'].'flags/';