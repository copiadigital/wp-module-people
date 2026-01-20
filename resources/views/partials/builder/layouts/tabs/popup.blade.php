<div class="section people people--tabs"
  x-data="{
    activeFilter: '',
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
      // Hide all main panels
      this.$el.querySelectorAll('.js-flex-panel').forEach(panel => {
        panel.style.display = 'none';
      });

      // Show only main panels for active filter
      this.$el.querySelectorAll(`.js-flex-panel[data-teams~='${this.activeFilter}']`).forEach(panel => {
        panel.style.display = '';
      });
    },
    openModal(el) {
      const data = JSON.parse(el.dataset.person || '{}');
      this.$dispatch('people-modal-open', data);
    }
  }"
  x-init="init()"
>
  @if($teams && $peoples)
    <div class="people__nav" @if(count($teams) <= 1) style="display: none;" @endif>
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
          $photoHtml = '';
          if($person['photo']) {
            $photoHtml = '<div class="ratio-16x9">
                          <img src="' . wp_get_attachment_image_url($person['photo']['id'], 'full') . '" alt="' . esc_attr(!empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id'])) . '" />
                          </div>';
          }
          $personData = [
            'photo' => $photoHtml,
            'name' => $person['title'] ?? '',
            'position' => $person['position'] ?? '',
            'description' => $person['descriptions'] ?? '',
          ];
        @endphp

        <div class="people__item col-12 col-md-6 col-lg-4 js-flex-item js-flex-panel" data-teams="{{ $person['teams'] }}">
          <div class="people__item-wrapper people-modal-toggle"
            data-person='@json($personData)'
            @click="openModal($el)"
            style="cursor: pointer;">

            @if($person['photo'])
              <div class="people__image">
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
      @endforeach
    </div>
  @endif
</div>
