<?php
namespace Panthera;

/**
 * This class is handling all messages, saving them to file, displaying
 * its used to debug whole application based on Panthera Framework
 *
 * @Package Panthera
 * @author Damian Kęska <damian@pantheraframework.org>
 * @author Mateusz Warzyński <lxnmen@gmail.com>
 */
class logging extends baseClass
{
    /**
     * Store all messages that has been printed
     *
     * @var array $messages
     */
    public $messages = array();

    /**
     * If activated then it will count execution time of selected block of code
     *
     * @var string|int
     */
    protected $timer = null;

    /**
     * Message format
     *
     * @var mixed|string $format
     */
    public $format = '';

    /**
     * Date format used in message
     *
     * @var mixed|string $dateFormat
     */
    public $dateFormat = '';

    /**
     * Turn on/off logging
     *
     * @var bool $enabled
     */
    public $enabled = false;

    /**
     * Init logging function
     *
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     */
    public function __construct()
    {
        parent::__construct();
        $this->format = $this->app->config->get('logging.format', '[%date][%path:%line] %executionTime%message');
        $this->dateFormat = $this->app->config->get('logging.format.date', 'Y-m-d H:i');
        $this->enabled = $this->app->config->get('logging.enabled', false);
    }

    /**
     *  Function that prints message
     *
     * @param string $message to print
     *
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     * @return bool|string
     */
    public function output($message)
    {
        if (!$this->enabled)
        {
            return false;
        }

        if (is_float($this->timer))
        {
            $this->timer = microtime(true) - $this->timer;
        }

        $backtrace = debug_backtrace();
        $backtrace = end($backtrace);

        $formatting = array(
            '%date'          => date($this->dateFormat),
            '%fullPath'      => $backtrace['file'],
            '%path'          => str_replace($this->app->appPath, '', $backtrace['file']),
            '%line'          => $backtrace['line'],
            '%basename'      => basename($backtrace['file']),
            '%function'      => $backtrace['function'],
            '%class'         => isset($backtrace['class']) ? $backtrace['class'] : '',
            '%message'       => $message,
            '%executionTime' => (string)$this->timer. ' ',
        );

        foreach ($formatting as $key => $value)
        {
            $this->format = str_replace($key, $value, $this->format);
        }

        $this->messages[] = $this->format;

        // reset the timer
        $this->timer = null;

        return $this->format;
    }

    /**
     * Start the timer to count execution time
     *
     * @author Damian Kęska <damian@pantheraframework.org>
     */
    public function startTimer()
    {
        $this->timer = microtime(true);
    }

    /**
     * Clear messages buffer
     *
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     */
    public function clear()
    {
        $this->messages = array();
    }
}