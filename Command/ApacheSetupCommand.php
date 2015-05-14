<?php

namespace Ctrl\RadBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ApacheSetupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ctrl:apache:install')
            ->setDescription('install apache vhost and add domain to hosts file (requires root)')
            ->addArgument(
                'domain',
                InputArgument::REQUIRED,
                'whats the domain name you want to use?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<error> you are about to do some stuff that requires sudo! </error>");

        $rootDir        = getcwd();
        /** @var DialogHelper $dialog */
        $dialog         = $this->getHelper('dialog');
        $domain         = $input->getArgument('domain');
        $vhost          = $this->getVhostContent($domain, $rootDir);
        $vhostFile      = "/etc/apache2/sites-available/$domain.conf";
        $hostsFile      = "/etc/hosts";
        $hosts          = "127.0.0.1\t$domain";

        $output->writeln("<info>We are going generate the following vhost file</info>");
        $output->writeln("----- $vhostFile");
        $output->writeln($vhost);
        $output->writeln("-----");
        $output->writeln("");
        $output->writeln("<info>the following line will be appended to your hosts file</info>");
        $output->writeln("----- $hostsFile ");
        $output->writeln($hosts);
        $output->writeln("-----");
        $output->writeln("");
        $output->writeln("<info>the following commands will then be executed</info>");
        $output->writeln("-----");
        $output->writeln("sudo a2ensite $vhostFile");
        $output->writeln("sudo apache2 reload");
        $output->writeln("-----");
        $output->writeln("");

        if (!$input->getOption('no-interaction')) {
            //$confirmed = $dialog->askConfirmation($output, "<question>please confirm, does this look good?</question> (y/N)", false);
            //if (!$confirmed) return 0;
        }

        $output->writeln("we're not actually gonna do all that right now, feel free to copy and execute it manually");
        //$this->writeFiles($vhostFile, $vhost, $hostsFile, $hosts);

        return 0;
    }

    protected function writeFiles($vhostFile, $vhost, $hostsFile, $hosts)
    {
        $hosts = PHP_EOL . $hosts;
        if (!file_exists($vhostFile)) {
            exec("echo '$vhost' > $vhostFile");
        }
        exec("echo '$hosts' >> $hostsFile");
    }

    protected function getVhostContent($domain, $rootDir)
    {
        $template = __DIR__ . '/../Resources/installer/vhost.template';
        $vhost = file_get_contents($template);

        if (!$vhost) {
            throw new \RuntimeException(
                sprintf('vhost template file could not be read %s', $template)
            );
        }

        return trim(str_replace(
            array(
                '__DIR__',
                '__DOMAIN__',
            ),
            array(
                $rootDir,
                $domain,
            ),
            $vhost
        ));
    }
}