<?php
/**
 * Static pages - module, contains database data model
 * 
 * Example:
 * <code>
 * $page = new customPage('unique', 'xyz'); // construct object by unique column = xyz
 * $page -> 
 *
 * @package Panthera\core\components\custompages
 * @author Damian Kęska
 * @author Mateusz Warzyński
 * @license LGPLv3
 */

if (!defined('IN_PANTHERA'))
    exit;

/**
 * Panthera fetch DB based wrapper for custom_pages table in database
 * 
 * @package Panthera\core\components\custompages
 * @author Damian Kęska
 */

class customPage extends pantheraFetchDB
{
    protected $_tableName = 'custom_pages';
    protected $_idColumn = 'id';
    protected $_constructBy = array('id', 'url_id', 'unique', 'array');
    protected $_meta;
    protected $_unsetColumns = array();

    /**
     * Get custompage's meta attributes
     * 
     * This function is diffirent from $this -> getMetas() as this function is using UNIQUE column to identify meta tags.
     * In brief it returns tags for every language of this page.
     *
     * @param string $meta Type of meta, by `id` or `unique`
     * @return object|null
     * @author Damian Kęska
     */

    public function meta($meta='id')
    {
        if ($meta == 'unique')
            $data = $this->unique;
        elseif ($meta == 'id')
            $data = $this->id;
        else
            return False;

        if (!isset($this->_meta[$meta]))
            $this->_meta[$meta] = new metaAttributes($this->panthera, 'cpages_' .$meta, $this->unique);

        return $this->_meta[$meta];
    }

    /**
      * Return columns from database with parsed Panthera URLS
      *
      * @param string $var Variable name
      * @return mixed
      * @author Damian Kęska
      */

    public function __get($var)
    {
        if ($var == 'html')
            return pantheraUrl(parent::__get($var));

        return parent::__get($var);
    }

    /**
      * Set column's value to database and convert automaticaly Panthera URLS
      *
      * @param string $var Variable name
      * @param string $value Value
      * @return mixed
      * @author Damian Kęska
      */

    public function __set($var, $value)
    {
        if ($var == 'html')
            return parent::__set($var, pantheraUrl($value, True));

       return parent::__set($var, $value);

    }

    /**
     * Create custom page
     * 
     * @param string $title Page title
     * @param string $language Valid, installed language. If invalid specified it will fallback to current active or system default
     * @param string|pantheraUser $authorName Author name or pantheraUser object
     * @param int $authorId Author ID, optional if pantheraUser object specified in $authorName argument
     * @param string $unique Unique ID string that will identify all translated versions of this static page
     * @param string $urlId SEO link, can be same as unique
     * @param string $adminTpl Administration panel template to display instead of default
     * @param bool $public Is this a public page or still a draft/private page?
     * 
     * @return bool
     * @author Mateusz Warzyński
     */

    public static function create($title, $language, $authorName, $authorId='', $unique='', $urlId='', $adminTpl='', $public=False)
    {
        $panthera = pantheraCore::getInstance();
        $allLanguages = False;
        
        if (!$urlId)
            $urlId = $panthera -> db -> createUniqueData('custom_pages', 'unique', seoUrl($title));
        
        if (!$unique)
            $unique = $urlId;
        
        if ($language == 'all')
            $allLanguages = True;
        
        // correct user input
        $unique = seoUrl($unique);
        $urlId = seUrl($urlId);
        
        $language = pantheraLocale::getFromOverride($language);
        
        if (is_object($authorName) and $authorName instanceof pantheraFetchDB)
        {
            $authorId = $authorName -> id;
            $authorName = $authorName -> getName();
        }
        
        $array = array(
            'unique' => $unique,
            'url_id' => $urlId,
            'title' => $title,
            'meta_tags' => '',
            'html' => '',
            'author_name' => $authorName,
            'author_id' => $authorId,
            'language' => $language,
            'mod_author_name' => $authorName,
            'mod_author_id' => $authorId,
            'admin_tpl' => $adminTpl,
            'created' => DB_TIME_NOW,
            'mod_time' => DB_TIME_NOW,
            'public' => $public,
        );
        
        $r = parent::create($array);
        
        if ($allLanguages)
        {
            $w = new whereClause;
            $w -> add('AND', 'unique', '=', $unique);
            $w -> add('AND', 'title', '=', $title);
            $w -> add('AND', 'language', '=', $language);
            $cpage = new customPage($w, false);
            
            meta::create('cp_gen_' .$unique, 1, 'var', $cpage->id);
        }
        
        return $r;
    }

