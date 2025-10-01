<?php

namespace People\Fields;

use Log1x\AcfComposer\Field;
use Log1x\AcfComposer\Builder;

class People extends Field
{
    /**
     * The field group.
     *
     * @return array
     */
    public function fields()
    {
        $Fields = Builder::make('people', [
            'title' => 'Fields',
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array(
                0 => 'the_content',
            ),
            'active' => true,
            'show_in_rest' => 0,
        ]);

        $Fields
            ->setLocation('post_type', '==', 'people');

        $Fields
            ->addText('position', [
                'label' => 'Position',
            ])
            ->addText('date_of_birth', [
                'label' => 'Date of Birth',
            ])
            ->addText('appointed_to_board', [
                'label' => 'Appointed to Board',
            ])
            ->addText('appointed_to_position', [
                'label' => 'Appointed to Position',
            ])
            ->addText('joined', [
                'label' => 'Joined',
            ])
            ->addText('positions_and_committees', [
                'label' => 'Positions and Committees',
            ])
            ->addWysiwyg('descriptions', [
                'label' => 'Descriptions',
            ])
            ->addImage('photo', [
                'label' => 'Photo',
                'preview_size' => 'thumbnail',
                'mime_types' => 'jpg, jpeg, png, webp, gif, svg',
            ]);
            
        return $Fields->build();
    }
}
