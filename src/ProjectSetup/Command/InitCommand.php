<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\ProjectSetup\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Blend\Component\Filesystem\Filesystem;

/**
 * InitCommand helps to initialize a new BlendEngine Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class InitCommand extends Command {

    /**
     * Points to the workfolder getcwd
     * @var string
     */
    private $workFolder;

    /**
     * Points to the folder where the temapltes are located
     * @var string
     */
    private $templatesFolder;

    /**
     * List of available templates
     * @var array
     */
    private $templates;

    /**
     * Mapping table that is used to rename template files
     * @var array
     */
    private $renameTable;

    /**
     * Mapping table that is used to generate files based on a PHP template
     * @var array
     */
    private $renderTable;

    /**
     * The name of the application to generate
     * @var string
     */
    private $applicationName;

    protected function configure() {
        parent::configure();

        $this->templatesFolder = realpath(__DIR__ . '/../Resources/Templates');
        $this->workFolder = getcwd();
        $this->applicationName = str_identifier((new \SplFileInfo($this->workFolder))->getBasename());

        $lowerName = strtolower($this->applicationName);

        $this->renameTable = array(
            'config/gitignore' => 'config/.gitignore',
            'bin/app' => 'bin/' . $lowerName,
            'bin/app.bat' => 'bin/' . $lowerName . '.bat',
            'bin/app.php' => 'bin/' . $lowerName . '.php',
            'src/Console/Application.php' => 'src/Console/' . $this->applicationName . 'Application.php'
        );

        $this->renderTable = array(
            'phpunit.xml.dist',
            'tests/README',
            'config/config.php',
            'bin/app',
            'bin/app.bat',
            'bin/app.php',
            'composer.json',
            'src/Console/Application.php'
        );


        $this->templates = $this->getTemplateNames();
        $this->setName('project:init')
                ->setDescription('Initializes a new BlendEngine project in [' . $this->workFolder . ']')
                ->addOption('template', 't', InputOption::VALUE_OPTIONAL, 'Name of the template to generate this project (' . implode(',', $this->templates) . ')', 'Basic')
                ->addOption('appname', 'a', InputOption::VALUE_OPTIONAL, 'Name of the application to generate', $this->applicationName);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getApplication()->getLongVersion() . "\n");
        if ($this->checkSanity()) {
            $template = strtolower($input->getOption('template'));
            if ($this->checkTemplate($template)) {
                $this->applicationName = $input->getOption('appname');
                $output->writeln('<info>Generating ' . $this->applicationName . ' :)</info>');
                $this->generateWithTemplate($template, $output);
            } else {
                $output->writeln("<error>The requested template [" . $template . '] does not exist!</error>');
            }
        } else {
            $output->writeln("<error>Looks like the " . $this->workFolder . ' is not empty!</error>');
        }
    }

    /**
     * Creates a rendering context array to be used when rendering 
     * template files
     * @return array
     */
    private function createRenderContext() {
        $lowerAppName = strtolower($this->applicationName);
        $gitInfo = $this->getCurrentGitUser();
        return array(
            'applicationName' => $this->applicationName,
            'applicationScriptName' => $lowerAppName,
            'applicationNamespace' => $this->applicationName,
            'applicationPackageName' => get_current_user() . '/' . $lowerAppName,
            'applicationCommandClassName' => $this->applicationName . 'Application',
            'applicationDatabaseName' => $lowerAppName,
            'currentUserName' => $gitInfo['user.name'],
            'currentUserEmail' => $gitInfo['user.email']
        );
    }

    /**
     * Generates an application based on a template
     * @param string $template
     * @param OutputInterface $output
     */
    private function generateWithTemplate($template, OutputInterface $output) {
        $fs = new Filesystem();
        $templateSource = $this->getTemplateFolder($template);
        $finder = new Finder();
        $finder->in($templateSource);
        $renderContext = $this->createRenderContext();
        foreach ($finder as $item) {
            $relativeName = str_replace(DIRECTORY_SEPARATOR, '/', $item->getRelativePathName());
            $dest = $this->workFolder . '/' . $this->getDestFilename($relativeName);
            if ($item->isDir()) {
                $fs->ensureFolder($dest);
            } else {
                if (in_array($relativeName, $this->renderTable)) {
                    $output->writeln("Rendering " . $relativeName);
                    render_php_template($item, $renderContext, $dest);
                } else {
                    $output->writeln("Processing " . $relativeName);
                    $fs->copy($item, $dest);
                }
            }
        }
    }

    /**
     * Get the correct destination file name by looping at the rename table
     * @param string $name name of the file to lookup
     * @return string
     */
    private function getDestFilename($name) {
        if (isset($this->renameTable[$name])) {
            return $this->renameTable[$name];
        } else {
            return $name;
        }
    }

    /**
     * Get the template folder based on a given template name
     * @param string $template
     * @return string
     */
    private function getTemplateFolder($template) {
        return realpath($this->templatesFolder . '/' . $this->templates[$template]);
    }

    /**
     * Checks if a given template exists
     * @param string $template
     * @return boolean
     */
    private function checkTemplate($template) {
        return array_key_exists($template, $this->templates);
    }

    /**
     * Find the names of currently availble templates
     * @return type
     */
    private function getTemplateNames() {
        $result = [];
        $finder = new Finder();
        $finder->files()
                ->directories()
                ->depth('==0')
                ->in($this->templatesFolder);
        foreach ($finder as $folder) {
            $result[strtolower($folder->getFilename())] = $folder->getFilename();
        }
        return $result;
    }

    /**
     * Checks if the working folder is empty
     * @return boolean
     */
    private function checkSanity() {
        $finder = new Finder();
        $finder->files()
                ->in($this->workFolder)
                ->ignoreVCS(true)
                ->ignoreDotFiles(true)
                ->exclude('vendor')
                ->notName("composer.*");
        return $finder->count() === 0;
    }

    /**
     * Get the current git user information. We need this to extract
     * the user name and email to put into the generated files
     * @return mixed
     */
    private function getCurrentGitUser() {
        $p = new Process('git config --list');
        $p->enableOutput();
        $result = [];
        try {
            $p->mustRun();
            $lines = explode("\n", trim($p->getOutput()));
            foreach ($lines as $line) {
                $ar = explode('=', trim($line));
                if (count($ar) === 2) {
                    $result[$ar[0]] = $ar[1];
                }
            }
            return $result;
        } catch (\Exception $e) {
            $user  = get_current_user();
            return array(
                'user.name' => $user,
                'user.email' => $user
            );
        }
    }

}
