import TomSelect from 'tom-select';

if (!window.__peopleModuleInitialized) {
  window.__peopleModuleInitialized = true;

  document.addEventListener('DOMContentLoaded', () => {
    // Enhance People filter select(s) with Tom Select
    document.querySelectorAll('.people .people__filter-select, .people .people__filter-select').forEach((el) => {
      if (el.tomselect) return;

      new TomSelect(el, {
        controlInput: null,
        maxItems: 1,
        dropdownAutoWidth: true,
      });
    });
  });
}
