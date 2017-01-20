<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Console\Command;

use Blend\Component\Support\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * PublishCommand creates a new release version of a product
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class PublishCommand extends Command
{
    /**
     * @var Blend\Component\Support\Version
     */
    protected $version;
    protected $branch;

    abstract protected function publish(InputInterface $input, OutputInterface $output);

    public function __construct($name = null)
    {
        $this->version = new Version($this->getLatestTagVersion());
        parent::__construct($name);
    }

    private function getLatestTagVersion()
    {
        $data = explode("\n", trim(`git tag`));
        if (!empty($data)) {
            $v = '0.0.0';
            foreach ($data as $item) {
                $item = str_replace('v', '', $item);
                if (version_compare($item, $v, '>')) {
                    $v = $item;
                }
            }

            return 'v' . $v;
        } else {
            return null;
        }
    }

    protected function configure()
    {
        $this->setName('publish')
                ->setDescription('Creates a new release version: defaults to build ' . ($this->version->getBuild() + 1))
                ->addOption('bump', 'b', InputOption::VALUE_REQUIRED, 'The version part', 'build');
    }

    protected function bumpVersion(InputInterface $input, OutputInterface $output)
    {
        $versionPart = $this->getAssetVersionPart($input->getOption('bump', 'build'));
        switch ($versionPart) {
            case 'major':
                $this->version->bumpMajor();
                break;
            case 'minor':
                $this->version->bumpMinor();
                break;
            case 'build':
                $this->version->bumpBuild();
                break;
            case 'beta':
            case 'alpha':
                $this->version->serReleaseTag($versionPart);
                break;
            default:
                $this->version->bumpBuild();
        }
        $output->writeln('Bumping to: ' . $this->version->getVersion());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Current version: ' . $this->version->getVersion());
        $this->branch = $this->getCurrentGitBranch();
        $this->bumpVersion($input, $output);
        if ($this->branch === 'master') {
            if ($this->isBranchClean()) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion('Continue with this action?', false);

                if ($helper->ask($input, $output, $question)) {
                    $this->createNewGitTag($output);
                    $this->pushToRepository($output);
                    $this->publish($input, $output);
                }
            } else {
                throw new \Exception("$this->branch branch is not clean yet!");
            }
        } else {
            throw new \Exception("Cannot relase from the $this->branch branch, only master is allowed!");
        }
    }

    private function pushToRepository(OutputInterface $output)
    {
        $output->writeln('Pushing to repository');

        return `git push origin master --follow-tags`;
    }

    private function createNewGitTag(OutputInterface $output)
    {
        $version = $this->version->getVersion();
        $output->writeln('Tagging to: ' . $version);

        return `git tag -a $version -m"Release version $version"`;
    }

    private function getAssetVersionPart($value)
    {
        $value = strtolower($value);
        $allowed = array('major', 'minor', 'build', 'beta', 'alpha');
        if (!in_array($value, $allowed)) {
            throw new \Exception("Invalid bump value $value. Only major, minor, build, or relase are allowed!");
        }

        return $value;
    }

    private function getCurrentGitBranch()
    {
        return trim(`git rev-parse --abbrev-ref HEAD`);
    }

    private function isBranchClean()
    {
        $result = trim(`git status --porcelain`);

        return empty($result);
    }
}
