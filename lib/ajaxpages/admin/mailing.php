<?php
/**
  * Manage mailing system
  *
  * @package Panthera\core\ajaxpages
  * @author Damian Kęska
  * @author Mateusz Warzyński
  * @license GNU Affero General Public License 3, see license.txt
  */

if (!defined('IN_PANTHERA'))
      exit;

$panthera -> importModule('mailing');
$panthera -> locale -> loadDomain('mailing');
$panthera -> config -> loadSection('mailing');

// permissions
$canModifySettings = getUserRightAttribute($panthera->user, 'can_edit_mailing'); $panthera -> template -> push ('canModifySettings', $canModifySettings);
$canSendMails = getUserRightAttribute($user, 'can_send_mails'); $panthera -> template -> push('canSendMails', $canSendMails);

// send one or more e-mails
if ($_GET['action'] == 'send')
{
    if (!$canSendMails)
        ajax_exit(array('status' => 'failed', 'message' => localize('Permission denied. You dont have access to this action', 'messages')));

    if (strlen($_POST['body']) < 5)
        ajax_exit(array('status' => 'failed', 'message' => localize('Message is too short')));

    if (!$panthera->types->validate($_POST['from'], 'email'))
        ajax_exit(array('status' => 'failed', 'message' => localize('Please type a valid e-mail adress in "from" input')));

    $recipients = explode(',', $_POST['recipients']);

    $r = 0;

    $mail = new mailMessage();

    // subject
    $mail -> setSubject($_POST['subject']);
    $mail -> setFrom($_POST['from']);

    // all recipients
    foreach ($recipients as $recipient)
    {
        $recipient = trim($recipient);
    
        if ($panthera->types->validate($recipient, 'email')) // custom e-mail adress
        {
            $mail -> addRecipient($recipient);
            $r++;
            
        } elseif (substr($recipient, 0, 4) == 'gid:') {
            // groups support here

        } elseif (substr($recipient, 0, 4) == 'uid:') { // get user by id
            $mailUser = new pantheraUser('id', $recipient);

            if ($mailUser->exists())
            {
                $mail -> addRecipient($mailUser->mail);
                $r++;
            }
        } elseif (substr($recipient, 0, 2) == 'u:') { // get user by login
            $mailUser = new pantheraUser('login', $recipient);

            if ($mailUser->exists())
            {
                $mail -> addRecipient($mailUser->mail);
                $r++;
            }
        }
    }

    if ($r > 0)
    {
        $panthera -> session -> set('mailing_last_from', $_POST['from']);
        $panthera -> session -> set('mailing_last_body', $_POST['body']);
        $panthera -> session -> set('mailing_last_recipients', $_POST['recipients']);
        $panthera -> session -> set('mailing_last_subject', $_POST['subject']);

        $send = $mail -> send(pantheraUrl($_POST['body']), 'html');

        if($send)
            ajax_exit(array('status' => 'success', 'message' => localize('Sent', 'mailing'). ' ' .$r. ' ' .localize('mails', 'mailing')));
        else
            ajax_exit(array('status' => 'failed', 'message' => slocalize('Cannot send mail, please check mailing configuration', 'mailing')));
    }

    ajax_exit(array('status' => 'failed', 'message' => localize('Please specify at least one recipient')));
} elseif ($_GET['action'] == 'select') {
    // TODO: list users and groups

    $template -> push('action', 'select');
    $template -> display($tpl);
    pa_exit();
    
/**
  * Save mailing settings
  *
  * @author Damian Kęska
  */
    
} elseif (isset($_POST['mailing_use_php'])) {
    // permissions check
    if(!$canModifySettings)
        ajax_exit(array('status' => 'failed', 'message' => localize('Permission denied. You dont have access to this action', 'messages')));

    // list of allowed fields that can be modified
    $fields = array('mailing_use_php', 'mailing_server', 'mailing_server_port', 'mailing_user', 'mailing_password', 'mailing_smtp_ssl', 'mailing_from');
    
    $_POST['mailing_use_php'] = intval($_POST['mailing_use_php']);
    $_POST['mailing_server_port'] = intval($_POST['mailing_server_port']);
    
    if (!$_POST['mailing_use_php'])
    {
        if(!fsockopen($_POST['mailing_server'], $_POST['mailing_server_port'], $errno, $errstr, 5))
        {
            ajax_exit(array('status' => 'failed', 'message' => localize('Cannot connect to mailing server', 'mailing')));
        }
    }
    
    foreach ($_POST as $key => $value)
    {
        if (in_array($key, $fields))
        {
            $panthera -> config -> setKey($key, $value); // we dont select section here as we bet that those keys already exists and the section will be selected automaticaly
        }
    }

    ajax_exit(array('status' => 'success'));
}

/*$message = new mailMessage();
$message -> setSubject('Testowa wiadomość');
$message -> addRecipient('xyz@gmail.com');
$message -> send('No to lecimy', 'plain');*/

$yn = array(0 => localize('No'), 1 => localize('Yes'));

$mailAttributes = array();
$mailAttributes['mailing_use_php'] = array('value' => (bool)$panthera -> config -> getKey('mailing_use_php', True, 'bool'));

// mailing server
$mailAttributes['mailing_server'] = array('name' => 'Server',  'value' => $panthera -> config -> getKey('mailing_server', null, null, 'mailing'));
$mailAttributes['mailing_server_port'] = array('name' => 'Port', 'value' => $panthera -> config -> getKey('mailing_server_port', 'int', 465, 'mailing'));

// auth data
$mailAttributes['mailing_user'] = array('name' => 'Login', 'value' => $panthera -> config -> getKey('mailing_user', 'email', 'mailing'));
$mailAttributes['mailing_password'] = array('name' => 'Password', 'value' => $panthera -> config -> getKey('mailing_password', '', 'string', 'mailing'));

// ssl
$mailAttributes['mailing_smtp_ssl'] = array('name' => 'SSL', 'value' => (bool)$panthera -> config -> getKey('mailing_smtp_ssl', True, 'bool', 'mailing'));

// From header
$mailAttributes['mailing_from'] = array('value' => $panthera -> config -> getKey('mailing_from', 'email', '', 'mailing'));

if (!$panthera->session->exists('mailing_last_from'))
    $panthera -> session -> set('mailing_last_from', $panthera -> config -> getKey('mailing_from', 'email', '', 'mailing'));

$panthera -> template -> push ('last_subject', $panthera->session->get('mailing_last_subject'));
$panthera -> template -> push ('last_recipients', $panthera->session->get('mailing_last_recipients'));
$panthera -> template -> push ('last_body', $panthera->session->get('mailing_last_body'));
$panthera -> template -> push ('last_from', $panthera->session->get('mailing_last_from'));
$panthera -> template -> push ('mail_attributes', $mailAttributes);

$panthera -> template -> display('mailing.tpl');
pa_exit();
