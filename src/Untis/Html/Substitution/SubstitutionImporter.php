<?php

namespace App\Untis\Html\Substitution;

use App\Import\AbsencesImportStrategy;
use App\Import\FreeTimespanImportStrategy;
use App\Import\Importer;
use App\Import\InfotextsImportStrategy;
use App\Import\SubstitutionsImportStrategy;
use App\Request\Data\AbsenceData;
use App\Request\Data\AbsencesData;
use App\Request\Data\FreeLessonTimespanData;
use App\Request\Data\FreeLessonTimespansData;
use App\Request\Data\InfotextData;
use App\Request\Data\InfotextsData;
use App\Request\Data\SubstitutionData;
use App\Request\Data\SubstitutionsData;
use App\Settings\UntisSettings;

class SubstitutionImporter {
    private Importer $importer;
    private SubstitutionsImportStrategy $substitutionStrategy;
    private InfotextsImportStrategy $infotextStrategy;
    private FreeTimespanImportStrategy $freeTimespanStrategy;
    private AbsencesImportStrategy $absenceStrategy;
    private SubstitutionReader $reader;
    private UntisSettings $untisSettings;

    public function __construct(Importer                   $importer, SubstitutionsImportStrategy $substitutionStrategy, InfotextsImportStrategy $infotextStrategy,
                                FreeTimespanImportStrategy $freeTimespanStrategy, AbsencesImportStrategy $absenceStrategy, SubstitutionReader $reader,
                                UntisSettings              $untisSettings) {
        $this->importer = $importer;
        $this->substitutionStrategy = $substitutionStrategy;
        $this->infotextStrategy = $infotextStrategy;
        $this->freeTimespanStrategy = $freeTimespanStrategy;
        $this->absenceStrategy = $absenceStrategy;
        $this->reader = $reader;
        $this->untisSettings = $untisSettings;
    }

    public function import(string $html, bool $suppressNotifications) {
        $result = $this->reader->readHtml($html);
        $context = $result->getDateTime()->format('Y-m-d');

        $this->importInfotexts($result, $context);
        $this->importAbsences($result, $context);
        $this->importFreeTimespans($result, $context);
        $this->importSubstitutions($result, $context, $suppressNotifications);
    }

    private function importInfotexts(SubstitutionResult $result, string $context): void {
        $data = new InfotextsData();
        $data->setContext($context);

        $infotexts = [ ];

        foreach($result->getInfotexts() as $infotext) {
            $infotexts[] = (new InfotextData())
                ->setDate($result->getDateTime())
                ->setContent($infotext->getContent());
        }

        $data->setInfotexts($infotexts);
        $this->importer->replaceImport($data, $this->infotextStrategy);
    }

    private function importAbsences(SubstitutionResult $result, string $context): void {
        $data = new AbsencesData();
        $data->setContext($context);

        $absences = [ ];

        foreach($result->getAbsences() as $absence) {
            $absences[] = (new AbsenceData())
                ->setDate($result->getDateTime())
                ->setLessonStart($absence->getLessonStart())
                ->setLessonEnd($absence->getLessonEnd())
                ->setObjective($absence->getObjective())
                ->setType($absence->getObjectiveType()->getValue());
        }

        $data->setAbsences($absences);
        $this->importer->replaceImport($data, $this->absenceStrategy);
    }

    private function importFreeTimespans(SubstitutionResult $result, string $context): void {
        $data = new FreeLessonTimespansData();
        $data->setContext($context);

        $timeSpans = [ ];

        foreach($result->getFreeLessons() as $freeLesson) {
            $timeSpans[] = (new FreeLessonTimespanData())
                ->setStart($freeLesson->getLessonStart())
                ->setEnd($freeLesson->getLessonEnd())
                ->setDate($result->getDateTime());
        }

        $data->setFreeLessons($timeSpans);
        $this->importer->replaceImport($data, $this->freeTimespanStrategy);
    }

    private function importSubstitutions(SubstitutionResult $result, string $context, bool $suppressNotifications): void {
        $data = new SubstitutionsData();
        $data->setContext($context);
        $data->setSuppressNotifications($suppressNotifications);

        $substitutions = [ ];

        $overrideMap = $this->getSubjectOverrideMap();

        foreach($result->getSubstitutions() as $htmlSubstitution) {
            $substitution = new SubstitutionData();

            $substitution->setId((string)$htmlSubstitution->getId());
            $substitution->setDate($htmlSubstitution->getDate());
            $substitution->setLessonStart($htmlSubstitution->getLessonStart());
            $substitution->setLessonEnd($htmlSubstitution->getLessonEnd());
            $substitution->setStartsBefore($htmlSubstitution->isSupervision());
            $substitution->setRooms($htmlSubstitution->getRooms());
            $substitution->setReplacementRooms($htmlSubstitution->getReplacementRooms());
            $substitution->setTeachers($htmlSubstitution->getTeachers());
            $substitution->setReplacementTeachers($htmlSubstitution->getReplacementTeachers());
            $substitution->setSubject($this->getSubject($htmlSubstitution->getSubject(), $overrideMap));
            $substitution->setReplacementSubject($this->getSubject($htmlSubstitution->getReplacementSubject(), $overrideMap));
            $substitution->setGrades($htmlSubstitution->getGrades());
            $substitution->setReplacementGrades($htmlSubstitution->getReplacementGrades());
            $substitution->setType($htmlSubstitution->getType());
            $substitution->setText($htmlSubstitution->getRemark());

            $substitutions[] = $substitution;
        }

        $data->setSubstitutions($substitutions);
        $this->importer->import($data, $this->substitutionStrategy);
    }

    private function getSubject(?string $untisSubject, array $map): ?string {
        if(empty($untisSubject)) {
            return null;
        }

        if(array_key_exists($untisSubject, $map)) {
            return $map[$untisSubject];
        }

        return $untisSubject;
    }

    private function getSubjectOverrideMap(): array {
        $map = [ ];

        foreach($this->untisSettings->getSubjectOverrides() as $override) {
            $map[$override['untis']] = $override['override'];
        }

        return $map;
    }
}