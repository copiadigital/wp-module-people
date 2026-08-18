import { createElement, Fragment } from '@wordpress/element';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
  __experimentalVStack as VStack,
  CheckboxControl,
  FormTokenField,
  Notice,
  PanelBody,
  Placeholder,
  SelectControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

const TAXONOMY = 'people_group';
const POST_TYPE = 'people';

const STYLES = [
  { value: 'grid', label: __('Grid', 'sage') },
  { value: 'tabs', label: __('Tabs', 'sage') },
  { value: 'slider', label: __('Slider', 'sage') },
];

// Mirrors the ACF layout: the "view page" option only exists when single
// person pages are enabled, which the server tells us via block settings.
const viewPageEnabled = () => !!window.THEME_PEOPLE_VIEW_PAGE_ENABLED;

const typeOptions = () => {
  const options = [
    { value: 'collapse', label: __('Collapse', 'sage') },
    { value: 'popup', label: __('Popup', 'sage') },
  ];
  if (viewPageEnabled()) {
    options.push({ value: 'view-page', label: __('View Page', 'sage') });
  }
  return options;
};

const sliderTypeOptions = () => {
  const options = [{ value: 'popup', label: __('Popup', 'sage') }];
  if (viewPageEnabled()) {
    options.push({ value: 'view-page', label: __('View Page', 'sage') });
  }
  return options;
};

const titleOf = (record) =>
  record?.title?.rendered?.trim() || __('(no title)', 'sage');

export default function Edit({ name, attributes, setAttributes }) {
  const blockClass = `wp-block-${name.replace('/', '-')}`;
  const blockProps = useBlockProps({ className: blockClass });

  const { style, type, sliderType, groups, showPeopleBasedOn, manualPosts } =
    attributes;

  const terms = useSelect(
    (select) =>
      select(coreStore).getEntityRecords('taxonomy', TAXONOMY, {
        per_page: -1,
        _fields: 'id,name',
      }),
    [],
  );

  const people = useSelect(
    (select) =>
      select(coreStore).getEntityRecords('postType', POST_TYPE, {
        per_page: 100,
        status: 'publish',
        _fields: 'id,title',
      }) || [],
    [],
  );

  const isSlider = style === 'slider';

  const peopleByTitle = {};
  const titleById = {};
  people.forEach((p) => {
    const t = titleOf(p);
    peopleByTitle[t] = p.id;
    titleById[p.id] = t;
  });

  const toggleGroup = (termId, checked) => {
    const next = checked
      ? [...groups, termId]
      : groups.filter((id) => id !== termId);
    setAttributes({ groups: [...new Set(next)] });
  };

  const summary = [
    STYLES.find((s) => s.value === style)?.label,
    isSlider
      ? sliderTypeOptions().find((o) => o.value === sliderType)?.label
      : typeOptions().find((o) => o.value === type)?.label,
  ]
    .filter(Boolean)
    .join(' · ');

  const groupSummary = () => {
    if (isSlider) {
      return showPeopleBasedOn === 'manual_posts'
        ? `${manualPosts.length} ${__('chosen manually', 'sage')}`
        : __('All people', 'sage');
    }
    if (!groups.length) {
      return __('All groups', 'sage');
    }
    return (terms || [])
      .filter((t) => groups.includes(t.id))
      .map((t) => t.name)
      .join(', ');
  };

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Layout', 'sage')}>
          <VStack spacing={4}>
            <SelectControl
              label={__('Style', 'sage')}
              value={style}
              options={STYLES}
              onChange={(value) => setAttributes({ style: value })}
              __nextHasNoMarginBottom
            />

            {isSlider ? (
              <SelectControl
                label={__('Type', 'sage')}
                value={sliderType}
                options={sliderTypeOptions()}
                onChange={(value) => setAttributes({ sliderType: value })}
                __nextHasNoMarginBottom
              />
            ) : (
              <SelectControl
                label={__('Type', 'sage')}
                value={type}
                options={typeOptions()}
                onChange={(value) => setAttributes({ type: value })}
                __nextHasNoMarginBottom
              />
            )}
          </VStack>
        </PanelBody>

        <PanelBody title={__('People shown', 'sage')}>
          {isSlider ? (
            <VStack spacing={4}>
              <SelectControl
                label={__('Show people based on', 'sage')}
                value={showPeopleBasedOn}
                options={[
                  { value: 'default', label: __('Show all people', 'sage') },
                  {
                    value: 'manual_posts',
                    label: __('Choose people manually', 'sage'),
                  },
                ]}
                onChange={(value) =>
                  setAttributes({ showPeopleBasedOn: value })
                }
                __nextHasNoMarginBottom
              />

              {showPeopleBasedOn === 'manual_posts' && (
                <FormTokenField
                  label={__('People', 'sage')}
                  value={manualPosts.map((id) => titleById[id] || `#${id}`)}
                  suggestions={Object.keys(peopleByTitle)}
                  onChange={(tokens) =>
                    setAttributes({
                      manualPosts: [
                        ...new Set(
                          tokens
                            .map((t) =>
                              typeof t === 'string' && t.startsWith('#')
                                ? Number(t.slice(1))
                                : peopleByTitle[t],
                            )
                            .filter(Boolean),
                        ),
                      ],
                    })
                  }
                  __experimentalExpandOnFocus
                  __nextHasNoMarginBottom
                />
              )}
            </VStack>
          ) : (
            <VStack spacing={2}>
              <p>{__('Show people from — leave all unticked for every group.', 'sage')}</p>
              {(terms || []).map((term) => (
                <CheckboxControl
                  key={term.id}
                  label={term.name}
                  checked={groups.includes(term.id)}
                  onChange={(checked) => toggleGroup(term.id, checked)}
                  __nextHasNoMarginBottom
                />
              ))}
              {terms && !terms.length && (
                <Notice status="warning" isDismissible={false}>
                  {__('No groups exist yet.', 'sage')}
                </Notice>
              )}
            </VStack>
          )}
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <Placeholder
          icon="groups"
          label={__('People', 'sage')}
          instructions={__(
            'Rendered on the published page. Adjust the layout in the block settings.',
            'sage',
          )}
        >
          <div>
            <strong>{summary}</strong>
            <br />
            {groupSummary()}
          </div>
        </Placeholder>
      </div>
    </>
  );
}
