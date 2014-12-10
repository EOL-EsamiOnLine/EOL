<?php
/**
 * File: config.php
 * User: Masterplan
 * Date: 3/15/13
 * Time: 11:36 AM
 * Desc: Configuration file for EOL
 */

/*----------------------------------*
 *  All system configurations       *
 *----------------------------------*/

// System version
$config['systemVersion'] = '0.0.4';
// System logo
$config['systemLogo'] = '__SYSTEMLOGO__';
// System title
$config['systemTitle'] = '__SYSTEMTITLE__';
// System home website (used for emails)
$config['systemHome'] = '__SYSTEMHOME__';
// System comunication email
$config['systemEmail'] = '__SYSTEMEMAIL__';
// Default system language (watch Languages table in db)
$config['systemLang'] = '__SYSTEMLANGUAGE__';
// Default system time zone (watch php documentation from time zone available)
$config['systemTimeZone'] = '__SYSTEMTIMEZONE__';
// Default controller for students, teachers and admins
$config['controller']['a'] = 'Admin';
$config['controller']['t'] = 'Teacher';
$config['controller']['s'] = 'Student';
$config['controller']['at'] = 'Teacher';
// System directories
$config['systemControllersDir'] = '../controllers/';
$config['systemQuestionTypesClassDir'] = '../questionTypes/';
$config['systemViewsDir'] = '../views/';
$config['systemLibsDir'] = 'libs/';
$config['systemLangsDir'] = 'langs/';
$config['systemQuestionTypesLibDir'] = $config['systemLibsDir'].'questionTypes/';
$config['systemLangsXml'] = '../resources/languages/';
$config['systemExtraDir'] = 'extra/';
// System log files directory
$config['logDir'] = '../logs/';
// System log files
$config['systemLog'] = $config['logDir'].'system.log';
// Main upload directory
$config['systemUploadDir'] = '/uploads/';

/*----------------------------------*
 *  All database configurations     *
 *----------------------------------*/

// Database type (mysql | ...)
$config['dbType'] = '__DBTYPE__';
// Database web address
$config['dbHost'] = '__DBHOST__';
// Database port
$config['dbPort'] = '__DBPORT__';
// Database name
$config['dbName'] = '__DBNAME__';
// Database access username
$config['dbUsername'] = '__DBUSERNAME__';
// Database access password
$config['dbPassword'] = '__DBPASSWORD__';

/*----------------------------------*
 *  All themes configurations       *
 *----------------------------------*/

// Themes directory
$config['themesDir'] = 'themes/';
// Theme name (equals to theme folder)
$config['themeName'] = '__THEME__';
// Theme directory
$config['themeDir'] = $config['themesDir'].$config['themeName'].'/';
// Theme's images directory
$config['themeImagesDir'] = $config['themeDir'].'images/';
// Theme's flags directory
$config['themeFlagsDir'] = $config['themeDir'].'flags/';