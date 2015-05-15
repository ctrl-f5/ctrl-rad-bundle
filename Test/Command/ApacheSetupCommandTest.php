<?php

namespace Ctrl\RadBundle\Test\Command;

use Ctrl\RadBundle\Command\ApacheSetupCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ApacheSetupCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test_execute()
    {
        $application = new Application();
        $application->add(new ApacheSetupCommand());

        $command = $application->find('ctrl:apache:install');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), 'domain' => 'test'));

        // status code is only available in 2.4 or higher
        if (method_exists($commandTester, 'getStatusCode')) {
            $this->assertSame(0, $commandTester->getStatusCode());
        }

        $output = $commandTester->getDisplay(true);

        $this->assertContains('ServerName test', $output);
        $this->assertContains('ServerAlias www.test', $output);
        $this->assertContains("127.0.0.1\ttest", $output);
    }
}