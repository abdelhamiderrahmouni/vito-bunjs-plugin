<?php

namespace App\Vito\Plugins\AbdelhamidErrahmouni\VitoBunJsPlugin\WorkflowActions;

use App\WorkflowActions\Site\CreateSite;

class CreateBunJsSite extends CreateSite
{
    public function inputs(): array
    {
        return array_merge(parent::inputs(), [
            'type' => 'bunjs',
            'port' => 'Port to run the Bun.js application on, example: 3000',
            'source_control' => 'Source control ID',
            'repository' => 'organization/repository',
            'branch' => 'Branch to deploy, example: main',
        ]);
    }
}
