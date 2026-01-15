<div class="section people"
  x-data="{
    activeFilter: '',
    openPerson: null,
    init() {
      const links = this.$el.querySelectorAll('.people__nav a[data-filter]');
      if (!links.length) return;

      // Set first link as active
      this.activeFilter = links[0].dataset.filter;

      // Initially show only first team's panels
      this.showPanels();

      // Listen for tab clicks
      links.forEach(link => {
        link.addEventListener('click', e => {
          e.preventDefault();
          // Set active filter
          this.activeFilter = link.dataset.filter;
          this.showPanels();
        });
      });
    },
    showPanels() {
      // Hide all main panels and dropdown panels
      this.$el.querySelectorAll('.js-flex-panel, .js-flex-dropdown').forEach(panel => {
        panel.style.display = 'none';
      });

      // Show only main panels for active filter
      this.$el.querySelectorAll(`.js-flex-panel[data-teams~='${this.activeFilter}']`).forEach(panel => {
        panel.style.display = '';
      });

      // Show dropdowns that are children of visible panels if needed
      this.$el.querySelectorAll(`.js-flex-panel[data-teams~='${this.activeFilter}'] + .js-flex-dropdown`).forEach(dropdown => {
        dropdown.style.display = '';
      });

      // Recalculate flex order
      this.fixFlexOrder();

      // Close any open person when switching filters
      this.openPerson = null;
    },
    togglePerson(slug) {
      this.openPerson = this.openPerson === slug ? null : slug;
    },
    fixFlexOrder() {
      const SNAP_LG = 992;
      const SNAP_SM = 767;
      const panels = this.$el.querySelectorAll('.js-flex-reorder > .js-flex-panel');
      let j = 0;
      panels.forEach(panel => {
        const dropdown = panel.nextElementSibling;
        if (panel.style.display === 'none') return;
        const windowWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        const divisor = windowWidth > SNAP_LG ? 3 : windowWidth < SNAP_SM ? 1 : 2;
        const rowOrder = Math.ceil((j + 1) / divisor);
        panel.style.order = rowOrder;
        panel.classList.add('is-number-' + (j + 1));
        if (dropdown) dropdown.style.order = rowOrder + 1;
        j++;
      });
    }
  }"
  x-init="init()"
>
  @if($teams && $peoples)
    <div class="people__nav">
      @foreach ($teams as $team)
        <div class="people__nav-item" role="presentation">
          <a href="#" class="people__nav-link" data-filter="{{ $team->slug }}"
            :class="{'active': activeFilter === '{{ $team->slug }}'}">{!! $team->name !!}</a>
        </div>
      @endforeach
    </div>

    <div class="row js-flex-reorder">
      @foreach($peoples as $person)
        @php
          $person_slug = $person['slug'] . '-' . uniqid();
        @endphp

        <div class="people__item col-12 col-md-6 col-lg-4 js-flex-item js-flex-panel" data-teams="{{ $person['teams'] }}">
          <div role="article" class="people__item-wrapper"
            :class="openPerson === '{{ $person_slug }}' ? '' : 'collapsed'"
            @click="togglePerson('{{ $person_slug }}')"
            :aria-expanded="openPerson === '{{ $person_slug }}'"
            aria-controls="personDropdown-{{ $person_slug }}"
            style="cursor: pointer;">
            @if($person['photo'])
              <div class="people__photo">
                <x-image-plain
                  fillclass="ratio-16x9"
                  size="full" sizes="{{ $person['photo']['id'] }}"
                  src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                  alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
                />
              </div>
            @endif
            @if(!empty($person['title']) || !empty($person['position']))
              <div class="people__content">
                @if(!empty($person['title']))
                  <p class="people__title text-primary h5">{!! $person['title'] !!}</p>
                @endif
                @if(!empty($person['position']))
                  <p class="people__position">{!! $person['position'] !!}</p>
                @endif
              </div>
            @endif
          </div>
        </div>
        <div class="col-12 js-flex-item js-flex-dropdown">
          <div class="panel__dropdown"
            id="personDropdown-{{ $person_slug }}"
            x-show="openPerson === '{{ $person_slug }}'"
            x-collapse>
            @if(!empty($person['descriptions']))
              <div class="people__description">
                {!! $person['descriptions'] !!}
              </div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
