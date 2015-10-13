<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Database;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of GenerateSchemaHelperCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class GenerateSchemaHelperCommand extends DatabaseConsoleCommand {

    /**
     * @var var_char The test field
     */
    const test = 'test';

    protected abstract function getNamespace();

    protected abstract function getOutputFolder();

    protected $fieldTemplate = <<<PHP
    /**
     * @var %data_type% %description%
     */
    const %const_name% = '%column_name%';
PHP;
    protected $classTemplate = <<<PHP
<?php

namespace %namespace%;

class %classname% {

    /**
     * @var string The %table_name% table name
     */
    const TABLE_NAME = '%table_name%';
%fields%

}
PHP;

    protected function configure() {
        $ns_default = ucfirst($this->getApplicationName()) . '\Database\Schema';
        $out_default = realpath($this->getConfigFolderLocation() . '/../src/') . '/Database/Schema';
        parent::configure();
        $this->setName('database:schemahelper')
                ->setDescription('Generate Schema helper files')
                ->addArgument('table', InputArgument::OPTIONAL,
                        'LIKE select criteria to select the table names: %( = ALL)',
                        '%');
    }

    protected function createOutputFolder() {
        @mkdir($this->getOutputFolder(), 0777, true);
    }

    protected function getTables($search) {
        $sql = <<<SQL
            select
                    *
            from
                    information_schema.tables
            where
                    table_name like :table and
                    table_catalog = :database and
                    table_schema='public'
SQL;

        return $this->database->executeQuery(
                        $sql,
                        array(
                    ':database' => $this->database->getDatabaseName(),
                    ':table' => $search
        ));
    }

    protected function getTableColumns($table_name) {
        $sql = <<<SQL
            select
                    *
            from
                    information_schema.columns
            where
                            table_catalog = '{$this->database->getDatabaseName()}' and
                            table_schema = 'public' and
                            table_name  = '{$table_name}'
SQL;

        $columns = $this->database->executeQuery($sql);
        foreach ($columns as $index => $column) {
            $column['description'] = 'Column is '
                    . ($column['is_nullable'] ? 'Nullable' : 'Not Nullable')
                    . '. Defaults to '
                    . (empty($column['column_default']) ? 'NULL' : $column['column_default']);
            $column['data_type'] = str_replace(' ', '_', $column['data_type']);
            $column['const_name'] = strtoupper($column['column_name']);
            $columns[$index] = $column;
        }
        return $columns;
    }

    protected function render($template, $data = array()) {
        return str_replace(array_keys($data), array_values($data), $template);
    }

    protected function executeDatabaseOperation(InputInterface $input, OutputInterface $output) {

        $tables = $this->getTables($input->getArgument('table'));
        $this->output->writeln("<info>" . count($tables) . " table(s) found!<info>");
        $this->createOutputFolder();

        foreach ($tables as $table) {

            $fields = array();
            $table_name = $table['table_name'];
            $class_name = strtoupper($table_name) . '_SCHEMA';
            $columns = $this->getTableColumns($table_name);

            $this->output->writeln("<info> Generating {$table_name}<info>");

            foreach ($columns as $column) {
                $field = str_replace_template($this->fieldTemplate,
                        array(
                    '%data_type%' => $column['data_type'],
                    '%description%' => $column['description'],
                    '%const_name%' => $column['const_name'],
                    '%column_name%' => $column['column_name']
                ));
                $fields[] = "\n$field";
            }

            $class = str_replace_template($this->classTemplate,
                    array(
                '%namespace%' => $this->getNamespace(),
                '%table_name%' => $table_name,
                '%classname%' => $class_name,
                '%fields%' => implode("\n", $fields)
            ));

            file_put_contents("{$this->getOutputFolder()}/{$class_name}.php",
                    $class);
        }
    }

}
