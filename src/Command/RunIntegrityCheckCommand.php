<?php

namespace App\Command;

use App\Messenger\RunIntegrityCheckMessage;
use App\Repository\StudentRepositoryInterface;
use App\Section\SectionResolverInterface;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCronJob('0 1 * * *')]
#[AsCommand('app:book:integrity_check:queue', description: 'Veranlasst einen (asynchronen) Integritätscheck.')]
class RunIntegrityCheckCommand extends Command {
    public function __construct(private readonly MessageBusInterface $messageBus, private readonly StudentRepositoryInterface $studentRepository,
                                private readonly SectionResolverInterface $sectionResolver, private readonly bool $isEnabled, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        if($this->isEnabled === false) {
            $io->error('Der Parameter ASYNC_CHECKS muss auf true gesetzt werden.');
            return Command::SUCCESS;
        }

        $section = $this->sectionResolver->getCurrentSection();

        if($section === null) {
            $io->error('Es gibt aktuell kein Schuljahresabschnitt zu prüfen');
            return Command::FAILURE;
        }

        foreach($this->studentRepository->findAllBySection($section) as $student) {
            $message = new RunIntegrityCheckMessage($student->getId(), $section->getStart(), $section->getEnd());
            $this->messageBus->dispatch($message);
        }

        return Command::SUCCESS;
    }
}