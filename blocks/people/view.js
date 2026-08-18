/**
 * People block behaviour.
 *
 * The markup ships static from save.jsx and is filled in from the REST
 * endpoint on init — the same shape as the news and latest-news blocks.
 * Tab switching, collapse and the modal all run through the Interactivity
 * API; there is no Alpine here.
 */
import { store, getContext, getElement } from '../../../wp-module-blocks/shims/interactivity.js';

const PEOPLE_URL =
  (typeof window !== 'undefined' &&
    window.THEME_PEOPLE_API &&
    window.THEME_PEOPLE_API.people) ||
  '/wp-json/blocks/v1/people';

const { state } = store('theme/people', {
  state: {
    /** Tabs filter by group; grid and slider show everything. */
    get visiblePeople() {
      const ctx = getContext();
      if (!ctx.activeGroup) return ctx.people;
      return ctx.people.filter((p) => p.groupId === ctx.activeGroup);
    },
    get isActiveGroup() {
      const ctx = getContext();
      return ctx.item.id === ctx.activeGroup;
    },
    get itemHasNoPhoto() {
      return !getContext().item.hasPhoto;
    },
    get isPersonClosed() {
      const ctx = getContext();
      return ctx.openPerson !== ctx.item.id;
    },
    get isModalClosed() {
      return !getContext().modalOpen;
    },
    get modalName() {
      return getContext().modal.name;
    },
    get modalPosition() {
      return getContext().modal.position;
    },
    get modalPhoto() {
      return getContext().modal.photo;
    },
    get modalHasNoPhoto() {
      return !getContext().modal.hasPhoto;
    },
  },

  actions: {
    selectGroup() {
      const ctx = getContext();
      ctx.activeGroup = ctx.item.id;
      // A person left open in one tab should not stay open behind another.
      ctx.openPerson = 0;
    },

    togglePerson() {
      const ctx = getContext();
      ctx.openPerson = ctx.openPerson === ctx.item.id ? 0 : ctx.item.id;
    },

    openModal() {
      const ctx = getContext();
      ctx.modal = { ...ctx.item };
      ctx.modalOpen = true;
      document.body.style.overflow = 'hidden';
    },

    closeModal() {
      const ctx = getContext();
      ctx.modalOpen = false;
      document.body.style.overflow = '';
    },

    onModalKeydown(event) {
      if (event.key === 'Escape') {
        const ctx = getContext();
        ctx.modalOpen = false;
        document.body.style.overflow = '';
      }
    },
  },

  callbacks: {
    *init() {
      const ctx = getContext();
      const { ref } = getElement();

      const url = new URL(PEOPLE_URL, window.location.origin);
      url.searchParams.set('style', ref.dataset.style || 'tabs');
      url.searchParams.set('groups', ref.dataset.groups || '');
      url.searchParams.set('based_on', ref.dataset.basedOn || 'default');
      url.searchParams.set('ids', ref.dataset.manualPosts || '');

      const data = yield fetch(url).then((r) => r.json());

      ctx.groups = data.groups || [];
      ctx.people = data.people || [];

      // Tabs open on the first group; the other layouts show everything.
      if (ref.dataset.style === 'tabs' && ctx.groups.length) {
        ctx.activeGroup = ctx.groups[0].id;
      }

      if (ref.dataset.style === 'slider') {
        yield initSlider(ref);
      }
    },

    // The bio is HTML and there is no directive that sets innerHTML —
    // wp-bind writes attributes.
    renderBio() {
      const { ref } = getElement();
      ref.innerHTML = getContext().item.description || '';
    },

    renderModalDescription() {
      const { ref } = getElement();
      ref.innerHTML = getContext().modal.description || '';
    },
  },
});

/**
 * Only the slider layout needs Swiper, so it is imported dynamically and the
 * grid and tabs layouts never download it. Runs after the fetch so the slides
 * that data-wp-each rendered are in the DOM.
 */
async function initSlider(root) {
  const container = root.querySelector('.swiper');
  if (!container || container.dataset.swiperReady === 'true') return;

  await new Promise((resolve) => requestAnimationFrame(resolve));
  container.dataset.swiperReady = 'true';

  const [{ default: Swiper }, { Navigation, Scrollbar }] = await Promise.all([
    import('swiper'),
    import('swiper/modules'),
    // Core CSS alone leaves the arrows and scrollbar unpositioned — they need
    // their own module stylesheets.
    import('swiper/css'),
    import('swiper/css/navigation'),
    import('swiper/css/scrollbar'),
  ]);

  new Swiper(container, {
    modules: [Navigation, Scrollbar],
    spaceBetween: 48,
    slidesPerView: 1,
    breakpoints: {
      768: { slidesPerView: 2 },
      1200: { slidesPerView: 3 },
    },
    navigation: {
      nextEl: root.querySelector('.wp-block-theme-people__next'),
      prevEl: root.querySelector('.wp-block-theme-people__prev'),
    },
    scrollbar: {
      el: root.querySelector('.wp-block-theme-people__scrollbar'),
      draggable: true,
    },
  });
}

export default state;
