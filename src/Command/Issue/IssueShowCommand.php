<?php

/**
 * This file is part of Gush package.
 *
 * (c) 2013-2014 Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Command\Issue;

use Gush\Command\BaseCommand;
use Gush\Feature\GitRepoFeature;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Shows an issue
 */
class IssueShowCommand extends BaseCommand implements GitRepoFeature
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('issue:show')
            ->setDescription('Shows given issue')
            ->addArgument('issue_number', InputArgument::REQUIRED, 'Issue number')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command shows issue details for either the current or the given organization
and repo:

    <info>$ gush %command.name% 60</info>

EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $issueNumber = $input->getArgument('issue_number');

        $adapter = $this->getIssueTracker();
        $issue = $adapter->getIssue($issueNumber);

        $output->writeln(
            sprintf(
                PHP_EOL.'Issue #%s (%s): by %s [%s]',
                $issue['number'],
                $issue['state'],
                $issue['user'],
                $issue['assignee']
            )
        );

        if ($issue['pull_request']) {
            $output->writeln('Type: Pull Request');
        } else {
            $output->writeln('Type: Issue');
        }
        $output->writeln('Milestone: '.$issue['milestone']);
        if ($issue['labels'] > 0) {
            $output->writeln('Labels: '.implode(', ', $issue['labels']));
        }
        $output->writeln(
            [
                'Title: '.$issue['title'],
                'Link: '.$issue['url'],
                '',
                $issue['body'],
            ]
        );

        return self::COMMAND_SUCCESS;
    }
}
