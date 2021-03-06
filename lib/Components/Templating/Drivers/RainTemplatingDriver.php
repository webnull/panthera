<?php
namespace Panthera\Components\Templating\Drivers;

use Panthera\Components\Templating\TemplatingInterface;
use Panthera\Components\Kernel\BaseFrameworkClass;

/**
 * Panthera Framework 2 template management class - displaying, compiling,
 *   validating, etc.
 *
 * @Package Panthera
 * @author Mateusz Warzyński <lxnmen@gmail.com>
 * @author Damian Kęska <damian@pantheraframework.org>
 */
class RainTemplatingDriver extends BaseFrameworkClass implements TemplatingInterface
{
    /**
     * RainTPL4 object
     *
     * @var null
     */
    public $rain = null;

    /**
     * Debugging mode on/off
     *
     * @var bool
     */
    public $debug = false;

    /**
     * Remove comments while compiling on/off
     *
     * @var bool
     */
    public $removeComments = true;

    /**
     * Ignore unknown tags on/on
     *
     * @var bool
     */
    public $ignoreUnknownTags = true;

    /**
     * Init template function
     *
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     */
    public function __construct()
    {
        parent::__construct();

        $this->debug = $this->app->config->get('template.debug', false);
        $this->removeComments = $this->app->config->get('template.remove_comments', true);
        $this->ignoreUnknownTags = $this->app->config->get('template.ignore_unknown_tags', true);

        if (!is_dir($this->app->appPath. '/.content/cache/RainTPL4/'))
        {
            mkdir($this->app->appPath. '/.content/cache/RainTPL4');
        }

        $this->rain = new \Rain\RainTPL4();

        $this->rain->setConfiguration([
            'base_url'            => null,
            'tpl_dir'             => $this->app->appPath . '/.content/Templates/',
            'cache_dir'           => $this->app->appPath . '/.content/cache/RainTPL4/',
            'remove_comments'     => $this->removeComments,
            'debug'               => $this->debug,
            'ignore_unknown_tags' => $this->ignoreUnknownTags,
        ]);
    }

    /**
     * Set include paths to template files
     *
     * @param array $paths
     * @return mixed|void
     */
    public function setIncludePaths(array $paths)
    {
        $paths[] = $this->app->appPath . '/.content/Templates/';

        $this->rain->setConfiguration([
            'tpl_dir' => $paths,
        ], true);
    }

    /**
     * Set configuration variables
     *
     * @param array $settings
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     */
    public function setConfiguration($settings)
    {
        $this->rain->setConfiguration($settings, true);
    }

    /**
     * Assign value for template
     *
     * @param mixed|array $variable Name of template variable or associative array name/value
     * @param mixed $value value assigned to this variable. Not set if variable_name is an associative array
     *
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     */
    public function assign($variable, $value = null)
    {
        $this->rain->assign($variable, $value);
    }

    /**
     * Draw template with assigned variables
     *
     * @param string $templateFile path to template
     * @param bool $toString if the method should return a string
     * @param bool $isString if input is a string, not a file path or echo the output
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     * @return string
     */
    public function display($templateFile, $toString = false, $isString = false)
    {
        return $this->rain->draw($templateFile, $toString, $isString);
    }
}