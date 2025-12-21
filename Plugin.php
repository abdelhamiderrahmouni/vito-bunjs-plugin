<?php

namespace App\Vito\Plugins\AbdelhamidErrahmouni\VitoBunJsPlugin;

use App\DTOs\DynamicField;
use App\Plugins\AbstractPlugin;
use App\Plugins\RegisterServiceType;
use App\Plugins\RegisterSiteType;
use App\Plugins\RegisterViews;
use App\Plugins\RegisterWorkflowAction;
use App\Vito\Plugins\AbdelhamidErrahmouni\VitoBunJsPlugin\WorkflowActions\CreateBunJsSite;

class Plugin extends AbstractPlugin
{
    protected string $name = 'Bun.js Plugin';

    protected string $description = 'Add Bun.js to your Vito Deploy v3 project';

    public function boot(): void
    {
        RegisterViews::make('vitodeploy-bunjs')
            ->path(__DIR__.'/resources/views')
            ->register();

        RegisterServiceType::make(Services\BunJS::id())
            ->type(Services\BunJS::type())
            ->label('Bun.js')
            ->handler(Services\BunJS::class)
            ->versions([
                '1.3.4',
                '1.2.23',
                '1.1.45',
                '1.0.36',
            ])
            ->register();

        RegisterSiteType::make(SiteTypes\BunJs::id())
            ->label('Bun.js')
            ->handler(SiteTypes\BunJs::class)
            ->form(\App\DTOs\DynamicForm::make([
                DynamicField::make('source_control')
                    ->component()
                    ->label('Source Control'),

                DynamicField::make('port')
                    ->text()
                    ->label('Port')
                    ->placeholder('3000')
                    ->description('On which port your app will be running'),

                DynamicField::make('repository')
                    ->text()
                    ->label('Repository')
                    ->placeholder('organization/repository')
                    ->description('Your package.json must have start and build scripts'),

                DynamicField::make('branch')
                    ->text()
                    ->label('Branch')
                    ->default('main'),
            ]))
            ->register();

        RegisterWorkflowAction::make('create-bunjs-site')
            ->label('Create Bun.js Site')
            ->category('site')
            ->handler(CreateBunJsSite::class)
            ->register();
    }
}