    /**
      * Get custom page by unique id and language
      *
      * @param string $field
      * @param string $value
      * @param string $language
      * @param bool $languageFallback - fallback, forceNative
      * @return mixed
      * @author Damian Kęska
      */

    public static function getBy($field, $value, $language='', $languageFallback='fallback')
    {
        $panthera = pantheraCore::getInstance();
        $panthera -> importModule('meta');

        // if not specified language it will be taken from active session
        if ($language == '')
            $language = $panthera -> locale -> getActive();

        if ($field == 'unique' and meta::get('var', 'cp_gen_' .$value))
            $language = 'all';

        $statement = new whereClause();
        $statement -> add('', $field, '=', $value);
        $statement -> add('AND', 'language', '=', $language);

        $cpage = new customPage($statement, '');
        $panthera -> logging -> output ('Trying to get customPage by field=' .$field. ', language=' .$language, 'customPages');

        // the simplest way is to find page by `unique`
        if ($field == 'unique')
        {
            if (!$cpage->exists())
            {
                // fallback to other language
                if ($languageFallback == 'fallback')
                    $cpage = new customPage('unique', $value);
            }
        } else {
            // no `unique` given but `id` or `url_id`

            // if page with `url_id` or `id` does not exists in current language
            if (!$cpage->exists())
            {
                // try to find page in other language to get `unique`
                $cpage = new customPage($field, $value);
                $panthera -> logging -> output ('customPage search by field=' .$field. ', value=' .$value, 'customPages');

                // if we found page by `unique` we can now search for page with that in unique in selected `langauge`
                if ($cpage->exists())
                {
                    $panthera -> logging -> output('And the result is positive, unique=' .$cpage->unique. ', now searching in language=' .$language, 'customPages');

                    $statement = new whereClause();
                    $statement -> add('', 'unique', '=', $cpage->unique);
                    $statement -> add('AND', 'language', '=', $language);

                    $ppage = new customPage($statement, '');

                    // if found, replace cpage with ppage
                    if ($ppage -> exists())
                        $cpage = $ppage;
                    else {
                        if ($languageFallback == 'forceNative')
                            $cpage = new customPage('array', array());
                    }
                }
            }
        }

        return $cpage;
    }

    /**
     * Custom delete function
     * 
     * @return bool
     */

    public function delete()
    {
        meta::remove('var', 'cp_gen_' .$this->unique);
        return parent::delete();
    }
    
    /**
     * Get permissions for view, edit, delete and management
     * 
     * @param string $type Permissions type - view, edit, delete, management
     * @return array
     */
    
    public function getPermissions($type='view')
    {
        $standard = array(
            'custompages.management',
            'custompages.manage.lang.' .$this -> language,
        );
        
        switch ($type)
        {
            case 'view':
                return array_merge($standard, array(
                    'custompages.view.' .$this -> unique,
                    'custompages.view.id.' .$this -> id,
                    'custompages.edit.' .$this -> unique,
                    'custompages.edit.id.' .$this -> id,
                    'custompage.viewall',
                ));
            break;
            
            case 'delete':
            case 'edit':
                return array_merge($standard,array(
                    'custompages.edit.' .$this -> unique,
                    'custompages.edit.id.' .$this -> id,
                ));
            break;
            
            case 'management':
                return $standard;
            break;
        }
    }
}
