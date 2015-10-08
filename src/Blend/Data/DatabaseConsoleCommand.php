<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Data;

use Blend\Core\Environments;
use Blend\Data\Database;
use DatabaseQueryException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for a Console command that need to do operations on the database
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class DatabaseConsoleCommand extends Command {

    protected $env;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Database;
     */
    protected $database;

    protected abstract function executeDatabaseOperation(InputInterface $input, OutputInterface $output);

    protected abstract function getApplicationName();

    protected abstract function getConfigFolderLocation();

    protected function configure() {
        $this->addOption('environment', null, InputOption::VALUE_OPTIONAL, 'Configuration environment (' . Environments::PRODUCTION . ' or ' . Environments::DEVELOPMENT . ')', Environments::PRODUCTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->input = $input;
        $this->output = $output;

        if ($this->setEnvironment()) {
            if ($this->setDatabaseConnection()) {
                $this->executeDatabaseOperation($input, $output);
            }
        }
    }

    private function setDatabaseConnection() {
        $configFile = realpath($this->getConfigFolderLocation() . '/' . $this->getApplicationName() . "-{$this->env}-config.php");
        if (file_exists($configFile)) {
            $config = include($configFile);
            $this->database = new Database($config['database']);
            try {
                $version = $this->database->executeQuery("select version()");
                $this->output->writeln("<info>Connected to Database</info>");
                return true;
            } catch (DatabaseQueryException $e) {
                $this->output->writeln("<error>{$e->getMessage()}</error>");
            }
        } else {
            $this->output->writeln("<error>Unable to find the config file {$configFile}</error>");
            return false;
        }
    }

    private function setEnvironment() {
        $options = array(Environments::DEVELOPMENT, Environments::PRODUCTION);
        $env = $this->input->getOption('environment');
        if (in_array($env, $options)) {
            $this->env = $env;
            return true;
        } else {
            $this->output->writeln('<error>Invalid environment option!</error>');
            return false;
        }
    }

    protected function getCurrentDatabaseVersion() {
        $sys_db_version = $this->database->executeQuery("select * from information_schema.tables where table_name='sys_db_version' and table_catalog=:database", array(
            ':database' => $this->database->getDatabaseName()
        ));
        if (count($sys_db_version) === 0) {
            $sys_db_version_table = <<<EOT
                    create table sys_db_version (
                            id serial not null primary key,
                            version varchar not null unique,
                            filename varchar not null unique,
                            date_patched timestamp not null default now()
                    );
EOT;
            $this->database->executeQuery($sys_db_version_table);
            return -1;
        } else {
            $version = $this->database->executeQuery("select * from sys_db_version order by id desc limit 1");
            if (count($version) === 0) {
                return -1;
            } else {
                return $version[0]['version'];
            }
        }
    }

}
