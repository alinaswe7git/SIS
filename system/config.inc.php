<?php
error_reporting(E_ALL);								// Disable PHP error messages in production

/*
 * General system setup
 * You are required to change these values for the setup of the system.
 */
$conf['conf']['installed'] =TRUE;						// Set to true to enable system
$conf['conf']['debugging'] = FALSE;						// Set TRUE to enable debugging
$conf['conf']['institutionName'] = "National Institute for Public Administration"; 			// Institution name
$conf['conf']['organization'] = "NIPA"; 			// organization name
$conf['conf']['domain'] = "nipa.ac.zm";						// Domain on which the pages will be served
$conf['conf']['mailEnabled'] = FALSE;						// Whether to enable in-system mailclient
$conf['conf']['hash'] = "2#FCLWJEFO2j3@K#LKF";					// Change this to a unique random set of characters
$conf['conf']['titleName'] = "NIPA Information System"; 		// Default Page Title
$conf['conf']['systemMail'] = "it@nipa.ac.zm";	 				// System email
$conf['conf']['path'] = "https://elearning.nipa.ac.zm/sis"; 					// Change to the installation path of Edurole on webserver (example: www.example.com/pathtosystem) would be /pathtosystem.
$conf['conf']['systemPath'] = "system/"; 					// Change to the installation path of Edurole on webserver (example: www.example.com/pathtosystem) would be /pathtosystem.
$conf['conf']['currency'] = "ZMW"; 						// Used Currency
$conf['conf']['language'] = "EN"; 						// Language


$conf['institution']['head'] = "Head Name";
$conf['institution']['title'] = "Prof. R.N. Mukwena";

/*
 * Update repository server
 */
$conf['conf']['updates']['server'] = "https://sis.nipa.ac.zm/update/";		// Update server

/*
 * MYSQL server information
 */
$conf['mysql']['server'] = "192.168.0.7"; 					// MySQL server host
$conf['mysql']['user'] = "aviplat"; 						// MySQL user
$conf['mysql']['password'] = "aviplat123!"; 					// MySQL password
$conf['mysql']['db'] = "aviplat"; 						// MySQL database

/*
 * MYSQL Moodle server information
 
$conf['mysql']['server'] = "41.63.16.6"; 					// MySQL server host
$conf['mysql']['user'] = "root"; 						// MySQL user
$conf['mysql']['password'] = "Muweb_123"; 					// MySQL password
$conf['mysql']['db'] = "moodle"; 						// MySQL database
*/
/*
 * SMS server information
 */
//$conf['sms']['server'] = "https://www.probasesms.com/api/json/commercials/sms/send-bulk"; 		// SMS server host
$conf['sms']['server'] = "https://www.probasesms.com/api/json/commercials/sms/send-bulk"; 		// SMS server host
$conf['sms']['user'] = "mulungushiuni"; 						// SMS user
$conf['sms']['password'] = "p@ssword@1"; 						// SMS password
$conf['sms']['senderid'] = "NIPA"; 								//SenderID
$conf['sms']['source'] = "NIPA"; 						// SMS Sender Name


/*
 * LDAP server information
 *
$conf['ldap']['ldapEnabled'] = FALSE; 						// Enable LDAP integration or not
$conf['ldap']['server'] = "10.0.0.2"; 						// LDAP host
$conf['ldap']['port'] = "389"; 							// LDAP server port
$conf['ldap']['domain']    = "dc=mu,dc=ac,dc=zm"; 			// Dedicated OU is needed for students
$conf['ldap']['studentou'] = "ou=students,dc=mu,dc=ac,dc=zm"; 		// Dedicated OU is needed for students
$conf['ldap']['staffou']   = "ou=staff,dc=mu,dc=ac,dc=zm"; 		// Dedicated OU is needed for staff
$conf['ldap']['adminou']   = "ou=administrators,dc=mu,dc=ac,dc=zm"; 	// Dedicated OU is needed for administrators

/*
 * Mail server information
 *
$conf['mail']['server'] = "mail.mu.ac.zm"; 				// IMAP server address
$conf['mail']['port'] = "389"; 							// IMAP port

/*
 * System paths
 */
