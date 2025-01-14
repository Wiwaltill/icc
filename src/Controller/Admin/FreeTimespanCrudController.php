<?php

namespace App\Controller\Admin;

use App\Entity\FreeTimespan;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class FreeTimespanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FreeTimespan::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('FreeTimespan')
            ->setEntityLabelInPlural('FreeTimespan')
            ->setSearchFields(['start', 'end', 'id']);
    }

    public function configureFields(string $pageName): iterable
    {
        $date = DateTimeField::new('date');
        $start = IntegerField::new('start');
        $end = IntegerField::new('end');
        $id = IntegerField::new('id', 'ID')->hideOnForm();

        return [$id, $date, $start, $end ];
    }
}
