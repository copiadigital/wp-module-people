<?php

namespace People\Fields\Partials;

use Log1x\AcfComposer\Partial;
use Log1x\AcfComposer\Builder;
use People\Providers\PeopleSettings;

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

        $choices = [
            'collapse' => 'Collapse',
            'popup' => 'Popup',
        ];

        if (PeopleSettings::isViewPageEnabled()) {
            $choices['view-page'] = 'View Page';
        }

        $Fields
            ->addSelect('style', [
                'label' => 'Style',
                'wrapper' => array(
                    'width' => '50',
                ),
                'choices' => [
                    'grid' => 'Grid',
                    'tabs' => 'Tabs',
                    'slider' => 'Slider',
                ],
                'default_value' => 'tabs',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 0,
                'return_format' => 'value',
                'ajax' => 0,
            ])
            ->addSelect('type', [
                'label' => 'Type',
                'wrapper' => array(
                    'width' => '50',
                ),
                'choices' => $choices,
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
                    'width' => '50',
                ),
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'style',
                            'operator' => '!=',
                            'value' => 'slider',
                        ),
                    ),
                ),
                'taxonomy' => 'people_group',
                'field_type' => 'checkbox',
                'return_format' => 'object',
            ])
            ->addRadio('show_people_based_on', [
                'label' => 'Show people based on',
                'wrapper' => array(
                    'width' => '50',
                ),
                'required' => 1,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'style',
                            'operator' => '==',
                            'value' => 'slider',
                        ),
                    ),
                ),
                'choices' => [
                    'default' => 'Show all people',
                    'manual_posts' => 'Choose people manually',
                ],
                'default_value' => 'order_by_date',
                'return_format' => 'value',
            ])
            ->addRelationship('manual_posts', [
                'label' => 'Manual posts',
                'required' => 1,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'style',
                            'operator' => '==',
                            'value' => 'slider',
                        ),
                        array(
                            'field' => 'show_people_based_on',
                            'operator' => '==',
                            'value' => 'manual_posts',
                        ),
                    ),
                ),
                'post_type' => array(
                    0 => 'people',
                ),
                'filters' => array(
                    0 => 'search',
                    1 => 'taxonomy',
                ),
                'return_format' => 'object',
            ]);

        return $Fields;
    }
}