$conf['conf']['corePath'] = "system/"; 
$conf['conf']['classPath'] = "system/classes/"; 				// Location for classes
$conf['conf']['viewPath'] = "system/views/"; 					// Location for views
$conf['conf']['libPath'] = "lib/"; 						// Location for forms
$conf['conf']['servicePath'] = "system/services/"; 				// Location for services
$conf['conf']['formPath'] = "system/forms/"; 					// Location for forms
$conf['conf']['templatePath'] = "templates/"; 					// Location for templates
//$conf['conf']['fullTemplatePath'] = "C:\xampp/htdocs/aviplatform/templates/"; 		// Location for templates
$conf['conf']['dataStorePath'] = getcwd() . "datastore/"; 			// Datastore location (userhomes, identities, etc.)
$conf['conf']['dataStoreJournal'] = getcwd() ."journals/"; 			// Datastore location (journals)


/*
 * Enabled templates, default is first template listed
 */
$conf['conf']['templates'] = array("shards");	// Template names in array form

/*
 * CSS available to the system, required is included everywhere header is active
 */
$conf['css']['required'] = array( "style", "bootstrap", "ddslick", "chat");

$conf['css']['bootstrap'] = '<link href="%BASE%/templates/%TEMPLATE%/css/bootstrap.css" rel="stylesheet" type="text/css" />';
$conf['css']['sb-admin'] = '<link href="%BASE%/templates/%TEMPLATE%/css/sb-admin.css" rel="stylesheet" type="text/css" />';
$conf['css']['style'] = '<link href="%BASE%/templates/%TEMPLATE%/css/style.css" rel="stylesheet" type="text/css" />';
$conf['css']['ddslick'] = '<link href="%BASE%/templates/%TEMPLATE%/css/ddSlick.css" rel="stylesheet" type="text/css" />';
$conf['css']['jq'] = '<link href="%BASE%/templates/%TEMPLATE%/css/jq.css" rel="stylesheet" type="text/css" />';
$conf['css']['login'] = '<link href="%BASE%/templates/%TEMPLATE%/css/login.css" rel="stylesheet" type="text/css" />';
$conf['css']['fullcalendar'] = '<link href="%BASE%/lib/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />';
$conf['css']['fullcalendar.print'] = '<link href="%BASE%/lib/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media="print" />';
$conf['css']['aloha'] = '<link href="%BASE%/lib/aloha/css/aloha.css" rel="stylesheet" type="text/css" />';
$conf['css']['autocomplete'] = '<link href="%BASE%/lib/autocomplete/autocomplete.css" rel="stylesheet" type="text/css" />';
//$conf['css']['chat'] = '<link href="%BASE%/templates/%TEMPLATE%/css/chat.css" rel="stylesheet" type="text/css" />';

/*
 * Javascript available to the system, required is included everywhere header is active
 */
$conf['javascript']['required'] = array("mail", "jquery.dropdown", "bootstrap", "jquery");


$conf['javascript']['jquery'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.js"></script>';
$conf['javascript']['bootstrap'] = '<script type="text/javascript" src="%BASE%/lib/bootstrap/bootstrap.js"></script>';
$conf['javascript']['jquery.dropdown'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.dropdown.js"></script>';
$conf['javascript']['mail'] = '<script type="text/javascript" src="%BASE%/lib/edurole/javascript/mail.js"></script>';
$conf['javascript']['jquery.ui'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.ui.core.js"></script>';
$conf['javascript']['jquery.ui.widget'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.ui.widget.js"></script>';
$conf['javascript']['jquery.ui.datepicker'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.ui.datepicker.js"></script>';
$conf['javascript']['require'] = '<script type="text/javascript" src="%BASE%/lib/requirejs/require.js"></script>';
$conf['javascript']['aloha'] = '<script src="%BASE%/lib/aloha/lib/aloha.js" data-aloha-plugins="common/ui,  common/format, common/list, common/link, common/highlighteditables"></script>';
$conf['javascript']['fullcalendar'] = '<script type="text/javascript" src="%BASE%/lib/fullcalendar/fullcalendar.min.js"></script>';
$conf['javascript']['register'] = '<script type="text/javascript" src="%BASE%/lib/edurole/javascript/register.js"></script>';
$conf['javascript']['jquery.form-repeater'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.form-repeater.js"></script>';
$conf['javascript']['jquery.ui.dialog'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.ui.dialog.js"></script>';
$conf['javascript']['jquery.autocomplete'] = '<script type="text/javascript" src="%BASE%/lib/autocomplete/jquery.autocomplete.js"></script>';
?>
