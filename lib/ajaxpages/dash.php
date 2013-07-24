<?php
/**
  * Home site
  *
  * @package Panthera\core\ajaxpages
  * @author Damian Kęska
  * @author Mateusz Warzyński
  * @license GNU Affero General Public License 3, see license.txt
  */

if (!defined('IN_PANTHERA'))
      exit;

if (!getUserRightAttribute($user, 'can_see_dash')) {
    $template->display('no_access.tpl');
    pa_exit();
}

$panthera -> locale -> loadDomain('dash');
$panthera -> template -> push('widgetsLocked', "1");

/**
  * Remove widget from dashboard
  *
  * @param string $widget
  * @author Damian Kęska
  */

if ($_GET['action'] == 'remove')
{
    $widgets = $panthera -> config -> getKey('dash_widgets');
    
    // disable widget
    if(array_key_exists($_GET['widget'], $widgets))
    {
        $widgets[$_GET['widget']] = False;
    }
    
    $panthera -> config -> setKey('dash_widgets', $widgets);
    $panthera -> template -> push('widgetsUnlocked', "1");
    
/**
  * Add a widget from /modules/dash/ directory or builtin (gallery or lastLogged)
  *
  * @param string $widget
  * @author Damian Kęska
  */
  
} elseif ($_GET['action'] == 'add') {

    $widget = addslashes(str_replace('/', '', $_GET['widget']));
    
    if (is_file(PANTHERA_DIR. '/modules/dash/' .$widget. '.widget.php') or is_file(SITE_DIR. '/content/modules/dash/' .$widget. '.widget.php') or $widget == 'gallery' or $widget == 'lastLogged')
    {
        $widgets = $panthera -> config -> getKey('dash_widgets');
        $widgets[$widget] = True;
        $panthera -> config -> setKey('dash_widgets', $widgets);
    }
    
    $panthera -> template -> push('widgetsUnlocked', "1");
}

$menu = array();

