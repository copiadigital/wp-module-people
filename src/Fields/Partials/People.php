<?php

namespace People\Fields\Partials;

use Log1x\AcfComposer\Partial;
use Log1x\AcfComposer\Builder;

class People extends Partial
{
    /**
     * The partial field group.
     *
     * @return array
     */
    public function fields()
    {
        $Fields = Builder::make('people');

        $Fields
            ->addSelect('type', [
                'label' => 'Type',
                'wrapper' => array(
                    'width' => '30',
                ),
                'choices' => array(
                    'tabs' => 'Tabs',
                    'popup' => 'Popup',
                    'view-page' => 'View Page',
                ),
                'default_value' => 'tabs',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 0,
                'return_format' => 'value',
                'ajax' => 0,
            ])
            ->addTaxonomy('groups', [
                'label' => 'Show people from',
                'wrapper' => array(
                    'width' => '70',
                ),
                'taxonomy' => 'people_group',
                'field_type' => 'checkbox',
                'return_format' => 'object',
            ]);

        return $Fields;
    }
}
