<?php

namespace App\Controller\Admin;

use App\Entity\StudyGroupMembership;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StudyGroupMembershipCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StudyGroupMembership::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('StudyGroupMembership')
            ->setEntityLabelInPlural('StudyGroupMembership')
            ->setSearchFields(['type', 'id']);
    }

    public function configureFields(string $pageName): iterable
    {
        $type = TextField::new('type');
        $studyGroup = AssociationField::new('studyGroup');
        $student = AssociationField::new('student');
        $id = IntegerField::new('id', 'ID')->hideOnForm();

        return [$id, $type, $studyGroup, $student];
    }
}
