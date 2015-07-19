<?php
namespace Panthera\cli;
use \Panthera\framework;
use Panthera\indexService;
use Panthera\PantheraFrameworkException;

require __DIR__. '/../init.php';

/**
 * Panthera Framework 2 Core deployment
 *
 * @package Panthera\Deployment
 * @author Damian Kęska <damian@pantheraframework.org>
 */
class deploymentApplication extends application
{
    /**
     * @var null|indexService
     */
    public $indexService = null;

    /**
     * List of available modules
     *
     * @var array
     */
    public $modules = array();

    /**
     * List of all runned tasks
     *
     * @var array
     */
    public $runnedTasks = array();

    /**
     * Mode to only check if all dependencies are at its place
     *
     * @var bool
     */
    public $onlyVerifyDependencies = false;

    /**
     * Constructor
     * Prepare a list of deployment services
     *
     * @author Damian Kęska <damian@pantheraframework.org>
     */
    public function __construct()
    {
        $this->indexService = new indexService;
        $this->indexService->indexFiles();
        $app = framework::getInstance();

        foreach ($this->indexService->mixedFilesStructure as $folder => $files)
        {
            if (strpos($folder, '/deployment') === 0)
            {
                // check if directory has it's "index" eg. tests/testsTask.php - this one would execute group of tasks in selected order
                try
                {
                    $groupTask = $app->getPath('.' .$folder . '/' . basename($folder) . 'Task.php');

                    if ($groupTask)
                    {
                        $tmp = explode('/', $folder);
                        unset($tmp[0]); unset($tmp[1]);

                        $this->modules[implode('/', $tmp)] = $groupTask;
                    }

                } catch (\Panthera\FileNotFoundException $e) { };

                foreach ($files as $filePath => $value)
                {
                    $moduleName = substr(str_replace('Task.php', '', $filePath), 12);

                    // don't add "index" task to the list twice
                    if (dirname($moduleName) == basename($moduleName))
                    {
                        continue;
                    }

                    try {
                        $this->modules[$moduleName] = $app->getPath($filePath);
                    } catch (Exception $e) {};
                }
            }
        }

        parent::__construct();
    }

    /**
     * List all available deployment modules
     *
     * @cli optional
     * @author Damian Kęska <damian@pantheraframework.org>
     */
    public function modules_cliArgument()
    {
        print(implode("\n", array_keys($this->modules)));
        print("\n");
    }

    /**
     * Only verify dependencies instead of running the deployment
     *
     * @cli optional no-value
     * @author Damian Kęska <damian@pantheraframework.org>
     */
    public function check__dependencies_cliArgument()
    {
        $this->onlyVerifyDependencies = true;
    }

    /**
     * Verify task's dependencies recursively
     *
     * @param array $tasks List of tasks
     * @param array $checked List of already checked tasks
     * @param string $parentTask If current iterated task is a child, then there should be a parent
     *
     * @author Damian Kęska <damian@pantheraframework.org>
     * @return null
     */
    public function verifyTasksDependencies($tasks, &$checked, $parentTask = '')
    {
        foreach ($tasks as $task)
        {
            if (in_array($task, $checked))
            {
                continue;
            }

            if (!isset($this->modules[$task]))
            {
                print("Error: Task \"" .$task. "\" does not exists");

                if ($parentTask)
                {
                    print(", required by: \"" .$parentTask. "\"");
                }

                print("\n");
                exit;
            }

            $object = $this->loadTaskModule($task);
            $checked[] = $task;

            if ($object->dependencies)
            {
                $this->verifyTasksDependencies($object->dependencies, $checked, $task);
            }
        }
    }

    /**
     * Instantiate a task
     *
     * @param string $taskName
     *
     * @throws PantheraFrameworkException
     * @throws \Panthera\FileNotFoundException
     *
     * @author Damian Kęska <damian@pantheraframework.org>
     * @return \Panthera\deployment\task|object
     */
    protected function loadTaskModule($taskName)
    {
        require_once $this->modules[$taskName];
        $taskClass = "\\Panthera\\deployment\\" .basename($taskName). "Task";

        if (!class_exists($taskClass, false))
        {
            print('Error: Class "' .$taskClass. '" does not exists for module "' .$taskName. "\"\n");
            exit;
        }

        return new $taskClass;
    }

    /**
     * Parse list of modules given from commandline and execute tasks
     *
     * @param \string[] $opts
     *
     * @throws PantheraFrameworkException
     * @throws \Panthera\FileNotFoundException
     *
     * @author Damian Kęska <damian@pantheraframework.org>
     * @return null|void
     */
    public function parseOpts($opts)
    {
        $checked = array();
        $this->verifyTasksDependencies($opts, $checked);

        if ($this->onlyVerifyDependencies)
        {
            exit;
        }

        foreach ($opts as $moduleName)
        {
            // don't run same task again
            if (isset($this->runnedTasks[$moduleName]))
            {
                continue;
            }

            print("=======> Running task " .$moduleName. "\n");

            /**
             * @var \Panthera\deployment\task $currentTask
             */
            $this->runnedTasks[$moduleName] = $currentTask = $this->loadTaskModule($moduleName);

            if ($currentTask->dependencies)
            {
                $this->parseOpts($currentTask->dependencies);
            }

            if (!method_exists($currentTask, 'execute') && !$currentTask->dependencies)
            {
                print("Error: Method execute does not exists in \"" .$moduleName. "\", cannot start task that even dont have any dependencies defined\n");
                exit;
            }

            /**
             * Execute a post-dependencies check/execution action
             */
            if (method_exists($currentTask, 'execute'))
            {
                $currentTask->execute($this);
            }
        }
    }
}

framework::runShellApplication('deployment');