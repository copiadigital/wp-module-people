<?php

namespace People\Support;

/**
 * Reads a person's structured data from post meta and post content.
 *
 * Replaces the ACF field group that used to back the People edit screen. The
 * meta keys are the ones ACF already wrote to, and the return shapes match
 * what the ACF getters produced — `photo()` still yields an array with `id`
 * and `alt`, `bio()` still yields rendered HTML — so every Blade partial in
 * this module keeps working untouched.
 */
class PersonData
{
    public const META_POSITION = 'position';
    public const META_MANUAL_RELATED_ENABLED = 'choose_manual_related_team';
    public const META_MANUAL_RELATED = 'manual_related_team';

    public static function position($postId = null): string
    {
        return (string) get_post_meta(self::resolveId($postId), self::META_POSITION, true);
    }

    /**
     * The person's photo is the post's featured image — WordPress already
     * ships a block-editor panel for it, so there is no custom control.
     *
     * @return array{id:int,alt:string,url:string}|null
     */
    public static function photo($postId = null): ?array
    {
        $id = (int) get_post_thumbnail_id(self::resolveId($postId));

        if (!$id || !wp_attachment_is_image($id)) {
            return null;
        }

        return [
            'id' => $id,
            'alt' => (string) get_post_meta($id, '_wp_attachment_image_alt', true),
            'url' => (string) wp_get_attachment_image_url($id, 'full'),
        ];
    }

    /**
     * The biography, authored in the canvas as `core/paragraph` blocks — the
     * only block the People editor allows (see Providers\BlockEditorSupport).
     */
    public static function bio($postId = null): string
    {
        $post = get_post(self::resolveId($postId));

        if (!$post || trim((string) $post->post_content) === '') {
            return '';
        }

        // The theme decorates core blocks for full-width page layouts: an
        // outer `.wp-block-theme-*` element carrying a `block-spacing--*`
        // margin, plus an inner `.container`. A bio renders inside a card,
        // a modal or a narrow column that already owns its width and spacing,
        // so all of that has to stay off.
        //
        // wp-module-blocks skips the whole wrapper while it is rendering the
        // inner content of a theme/column, tracked with this depth counter.
        // A bio is the same situation, so borrow the counter rather than
        // post-processing the markup. Saved and restored because a bio can be
        // rendered while the page is itself inside a column.
        $depth = $GLOBALS['theme_inside_column_depth'] ?? 0;
        $GLOBALS['theme_inside_column_depth'] = $depth + 1;

        $html = apply_filters('the_content', $post->post_content);

        $GLOBALS['theme_inside_column_depth'] = $depth;

        return $html;
    }

    public static function usesManualRelated($postId = null): bool
    {
        return (bool) get_post_meta(self::resolveId($postId), self::META_MANUAL_RELATED_ENABLED, true);
    }

    /**
     * @return int[]
     */
    public static function manualRelatedIds($postId = null): array
    {
        $ids = get_post_meta(self::resolveId($postId), self::META_MANUAL_RELATED, true);

        if (!is_array($ids)) {
            return [];
        }

        return array_values(array_filter(array_map('intval', $ids)));
    }

    /**
     * The row shape every listing layout consumes.
     */
    public static function summary($postId): array
    {
        $postId = (int) $postId;

        return [
            'ID' => $postId,
            'title' => get_the_title($postId),
            'position' => self::position($postId),
            'descriptions' => self::bio($postId),
            'photo' => self::photo($postId),
            'link' => get_permalink($postId),
        ];
    }

    protected static function resolveId($postId): int
    {
        return $postId ? (int) $postId : (int) get_the_ID();
    }
}
