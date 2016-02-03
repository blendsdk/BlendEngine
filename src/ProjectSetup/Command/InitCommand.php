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
    private $renameTable;

    protected function configure() {
        parent::configure();

        $this->templatesFolder = realpath(__DIR__ . '/../Resources/Templates');
        $this->workFolder = getcwd();
        $this->renameTable = array(
            'config/gitignore' => 'config/.gitignore'
        );

        $this->templates = $this->getTemplateNames();
        $this->setName('project:init')
                ->setDescription('Initializes a new BlendEngine project in [' . $this->workFolder . ']')
                ->addOption('template', 't', InputOption::VALUE_OPTIONAL, 'Name of the template to generate this project (' . implode(',', $this->templates) . ')', 'Basic');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getApplication()->getLongVersion() . "\n");
        if ($this->checkSanity()) {
            $template = strtolower($input->getOption('template'));
            if ($this->checkTemplate($template)) {
                $output->writeln("<info>Everything looks okay. We are good to go :)</info>");
                $this->generateWithTemplate($template, $output);
            } else {
                $output->writeln("<error>The requested template [" . $template . '] does not exist!</error>');
            }
        } else {
            $output->writeln("<error>Looks like the " . $this->workFolder . ' is not empty!</error>');
        }
    }

    private function generateWithTemplate($template, OutputInterface $output) {
        $fs = new Filesystem();
        $templateSource = $this->getTemplateFolder($template);
        $finder = new Finder();
        $finder->in($templateSource);
        foreach ($finder as $item) {
            $dest = $this->workFolder . '/' . $this->getDestFilename($item->getRelativePathName());
            $output->writeln("Creating " . $item->getRelativePathName());
            if ($item->isDir()) {
                $fs->ensureFolder($dest);
            } else {
                $fs->copy($item, $dest);
            }
        }
    }

    private function getDestFilename($name) {
        if (isset($this->renameTable[$name])) {
            return $this->renameTable[$name];
        } else {
            return $name;
        }
    }

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

}
