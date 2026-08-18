import { createElement, useMemo, useState } from '@wordpress/element';
import { PluginDocumentSettingPanel, store as editorStore } from '@wordpress/editor';
import {
  __experimentalVStack as VStack,
  FormTokenField,
  TextControl,
  ToggleControl,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

const POST_TYPE = 'people';

const META_POSITION = 'position';
const META_MANUAL_RELATED_ENABLED = 'choose_manual_related_team';
const META_MANUAL_RELATED = 'manual_related_team';

const titleOf = (record) =>
  record?.title?.rendered?.trim() || __('(no title)', 'sage');

/**
 * Replacement for the ACF relationship field. FormTokenField works in
 * strings, so we keep a title -> id map built from both the current
 * selection and the live search results.
 */
function RelatedPeopleControl({ selectedIds, currentPostId, onChange }) {
  const [search, setSearch] = useState('');

  const suggestionRecords = useSelect(
    (select) =>
      select(coreStore).getEntityRecords('postType', POST_TYPE, {
        per_page: 20,
        search: search || undefined,
        status: 'publish',
        _fields: 'id,title',
      }) || [],
    [search],
  );

  const selectedRecords = useSelect(
    (select) =>
      selectedIds.length
        ? select(coreStore).getEntityRecords('postType', POST_TYPE, {
            include: selectedIds,
            per_page: selectedIds.length,
            _fields: 'id,title',
          }) || []
        : [],
    [selectedIds.join(',')],
  );

  const { titleToId, idToTitle } = useMemo(() => {
    const toId = {};
    const toTitle = {};

    [...selectedRecords, ...suggestionRecords].forEach((record) => {
      if (record?.id === currentPostId) {
        return;
      }
      const title = titleOf(record);
      toId[title] = record.id;
      toTitle[record.id] = title;
    });

    return { titleToId: toId, idToTitle: toTitle };
  }, [selectedRecords, suggestionRecords, currentPostId]);

  // An id whose record hasn't loaded yet has no title — keep it in the value
  // list as a placeholder so saving doesn't silently drop it.
  const value = selectedIds.map(
    (id) => idToTitle[id] || `#${id}`,
  );

  const suggestions = Object.keys(titleToId).filter(
    (title) => !value.includes(title),
  );

  return (
    <FormTokenField
      label={__('Choose related team', 'sage')}
      value={value}
      suggestions={suggestions}
      onInputChange={setSearch}
      __experimentalExpandOnFocus
      __nextHasNoMarginBottom
      onChange={(tokens) => {
        const ids = tokens
          .map((token) => {
            if (typeof token !== 'string') {
              return null;
            }
            if (token.startsWith('#')) {
              const raw = Number(token.slice(1));
              return Number.isInteger(raw) && raw > 0 ? raw : null;
            }
            return titleToId[token] ?? null;
          })
          .filter((id) => id !== null);

        onChange([...new Set(ids)]);
      }}
    />
  );
}

export default function PersonPanel() {
  const { meta, postType, currentPostId } = useSelect((select) => {
    const editor = select(editorStore);

    return {
      meta: editor.getEditedPostAttribute('meta') || {},
      postType: editor.getCurrentPostType(),
      currentPostId: editor.getCurrentPostId(),
    };
  }, []);

  const { editPost } = useDispatch(editorStore);

  if (postType !== POST_TYPE) {
    return null;
  }

  const setMeta = (next) => editPost({ meta: { ...meta, ...next } });

  const manualEnabled = !!meta[META_MANUAL_RELATED_ENABLED];
  const selectedIds = Array.isArray(meta[META_MANUAL_RELATED])
    ? meta[META_MANUAL_RELATED]
    : [];

  return (
    <PluginDocumentSettingPanel
      name="people-person-details"
      title={__('Person Details', 'sage')}
      className="people-person-details"
    >
      <VStack spacing={4}>
        <TextControl
          label={__('Position', 'sage')}
          value={meta[META_POSITION] || ''}
          onChange={(position) => setMeta({ [META_POSITION]: position })}
          __nextHasNoMarginBottom
        />

        <ToggleControl
          label={__('Choose related team manually', 'sage')}
          help={__(
            'Off: related people are pulled from this person’s groups.',
            'sage',
          )}
          checked={manualEnabled}
          onChange={(enabled) =>
            setMeta({ [META_MANUAL_RELATED_ENABLED]: enabled })
          }
          __nextHasNoMarginBottom
        />

        {manualEnabled && (
          <RelatedPeopleControl
            selectedIds={selectedIds}
            currentPostId={currentPostId}
            onChange={(ids) => setMeta({ [META_MANUAL_RELATED]: ids })}
          />
        )}
      </VStack>
    </PluginDocumentSettingPanel>
  );
}
