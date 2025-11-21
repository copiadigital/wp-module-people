import { Collapse } from 'bootstrap';

if (!window.__peopleModuleInitialized) {
  window.__peopleModuleInitialized = true;

  document.addEventListener('DOMContentLoaded', () => {
    const SNAP_LG = 992;
    const SNAP_SM = 767;

    // Flex order
    const fixPeopleFlexOrder = (section) => {
      let j = 0;
      const panels = section.querySelectorAll('.js-flex-reorder > .js-flex-panel');
      panels.forEach(panel => {
        const dropdown = panel.nextElementSibling;
        const windowWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        const divisor = windowWidth > SNAP_LG ? 3 : windowWidth < SNAP_SM ? 1 : 2;
        const rowOrder = Math.ceil((j + 1) / divisor);

        if (panel.offsetParent !== null) {
          panel.style.order = rowOrder;
          panel.classList.add('is-number-' + (j + 1));
          if (dropdown) dropdown.style.order = rowOrder + 1;
          j++;
        } else {
          panel.style.order = 0;
          if (dropdown) dropdown.style.order = 0;
        }
      });
    };

    const peopleSections = document.querySelectorAll('.people');
    if (!peopleSections.length) return;

    peopleSections.forEach(section => {
      const peopleNavLinks = section.querySelectorAll('.people__nav a[data-filter]');
      if (!peopleNavLinks.length) return;

      const panels = section.querySelectorAll('.js-flex-panel');

      // Set first link active
      const firstLink = peopleNavLinks[0];
      firstLink.classList.add('active');
      const teamFilterFirst = firstLink.dataset.filter;

      // Hide all panels first
      panels.forEach(panel => panel.style.display = 'none');

      // Show only the first team's panels
      section.querySelectorAll(`.js-flex-panel[data-teams~="${teamFilterFirst}"]`).forEach(panel => {
        panel.style.display = '';
      });

      // Apply flex order
      fixPeopleFlexOrder(section);

      // Add click listener to filter links
      peopleNavLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();

          // Close any open collapse in this section
          section.querySelectorAll('.collapse.show').forEach(panel => {
            const bsCollapse = Collapse.getInstance(panel);
            if (bsCollapse) bsCollapse.hide();
          });

          // Remove active class from all links in this section
          peopleNavLinks.forEach(l => l.classList.remove('active'));
          this.classList.add('active');

          // Hide all panels
          panels.forEach(panel => panel.style.display = 'none');

          // Show only selected filter
          const teamFilter = this.dataset.filter;
          section.querySelectorAll(`.js-flex-panel[data-teams~="${teamFilter}"]`).forEach(panel => {
            panel.style.display = '';
          });

          // Reapply flex order
          fixPeopleFlexOrder(section);
        });
      });
    });
  });
}
