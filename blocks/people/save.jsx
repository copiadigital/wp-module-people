import { createElement, Fragment } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { getBlockClass } from '../../../wp-module-blocks/utils/blockClass.js';

const INITIAL_CONTEXT = {
  groups: [],
  people: [],
  activeGroup: 0,
  openPerson: 0,
  modalOpen: false,
  modal: { name: '', position: '', description: '', photo: '', hasPhoto: false },
};

export default function Save({ attributes }) {
  const { style, type, sliderType, groups, showPeopleBasedOn, manualPosts } =
    attributes;

  const layoutType = style === 'slider' ? sliderType : type;
  const isSlider = style === 'slider';
  const isTabs = style === 'tabs';

  const blockProps = useBlockProps.save({
    className: `wp-block-theme-people--${style}`,
  });
  const blockClass = getBlockClass(blockProps.className);

  return (
    <div
      {...blockProps}
      data-wp-interactive="theme/people"
      data-wp-context={JSON.stringify(INITIAL_CONTEXT)}
      data-wp-init="callbacks.init"
      data-style={style}
      data-type={layoutType}
      data-groups={groups.join(',')}
      data-based-on={showPeopleBasedOn}
      data-manual-posts={manualPosts.join(',')}
    >
      <div className="container mx-auto px-20 md:px-40">
        {isTabs && (
          <div
            className={`${blockClass}__nav mb-40 flex flex-wrap border-b border-grey-dark`}
            role="tablist"
          >
            <template
              data-wp-each="context.groups"
              data-wp-each-key="context.item.id"
            >
              <button
                type="button"
                role="tab"
                className={`${blockClass}__nav-link grow cursor-pointer border-b-4 border-transparent bg-transparent py-30 text-center`}
                data-wp-on--click="actions.selectGroup"
                data-wp-class--is-active="state.isActiveGroup"
                data-wp-bind--aria-selected="state.isActiveGroup"
                data-wp-text="context.item.name"
              ></button>
            </template>
          </div>
        )}

        <div className={`${blockClass}__list${isSlider ? ' swiper relative' : ''}`}>
          <div
            className={`${blockClass}__track${
              isSlider
                ? ' swiper-wrapper'
                : ' grid gap-30 md:grid-cols-2 lg:grid-cols-3 [grid-auto-flow:row_dense]'
            }`}
          >
            <template
              data-wp-each="state.visiblePeople"
              data-wp-each-key="context.item.key"
            >
              <div
                className={`${blockClass}__item${isSlider ? ' swiper-slide h-auto' : ''}${
                  layoutType === 'collapse' ? ' contents' : ''
                }`}
              >
                {layoutType === 'view-page' ? (
                  <a
                    className={`${blockClass}__trigger block w-full no-underline`}
                    data-wp-bind--href="context.item.link"
                  >
                    <span
                      className={`${blockClass}__photo mb-20 block`}
                      data-wp-bind--hidden="state.itemHasNoPhoto"
                    >
                      <img
                        className="h-auto w-full"
                        loading="lazy"
                        data-wp-bind--src="context.item.photo"
                        data-wp-bind--alt="context.item.name"
                      />
                    </span>
                    <span className={`${blockClass}__meta block`}>
                      <span
                        className={`${blockClass}__name text-h5 block`}
                        data-wp-text="context.item.name"
                      ></span>
                      <span
                        className={`${blockClass}__position block`}
                        data-wp-text="context.item.position"
                      ></span>
                    </span>
                  </a>
                ) : (
                  <button
                    type="button"
                    className={`${blockClass}__trigger block w-full cursor-pointer border-0 bg-transparent p-0 text-left`}
                    data-wp-on--click={
                      layoutType === 'popup'
                        ? 'actions.openModal'
                        : 'actions.togglePerson'
                    }
                  >
                    <span
                      className={`${blockClass}__photo mb-20 block`}
                      data-wp-bind--hidden="state.itemHasNoPhoto"
                    >
                      <img
                        className="h-auto w-full"
                        loading="lazy"
                        data-wp-bind--src="context.item.photo"
                        data-wp-bind--alt="context.item.name"
                      />
                    </span>
                    <span className={`${blockClass}__meta block`}>
                      <span
                        className={`${blockClass}__name text-h5 block`}
                        data-wp-text="context.item.name"
                      ></span>
                      <span
                        className={`${blockClass}__position block`}
                        data-wp-text="context.item.position"
                      ></span>
                    </span>
                  </button>
                )}

                {layoutType === 'collapse' && (
                  <div
                    className={`${blockClass}__bio prose col-span-full mt-20`}
                    hidden
                    data-wp-bind--hidden="state.isPersonClosed"
                    data-wp-watch="callbacks.renderBio"
                  ></div>
                )}
              </div>
            </template>
          </div>

          {isSlider && (
            <>
              <div className={`${blockClass}__prev swiper-button-prev`}></div>
              <div className={`${blockClass}__next swiper-button-next`}></div>
              <div className={`${blockClass}__scrollbar swiper-scrollbar`}></div>
            </>
          )}
        </div>
      </div>

      {/*
        The modal ships with a static `hidden` attribute: `data-wp-bind--hidden`
        is only applied once the Interactivity runtime hydrates, so without it
        a full-screen overlay is painted before the JS runs.
      */}
      {layoutType === 'popup' && (
        <div
          className={`${blockClass}__modal fixed inset-0 z-50 flex items-center justify-center p-20`}
          hidden
          data-wp-bind--hidden="state.isModalClosed"
          data-wp-on--keydown="actions.onModalKeydown"
          role="dialog"
          aria-modal="true"
          tabIndex="-1"
        >
          <div
            className={`${blockClass}__modal-backdrop absolute inset-0 bg-[rgba(0,0,0,0.5)]`}
            data-wp-on--click="actions.closeModal"
          ></div>
          <div
            className={`${blockClass}__modal-dialog relative max-h-[90vh] w-full max-w-[900px] overflow-y-auto bg-white p-40`}
          >
            <button
              type="button"
              className={`${blockClass}__modal-close absolute right-20 top-20 cursor-pointer border-0 bg-transparent text-h4 leading-none`}
              data-wp-on--click="actions.closeModal"
              aria-label="Close"
            >
              &times;
            </button>
            <div
              className={`${blockClass}__modal-photo mb-30`}
              data-wp-bind--hidden="state.modalHasNoPhoto"
            >
              <img
                className="h-auto w-full"
                data-wp-bind--src="state.modalPhoto"
                data-wp-bind--alt="state.modalName"
              />
            </div>
            <h2
              className={`${blockClass}__modal-name text-h3`}
              data-wp-text="state.modalName"
            ></h2>
            <p
              className={`${blockClass}__modal-position mb-20`}
              data-wp-text="state.modalPosition"
            ></p>
            <div
              className={`${blockClass}__modal-description prose`}
              data-wp-watch="callbacks.renderModalDescription"
            ></div>
          </div>
        </div>
      )}
    </div>
  );
}
