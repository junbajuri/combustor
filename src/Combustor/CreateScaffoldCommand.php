<?php

namespace Combustor;

use Combustor\Tools\GetColumns;
use Combustor\Tools\Inflect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateScaffoldCommand extends Command
{

	/**
	 * Set the configurations of the specified command
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
				'keep',
				null,
				InputOption::VALUE_NONE,
				'Keeps the name to be used'
			)->addOption(
				'snake',
				NULL,
				InputOption::VALUE_NONE,
				'Use the snake case naming convention for the accessor and mutators'
			);
	}

	/**
	 * Execute the command
	 * 
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$bootstrap = $input->getOption('bootstrap');
		$keep = $input->getOption('keep');
		$snake = $input->getOption('snake');
		
		$arguments = array(
			'command' => NULL,
			'name' => $input->getArgument('name')
		);

		$commands = array('create:controller', 'create:model', 'create:view');

		foreach ($commands as $command) {
			$arguments['command'] = $command;
			
			if (isset($arguments['--bootstrap'])) {
				unset($arguments['--bootstrap']);
			}

			if (isset($arguments['--keep'])) {
				unset($arguments['--keep']);
			}

			if (isset($arguments['--snake'])) {
				unset($arguments['--snake']);
			}

			if ($command == 'create:controller') {
				$arguments['--keep'] = $keep;
				$arguments['--snake'] = $snake;
			} elseif ($command == 'create:model') {
				$arguments['--snake'] = $snake;
			} elseif ($command == 'create:view') {
				$arguments['--bootstrap'] = $bootstrap;
				$arguments['--snake'] = $snake;
			}

			$input = new ArrayInput($arguments);

			$application = $this->getApplication()->find($command);
			$result = $application->run($input, $output);
		}
	}
	
}