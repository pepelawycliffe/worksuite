<?php

use App\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InvoiceClientSearchFix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = \App\Company::all();
        foreach($companies as $company)
        {
            $invoices = Invoice::with(
                [
                    'project' => function ($q) {
                        $q->withTrashed();
                        $q->select('id', 'project_name', 'client_id');
                    },
                    'project.client', 'client', 'estimate', 'estimate.client'
                ]
            )->whereNull('invoices.client_id')
                ->where('company_id', $company->id)
                ->get();

            foreach($invoices as $invoice){
                if ($invoice->project && $invoice->project->client) {
                    $invoice->client_id = $invoice->project->client->user_id;
                } else if ($invoice->estimate && $invoice->estimate->client) {
                    $invoice->client_id = $invoice->estimate->client->id;
                } else {
                    $invoice->client_id = null;
                }
                $invoice->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
