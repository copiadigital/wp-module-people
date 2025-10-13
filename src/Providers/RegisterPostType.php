<?php

namespace People\Providers;
use Copia\CustomPostTypes as CPT;

class RegisterPostType implements Provider
{
    public function __construct()
    {
        add_action('init', [$this, 'cpt_register']);
    }

    public function register()
    {
        //
    }

    public function cpt_register() {
        $types = [];

        array_push($types, CPT::createPostType('people', 'People', 'People')
            ->setPublic(true)
            ->setPubliclyQueryable(false)
            ->setMenuPosition(25)
            ->setMenuIcon('dashicons-groups')
            ->setSupports(['title', 'editor', 'revisions'])
            ->setRewrite([
                'slug' => 'person',
                'with_front' => false
            ]),
        );

        array_push($types, CPT::createTaxonomy('people_group', 'people', 'Group')
            ->setPubliclyQueryable(false)
        );

        $types = apply_filters('people_tax_before_insert', $types);

        CPT::register($types, false);
    }
}