switch ($_GET['menu'])
{
    case 'settings':
        $menu[] = array('link' => '?display=dash', 'name' => localize('Back'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/home.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=settings&action=users', 'name' => localize('Users'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/users.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=settings&action=my_account', 'name' => localize('My account', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/user.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=database', 'name' => localize('Database management', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/db.png' , 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=leopard', 'name' => localize('Package management', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/package.png' , 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=settings&action=system_info', 'name' => localize('Informations about system', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/system.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=conftool', 'name' => localize('Configuration editor', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/config.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=ajaxpages', 'name' => localize('Index of ajax pages', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/Actions-tab-detach-icon.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=shellutils', 'name' => localize('Shell utils', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/Apps-yakuake-icon.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=errorpages', 'name' => localize('System error pages', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/Actions-process-stop-icon.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=menuedit', 'name' => localize('Menu editor', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/Actions-transform-move-icon.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=locales', 'name' => localize('Language settings', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/locales.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=plugins', 'name' => ucfirst(localize('plugins', 'dash')), 'icon' => '{$PANTHERA_URL}/images/admin/menu/Apps-preferences-plugin-icon.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=templates', 'name' => ucfirst(localize('templates', 'dash')), 'icon' => '{$PANTHERA_URL}/images/admin/menu/Icon-template.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=langtool', 'name' => ucfirst(localize('translates', 'dash')), 'icon' => '{$PANTHERA_URL}/images/admin/menu/langtool.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=mergephps', 'name' => ucfirst(localize('merge phps and json arrays', 'dash')), 'icon' => '{$PANTHERA_URL}/images/admin/mimes/php.png', 'linkType' => 'ajax');
    break;

    case '':
        $_GET['menu'] = 'main';

        // main menu, there are predefined variables
        $menu[] = array('link' => '{$PANTHERA_URL}', 'name' => localize('Front page', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/home.png');
        $menu[] = array('link' => '?display=dash&menu=settings', 'name' => localize('Settings', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/settings.png' , 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=debug', 'name' => localize('Debugging center'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/developement.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=settings&action=users', 'name' => localize('Users'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/users.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=mailing', 'name' => localize('Mailing', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/mail-replied.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=gallery', 'name' => localize('Gallery'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/gallery.png', 'linkType' => 'ajax');
        $menu[] = array('link' => 'createPopup(\'_ajax.php?display=upload&popup=true&callback=upload_file_callback\', 1300, 550);', 'name' => localize('Uploads', 'dash'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/uploads.png', 'linkType' => 'onclick');
        $menu[] = array('link' => '?display=contact', 'name' => localize('Contact'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/contact.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=custom', 'name' => localize('Custom pages'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/custom-pages.png', 'linkType' => 'ajax');
        //$menu[] = array('link' => '?display=newsletter', 'name' => localize('Newsletter'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/Newsletter.png', 'linkType' => 'ajax');
        $menu[] = array('link' => '?display=messages', 'name' => localize('Quick messages'), 'icon' => '{$PANTHERA_URL}/images/admin/menu/messages.png', 'linkType' => 'ajax');
        $panthera -> template -> push('showWidgets', True);
    break;
}

list($menu, $category) = $panthera -> get_filters('dash_menu', array($menu, $_GET['menu']));

/**
  * Main screen
  *
  * @author Damian Kęska
  */

if ($category == 'main')
{
    $settings = $panthera -> config -> getKey('dash_widgets', array('gallery' => True, 'lastLogged' => True), 'array');
    $widgets = False;
    $enabledWidgets = array(); // array of widget instances
    $dashCustomWidgets = array(); // list of templates
    
    if ($panthera->varCache)
    {
        if ($panthera->varCache->exists('dash.widgets'))
        {
            $widgets = $panthera -> varCache -> get('dash.widgets');
            $panthera -> logging -> output('Getting list of widgets from varCache', 'dash');
        }
    }

    if ($widgets == False)
    {
        // list of widgets in lib and content
        $widgetsDir = array();
        if (is_dir(PANTHERA_DIR. '/modules/dash/'))
            $widgetsDir = @scandir(PANTHERA_DIR. '/modules/dash/');
            
        $widgetsContentDir = array();
        if (is_dir(SITE_DIR. '/content/modules/dash/'))
            $widgetsContentDir = @scandir(SITE_DIR. '/content/modules/dash/');
            
        $widgets = array_merge($widgetsDir, $widgetsContentDir);
        unset($widgets[0]);
        unset($widgets[1]);
        
        if ($panthera -> varCache)
        {
            $panthera -> varCache -> set('dash.widgets', $widgets, 120);
            $panthera -> logging -> output('Saving widgets list to varCache', 'dash');
        }
    }
    
    // add widgets from lib and content directories to the list
    foreach ($widgets as $widget)
    {
        $widget = substr($widget, 0, strlen($widget)-11);
    
        if (!array_key_exists($widget, $settings))
            $settings[$widget] = False;
    }
    
    $panthera -> template -> push ('dashAvaliableWidgets', $settings);
    
    // recent gallery items
    if ($settings['gallery'] === True)
    {
        $panthera -> importModule('gallery');
        $panthera -> template -> push ('galleryItems', gallery::getRecentPicture('', 9));
    }

    // last logged in users    
    if ($settings['lastLogged'] === True)
    {
        $u = getUsers('', 10, 0, 'lastlogin', 'DESC');
        $users = array();
        
        foreach ($u as $key => $value)
        {
            //if ($value->attributes->superuser)
                //continue;

            $users[] = array('login' => $value->login, 'time' => date_calc_diff(strtotime($value->lastlogin), time()), 'avatar' => pantheraUrl($value->profile_picture), 'uid' => $value->id);
        }
        
        $panthera -> template -> push ('lastLogged', $users);
    }
    
    // load all enabled widgets
    foreach ($settings as $widget => $enabled)
    {
        if ($enabled == True)
        {
            $dir = getContentDir('/modules/dash/' .$widget. '.widget.php');
            
            if ($dir == False)
                continue;
                
            $widgetName = $widget. '_dashWidget';
            
            try {
                include_once $dir;
                
                if (!class_exists($widgetName))
                {
                    $panthera -> logging -> output('Class ' .$widgetName. ' does not exists in ' .$dir. ' file, skipping this widget', 'dash');
                    continue;
                }
                
                $enabledWidgets[$widget] = new $widgetName($panthera);
                $enabledWidgets[$widget] -> display();
                
                if (isset($enabledWidgets[$widget]->template))
                    $dashCustomWidgets[] = $enabledWidgets[$widget]->template;
                else
                    $dashCustomWidgets[] = 'dashWidget_' .$widget. '.tpl';
                    
            } catch (Exception $e) {
                $panthera -> logging -> output ('Cannot display a widget, got an exception: ' .$e->getMessage(), 'dash');
            }
        }
    }
    
    $template -> push ('dashCustomWidgets', $dashCustomWidgets);
}

// menu
$template -> push ('dash_menu', $menu);
$template -> push ('dash_messages', $panthera -> get_filters('ajaxpages.dash.msg', array()));

$panthera -> template -> display ('dash.tpl');
pa_exit();
