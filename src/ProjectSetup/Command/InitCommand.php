<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\ProjectSetup\Command;

use Blend\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * InitCommand helps to initialize a new BlendEngine Application.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class InitCommand extends Command
{
    /**
     * Points to the workfolder getcwd.
     *
     * @var string
     */
    private $workFolder;

    /**
     * Points to the folder where the templates are located.
     *
     * @var string
     */
    private $templatesFolder;

    /**
     * List of available templates.
     *
     * @var array
     */
    private $templates;

    /**
     * Mapping table that is used to rename template files.
     *
     * @var array
     */
    private $renameTable;

    /**
     * Mapping table that is used to generate files based on a PHP template.
     *
     * @var array
     */
    private $renderTable;

    /**
     * The name of the application to generate.
     *
     * @var string
     */
    private $applicationName;

    /**
     * Flag to generate a testable Application.
     *
     * @var type
     */
    private $testable;

    /**
     * Mapping table to be used when rendering files.
     *
     * @var array
     */
    private $renderContext;

    /**
     * Indicates if compass is installed.
     *
     * @var bool
     */
    private $hasCompass;

    /**
     * Indicates if PostgreSQL is installed.
     *
     * @var bool
     */
    private $hasPostgres;

    /**
     * The current version of installed BlendEngine.
     *
     * @var string
     */
    private $installedSDKVersion;

    protected function configure()
    {
        parent::configure();

        $this->templatesFolder = realpath(__DIR__.'/../Resources/Templates');
        $this->workFolder = getcwd();
        $this->applicationName = str_identifier((new \SplFileInfo($this->workFolder))->getBasename());
        $this->templates = $this->getTemplateNames();
        $this->installedSDKVersion = $this->getInstalledBlendEngineVersion();

        $this->setName('init')
                ->setDescription('Initializes a new BlendEngine project in ['.$this->workFolder.']')
                ->addOption('template', 't', InputOption::VALUE_OPTIONAL, 'Name of the template to generate this project ('.implode(',', $this->templates).')', 'Basic')
                ->addOption('appname', 'a', InputOption::VALUE_OPTIONAL, 'Name of the application to generate', $this->applicationName)
                ->addOption('testable', 'x', InputOption::VALUE_OPTIONAL, 'Expose the application internals for testing. (Internal use only)', false);
    }

    /**
     * Creates a rendering context array to be used when rendering
     * template files.
     *
     * @return array
     */
    private function createRenderContext()
    {
        $lowerAppName = strtolower($this->applicationName);
        $gitInfo = $this->getCurrentGitUser();

        return array(
            'testable' => $this->testable,
            'applicationName' => $this->applicationName,
            'applicationScriptName' => $lowerAppName,
            'applicationNamespace' => $this->applicationName,
            'applicationPackageName' => get_current_user().'/'.$lowerAppName,
            'applicationCommandClassName' => $this->applicationName.'Application',
            'applicationClassName' => $this->applicationName.'Application',
            'applicationDatabaseName' => $lowerAppName,
            'currentUserName' => $gitInfo['user.name'],
            'currentUserEmail' => $gitInfo['user.email'],
        );
    }

    /**
     * Prepare interval variables.
     */
    private function prepareTablesAndContext()
    {
        $applicationCommandClassName = $applicationScriptName = null;
        $lowerName = strtolower($this->applicationName);
        $this->renderContext = $this->createRenderContext();
        extract($this->renderContext);

        $this->renameTable = array(
            'gitignore' => '.gitignore',
            'config/gitignore' => 'config/.gitignore',
            'resources/gitignore' => 'resources/.gitignore',
            'resources/sass/app.scss' => 'resources/sass/'.$applicationScriptName.'.scss',
            'bin/app' => 'bin/'.$lowerName,
            'bin/app.bat' => 'bin/'.$lowerName.'.bat',
            'bin/app.php' => 'bin/'.$lowerName.'.php',
            'src/Console/Application.php' => 'src/Console/'.$applicationCommandClassName.'.php',
            'src/Application.php' => 'src/'.$applicationClassName.'.php',
            'web/app_dev.php' => 'web/'.$lowerName.'_dev.php',
            'web/app.php' => 'web/'.$lowerName.'.php',
            'src/Runtime.php' => 'src/'.$applicationName.'Runtime.php',
        );

        $this->renderTable = array(
            'phpunit.xml.dist',
            'tests/README',
            'config/config.json',
            'bin/app',
            'bin/app.bat',
            'bin/app.php',
            'composer.json',
            'src/Console/Application.php',
            'src/Application.php',
            'src/Runtime.php',
            'web/app_dev.php',
            'web/app.php',
            'src/Console/Command/PublishCommand.php',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getApplication()->getLongVersion()."\n");
        if ($this->checkSanity()) {
            $this->hasCompass = $this->checkForCompass($output);
            $this->hasPostgres = $this->checkForPostgreSQL($output);

            $template = strtolower($input->getOption('template'));
            if ($this->checkTemplate($template)) {
                $this->applicationName = $input->getOption('appname');
                $this->testable = $input->getOption('testable');
                $output->writeln('<info>Generating '.$this->applicationName.' :)</info>');
                $this->prepareTablesAndContext();
                $this->generateWithTemplate($template, $output);
                $this->runCompass($output);
                $this->createVarFolders();
                $this->setSDKVersion();

                $output->writeln(
                        array('', '<info>Thank you for using BlendEngine :)</info>', '<info>Enjoy!</info>')
                );
            } else {
                $output->writeln('<error>The requested template ['.$template.'] does not exist!</error>');
            }
        } else {
            $output->writeln('<error>Looks like the '.$this->workFolder.' is not empty!</error>');
        }
    }

    private function createVarFolders()
    {
        $fs = new Filesystem();
        $folders = array(
            $this->workFolder.'/var/cache',
            $this->workFolder.'/var/log',
            $this->workFolder.'/var/session',
        );
        foreach ($folders as $folder) {
            $fs->ensureFolder($folder, 0777);
        }
    }

    /**
     * Run compass to compile the default stylesheets.
     *
     * @param type $output
     */
    private function runCompass($output)
    {
        $p = new Process('compass compile');
        $p->setWorkingDirectory($this->workFolder.'/resources');
        try {
            $output->writeln('<info>Compiling default sass files.</info>');
            $p->mustRun();
        } catch (\Exception $e) {
            $output->writeln('<warn>'.$e->getMessage().'</warn>');
        }
    }

    /**
     * Generates an application based on a template.
     *
     * @param string          $template
     * @param OutputInterface $output
     */
    private function generateWithTemplate($template, OutputInterface $output)
    {
        $fs = new Filesystem();
        $templateSource = $this->getTemplateFolder($template);
        $finder = new Finder();
        $finder->in($templateSource);
        foreach ($finder as $source) {
            $relativeName = str_replace(DIRECTORY_SEPARATOR, '/', $source->getRelativePathName());
            $dest = $this->workFolder.'/'.$this->getDestFilename($relativeName);
            if ($source->isDir()) {
                $fs->ensureFolder($dest);
            } else {
                $this->processFile($fs, $relativeName, $source, $dest, $output);
            }
        }
    }

    /**
     * Render of copy the file to the correct location.
     *
     * @param Filesystem      $fs
     * @param string          $relativeName
     * @param SplFileInfo     $source
     * @param string          $dest
     * @param OutputInterface $output
     */
    private function processFile($fs, $relativeName, $source, $dest, OutputInterface $output)
    {
        if (in_array($relativeName, $this->renderTable)) {
            $output->writeln('Rendering '.$relativeName);
            render_php_template($source, $this->renderContext, $dest);
            if ($relativeName === 'bin/app') {
                chmod($dest, 0750);
            }
        } else {
            $output->writeln('Processing '.$relativeName);
            $fs->copy($source, $dest);
        }
    }

    /**
     * Get the correct destination file name by looping at the rename table.
     *
     * @param string $name name of the file to lookup
     *
     * @return string
     */
    private function getDestFilename($name)
    {
        if (isset($this->renameTable[$name])) {
            return $this->renameTable[$name];
        } else {
            return $name;
        }
    }

    /**
     * Get the template folder based on a given template name.
     *
     * @param string $template
     *
     * @return string
     */
    private function getTemplateFolder($template)
    {
        return realpath($this->templatesFolder.'/'.$this->templates[$template]);
    }

    /**
     * Checks if a given template exists.
     *
     * @param string $template
     *
     * @return bool
     */
    private function checkTemplate($template)
    {
        return array_key_exists($template, $this->templates);
    }

    /**
     * Find the names of currently available templates.
     *
     * @return type
     */
    private function getTemplateNames()
    {
        $result = array();
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
     * Checks if the working folder is empty.
     *
     * @return bool
     */
    private function checkSanity()
    {
        $finder = new Finder();
        $finder->files()
                ->in($this->workFolder)
                ->ignoreVCS(true)
                ->ignoreDotFiles(true)
                ->exclude('vendor')
                ->notName('composer.*')
                ->notName('reset-project.*');

        return $finder->count() === 0;
    }

    /**
     * Get the current version of the installed BlendEngine.
     *
     * @return string
     */
    private function getInstalledBlendEngineVersion()
    {
        $fname = $this->workFolder.'/composer.json';
        if (file_exists($fname) && is_file($fname)) {
            $config = json_decode(file_get_contents($fname), true);
            if (isset($config['require']['blendsdk/blendengine'])) {
                return $config['require']['blendsdk/blendengine'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Set the correct SDK version in the newly created/initialized project.
     */
    private function setSDKVersion()
    {
        if ($this->installedSDKVersion !== null) {
            $fname = $this->workFolder.'/composer.json';
            $config = json_decode(file_get_contents($fname), true);
            $config['require']['blendsdk/blendengine'] = $this->installedSDKVersion;
            file_put_contents($fname, json_encode($config, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Get the current git user information. We need this to extract
     * the user name and email to put into the generated files.
     *
     * @return mixed
     */
    private function getCurrentGitUser()
    {
        $p = new Process('git config --list');
        $p->enableOutput();
        $result = array();
        $user = get_current_user();
        try {
            $p->mustRun();
            $lines = explode("\n", trim($p->getOutput()));
            foreach ($lines as $line) {
                $ar = explode('=', trim($line));
                if (count($ar) === 2) {
                    $result[$ar[0]] = $ar[1];
                }
            }
            if (!isset($result['user.name'])) {
                $result['user.name'] = $user;
            }
            if (!isset($result['user.email'])) {
                $result['user.email'] = $user;
            }

            return $result;
        } catch (\Exception $e) {
            return array(
                'user.name' => $user,
                'user.email' => $user,
            );
        }
    }

    /**
     * Check to see if ruby compass is installed.
     *
     * @param OutputInterface $output
     */
    private function checkForCompass(OutputInterface $output)
    {
        $p = new Process('compass --version');
        try {
            $p->mustRun();
            $output->writeln('<info>Compass is installed on your system, great.</info>');

            return true;
        } catch (\Exception $e) {
            $log = $e->getMessage();
            $output->writeln(array(
                '',
                '<warn>WARNING: Compass could not be verified on your system!</warn>',
                '<warn>Perhaps it is not installed or your PATH settings are not correct.</warn>',
                '<warn>Without compass you will not be able to compile the style sheets.</warn>',
                '<warn>Check out http://compass-style.org/install for more information.</warn>',
                '',
            ));

            return false;
        }
    }

    /**
     * Check to see if ruby compass is installed.
     *
     * @param OutputInterface $output
     */
    private function checkForPostgreSQL(OutputInterface $output)
    {
        $p = new Process('php -m');
        try {
            $p->mustRun();
            $lines = explode("\n", trim($p->getOutput()));
            if (in_array('pdo_pgsql', $lines)) {
                $output->writeln('<info>PostgreSQL is installed on your system, great.</info>');

                return true;
            } else {
                $output->writeln(array(
                    '',
                    '<warn>WARNING: Could not verify your PostgreSQL installation!</warn>',
                    '<warn>Did you forget to install the pdo_psql extension?.</warn>',
                    '',
                ));

                return false;
            }
        } catch (\Exception $e) {
            $output->writeln(array(
                '',
                '<warn>WARNING: Could not verify your PostgreSQL installation!</warn>',
                '<warn>The PHP command line utility did not run correctly.</warn>',
                '',
            ));

            return false;
        }
    }
}
