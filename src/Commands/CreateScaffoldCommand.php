<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\AbstractCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create Scaffold Command
 *
 * Generates a Wildfire or Doctrine-based controller,
 * model and view files for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CreateScaffoldCommand extends AbstractCommand
{
    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (
            file_exists(APPPATH . 'libraries/Wildfire.php') ||
            file_exists(APPPATH . 'libraries/Doctrine.php')
        ) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create:scaffold')
            ->setDescription('Create a new controller, model and view')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the controller, model and view'
            )->addOption(
                'bootstrap',
                NULL,
                InputOption::VALUE_NONE,
                'Include the Bootstrap CSS/JS Framework tags'
            )->addOption(
                'camel',
                NULL,
                InputOption::VALUE_NONE,
                'Use the camel case naming convention'
            )->addOption(
                'doctrine',
                NULL,
                InputOption::VALUE_NONE,
                'Use the Doctrine\'s specifications'
            )->addOption(
                'keep',
                null,
                InputOption::VALUE_NONE,
                'Keeps the name to be used'
            )->addOption(
                'lowercase',
                null,
                InputOption::VALUE_NONE,
                'Keep the first character of the name to lowercase'
            )->addOption(
                'wildfire',
                NULL,
                InputOption::VALUE_NONE,
                'Use the Wildfire\'s specifications'
            );
    }

    /**
     * Executes the command.
     * 
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return object|OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bootstrap = $input->getOption('bootstrap');
        $camel = $input->getOption('camel');
        $doctrine = $input->getOption('doctrine');
        $keep = $input->getOption('keep');
        $lowercase = $input->getOption('lowercase');
        $wildfire = $input->getOption('wildfire');

        $arguments = [
            'command' => NULL,
            'name' => $input->getArgument('name')
        ];

        $commands = [
            'create:controller',
            'create:model',
            'create:view'
        ];

        foreach ($commands as $command) {
            $arguments['command'] = $command;
            
            if (isset($arguments['--bootstrap'])) {
                unset($arguments['--bootstrap']);
            }

            if (isset($arguments['--camel'])) {
                unset($arguments['--camel']);
            }

            if (isset($arguments['--doctrine'])) {
                unset($arguments['--doctrine']);
            }

            if (isset($arguments['--keep'])) {
                unset($arguments['--keep']);
            }

            if (isset($arguments['--lowercase'])) {
                unset($arguments['--lowercase']);
            }

            if (isset($arguments['--wildfire'])) {
                unset($arguments['--wildfire']);
            }

            if ($command == 'create:controller') {
                $arguments['--camel']     = $camel;
                $arguments['--doctrine']  = $doctrine;
                $arguments['--keep']      = $keep;
                $arguments['--lowercase'] = $lowercase;
                $arguments['--wildfire']  = $wildfire;
            } else if ($command == 'create:model') {
                $arguments['--camel']     = $camel;
                $arguments['--doctrine']  = $doctrine;
                $arguments['--lowercase'] = $lowercase;
                $arguments['--wildfire']  = $wildfire;
            } else if ($command == 'create:view') {
                $arguments['--bootstrap'] = $bootstrap;
                $arguments['--camel']     = $camel;
                $arguments['--keep']      = $keep;
            }

            $input = new ArrayInput($arguments);
            $application = $this->getApplication()->find($command);
            $result = $application->run($input, $output);
        }
    }
}