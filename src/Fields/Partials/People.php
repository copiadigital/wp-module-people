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
            ->addSelect('style', [
                'label' => 'Style',
                'wrapper' => array(
                    'width' => '33.33',
                ),
                'choices' => [
                    'plain' => 'Plain',
                    'with-filter-modal' => 'With Filter & Modal',
                ],
                'default_value' => 'plain',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 0,
                'return_format' => 'value',
            ])
            ->addTaxonomy('groups', [
                'label' => 'Show people from',
                'instructions' => 'Select one or more groups. If a single group is selected, it will use that group\'s custom order. Multiple groups will be sorted by their individual group orders.',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'style',
                            'operator' => '==',
                            'value' => 'plain',
                        ),
                    ),
                ),
                'taxonomy' => 'people_group',
                'field_type' => 'radio',
                'return_format' => 'id',
                'multiple' => 1,
            ]);

        return $Fields;
    }
}
