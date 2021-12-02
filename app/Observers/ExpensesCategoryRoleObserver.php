<?php
namespace App\Observers;

use App\ExpensesCategoryRole;

class ExpensesCategoryRoleObserver
{

    public function saving(ExpensesCategoryRole $categoryRole)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $categoryRole->company_id = company()->id;
        }
    }

}
