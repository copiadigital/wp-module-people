# How to use

1. ### Activate the module

   Once the modules installed, make sure to edit the **modules.php** & uncomment this line of code under **/web/app/theme/{name-of-the-theme}/app**

   _modules.php_
   ```sh
   require_once get_template_directory() . '/modules/wp-module-people/people.php';
   ```

2. ### Compiling assets

   To compile the people script, you just need to run yarn & yarn start or yarn build under **/web/app/theme/{name-of-the-theme}**

3. ### Editing a person

   The People post type uses the block editor — it has no ACF fields.

   - **Biography** is written in the canvas. People defaults to allowing only
     the Paragraph block; change that under *Block Editor → Allowed Blocks →
     People*, which stays authoritative once saved.
   - **Photo** is the post's **featured image** — WordPress' own sidebar panel.
   - **Position** and **related team** live in the *Person Details* panel in
     the document sidebar, stored as REST-backed post meta.

   The panel is `resources/scripts/editor/PersonPanel.jsx`, loaded on the
   People edit screen only by `Providers\RegisterAssets`. The meta keys are
   declared once in `Support\PersonData` and registered in
   `Providers\RegisterMeta`; read person data through `PersonData` rather than
   calling `get_post_meta()` directly, so the Blade partials keep receiving
   the array shapes they expect.

   `Providers\BlockEditorSupport` keeps `people` in the theme's Block Editor
   post-type list, so the Block Editor → Post Types screen always shows People
   ticked and it cannot be switched back to the classic editor.

   The People *page-builder layout* (grid / tabs / slider) is still an ACF
   flexible-content layout — see `Fields\Partials\People`. Only the person
   edit screen was converted.

4. ### The People block

   `theme/people` renders a people listing inside Gutenberg — the block
   equivalent of the ACF page-builder layout, with the same grid / tabs /
   slider styles and collapse / popup / view-page types.

   It lives in `blocks/people/` and follows the same pattern as every other
   block in wp-module-blocks — static `save.jsx` markup plus `view.js`
   hydration over REST, the shape the `news` block uses:

   - `save.jsx` emits the markup with `data-wp-*` directives and `<template
     data-wp-each>` placeholders; there is **no** `render.php` or
     `render_callback`.
   - `rest-api/PeopleApi.php` serves `/wp-json/blocks/v1/people`, reading
     through `Blocks\PeopleBlockData` so it honours the same `tax_position`
     and `people_post_order` ordering as the ACF layout.
   - `view.js` is an Interactivity API store — tab switching, collapse and the
     modal. No Alpine. Swiper is imported dynamically, so only the slider
     style downloads it.

   Because it is a static block, its markup lives in `post_content` and is
   written by the editor on save. Changing `save.jsx` invalidates existing
   instances — authors get "this block contains unexpected content" until they
   run Attempt Block Recovery and re-save. An instance saved before a markup
   change keeps rendering the **old** HTML until that happens.

   Anything whose visibility is controlled by `data-wp-bind--hidden` must also
   carry a static `hidden` attribute in `save.jsx`. Directives are applied only
   after the Interactivity runtime hydrates, so without it the element is
   painted first — which is why the modal flashed on load.

   Block discovery is module-agnostic: `vite.config.js` and
   `Blocks\Providers\RegisterBlocks` scan every `modules/*/blocks/*/`, so this
   block builds and registers with no wiring in wp-module-blocks.
   `Providers\BlockEditorSupport` adds it to the Allowed Blocks screen through
   the `theme/manageable_blocks` filter.

   **To use it**, tick *People* under **Block Editor → Allowed Blocks** for the
   post types you want it in. It only appears on post types where Gutenberg is
   enabled (**Block Editor → Post Types**).
