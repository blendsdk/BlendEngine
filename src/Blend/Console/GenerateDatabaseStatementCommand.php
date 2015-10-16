<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Console;

use Blend\Console\Statement\StatementConfig;
use Blend\Console\StatementConfig\SelectStatementConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of GenerateDatabaseStatementCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class GenerateDatabaseStatementCommand extends DatabaseConsoleCommand {

    /**
     * @var StatementConfig[]
     */
    protected $config;

    protected abstract function getNamespace();

    protected abstract function getOutputFolder();

    protected abstract function getStatamentConfig();

    protected function configure() {
        parent::configure();
        $this->setName('database:statement')
                ->setDescription('Generate Statament Classes');
    }

    protected function ensureFolder($folder) {
        if (!is_dir($folder) && !file_exists($folder)) {
            if (@mkdir($folder, 0777, true) === false) {
                throw new \Exception("Unable to create folder {$folder}");
            }
        }
        return true;
    }

    protected function executeDatabaseOperation(InputInterface $input, OutputInterface $output) {
        $this->config = $this->getStatamentConfig();
        foreach ($this->config as $stmt) {
            if ($stmt->overwrite() && $this->ensureFolder($stmt->getOutFolder())) {
                if ($stmt instanceof SelectStatementConfig) {
                    $this->generateSelectStatement($stmt, $output);
                }
            }
        }
    }

    protected function generateSelectStatement(SelectStatementConfig $stmt, OutputInterface $output) {
        $output->writeln("<info>Generating {$stmt->getName()}</info>");
        $class = $this->renderFile(dirname(__FILE__).'/templates/select_statement.php',array(
            'namespace' => $stmt->getNamespace(),
            'uses' => $stmt->getUses(),
            'classname' => $stmt->getName(),
            'sql' => $stmt->getSQL(),
            'setters' => $stmt->getSetters(),
            'description' => $stmt->getDescription()
        ));
        $file_name = "{$stmt->getOutFolder()}/{$stmt->getName()}.php";
        file_put_contents($file_name, $class);
    }

}
