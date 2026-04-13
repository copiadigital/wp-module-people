<div class="section people people--tabs tw-py-40 md:tw-py-100"
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
    <div class="people__nav tw-border-b-0 tw-mb-40 tw-list-none tw-pl-0 md:tw-flex" @if(count($teams) <= 1) style="display: none;" @endif>
      @foreach ($teams as $team)
        <div class="people__nav-item tw-mb-20 tw-grow tw-text-center tw-border-b tw-border-grey-dark md:tw-mb-0" role="presentation">
          <a href="#" class="people__nav-link tw-block tw-py-30 tw-border-b-4 tw-cursor-pointer tw-no-underline hover:tw-border-current focus:tw-border-current {{ $loop->first ? 'tw-border-primary' : 'tw-border-transparent' }}" data-filter="{{ $team->slug }}"
            :class="activeFilter === '{{ $team->slug }}' ? 'active tw-border-primary' : 'tw-border-transparent'">{!! $team->name !!}</a>
        </div>
      @endforeach
    </div>

    <div class="tw-flex tw-flex-wrap -tw-mx-20 js-flex-reorder">
      @foreach($peoples as $person)
        @php
          $person_slug = $person['slug'] . '-' . uniqid();
          $photoHtml = '';
          if($person['photo']) {
            $photoHtml = '<div class="tw-aspect-[16/9]">
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

        <div class="people__item tw-w-full md:tw-w-1/2 lg:tw-w-1/3 tw-px-20 tw-mb-40 js-flex-item js-flex-panel" data-teams="{{ $person['teams'] }}">
          <div class="people__item-wrapper people-modal-toggle"
            data-person='@json($personData)'
            @click="openModal($el)"
            style="cursor: pointer;">

            @if($person['photo'])
              <div class="people__image">
                <x-picture-plain
                  fillclass="tw-aspect-[16/9]"
                  size="full" sizes="{{ $person['photo']['id'] }}"
                  src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                  alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
                />
              </div>
            @endif
            @if(!empty($person['title']) || !empty($person['position']))
              <div class="people__content">
                @if(!empty($person['title']))
                  <p class="people__title tw-text-h5">{!! $person['title'] !!}</p>
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
