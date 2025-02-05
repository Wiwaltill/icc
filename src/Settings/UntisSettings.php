<?php

namespace App\Settings;

class UntisSettings extends AbstractSettings {
    public function setSubjectOverrides(array $overrides): void {
        $this->setValue('untis.import.subject_overrides', $overrides);
    }

    public function getSubjectOverrides(): array {
        return $this->getValue('untis.import.subject_overrides', [ ]);
    }

    public function setWeekMap(array $weeks): void {
        $this->setValue('untis.import.week_map', $weeks);
    }

    public function getWeekMap(): array {
        return $this->getValue('untis.import.week_map', [ ]);
    }

    public function setSubstitutionDays(int $days): void {
        $this->setValue('untis.import.substitution.days', $days);
    }

    public function getSubstitutionDays(): int {
        return $this->getValue('untis.import.substitution.days', 7);
    }

    public function isSubstitutionCollapsingEnabled(): bool {
        return $this->getValue('untis.import.substitution.collapse', true);
    }

    public function setSubstitutionCollapsingEnabled(bool $value) {
        $this->setValue('untis.import.substitution.collapse', $value);
    }

    public function setAlwaysImportExamWriters(bool $alwaysImport): void {
        $this->setValue('untis.import.exams.always_import_students', $alwaysImport);
    }

    public function alwaysImportExamWriters(): bool {
        return $this->getValue('untis.import.exams.always_import_students', false);
    }

    public function setIgnoreStudentOptionRegExp(?string $regExp): void {
        $this->setValue('untis.import.exams.ignore_import_students_options_regexp', $regExp);
    }

    public function getIgnoreStudentOptionRegExp(): ?string {
        return $this->getValue('untis.import.exams.ignore_import_students_options_regexp', null);
    }
}