<?php

namespace People\Providers;

use Illuminate\Support\Facades\View;
use People\Fields\People as PeopleField;
use People\Fields\Partials\People as PeopleBuilderField;
use People\View\Composers\People as PeopleComposer;
use Log1x\AcfComposer\AcfComposer;

class PeopleServiceProvider implements Provider
{
    protected function providers()
    {
        return [
            RegisterAssets::class,
            RegisterPostType::class,
            PeopleOrder::class,
        ];
    }

    protected function registerFields()
    {
        $composer = app(AcfComposer::class);
        $people = new PeopleField($composer);
        $people->compose();
    }

    protected function registerLayouts()
    {
        add_filter('acf_page_builder_before_build', function ($builder) {
            $fields = $builder->getFields();

            $flexible = null;

            foreach ($fields as $field) {
                if ($field->getName() === 'builder') {
                    $flexible = $field;
                    break;
                }
            }

            if ($flexible) {
                $composer = app(AcfComposer::class);

                $flexible
                    ->addLayout((new PeopleBuilderField($composer))->fields(), [
                        'label' => 'People',
                        'display' => 'block',
                    ]);
            }

            return $builder;
        });
    }

    public function register()
    {
        foreach ($this->providers() as $service) {
            (new $service)->register();
        }

        $this->registerFields();
        $this->registerLayouts();
    }

    public function boot()
    {
        // Register views
        View::addLocation(dirname(dirname(__DIR__)) . '/resources/views');

        View::composer('partials.builder.people', PeopleComposer::class);
    }
}
