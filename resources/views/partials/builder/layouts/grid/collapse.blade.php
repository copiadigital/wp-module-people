<div class="section people people--grid"
  x-data="{
    openPerson: null,
    togglePerson(slug) {
      this.openPerson = this.openPerson === slug ? null : slug;
    },
    fixFlexOrder(container) {
      const SNAP_LG = 992;
      const SNAP_SM = 767;
      const panels = container.querySelectorAll('.js-flex-panel');
      let j = 0;
      panels.forEach(panel => {
        const dropdown = panel.nextElementSibling;
        const windowWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        const divisor = windowWidth > SNAP_LG ? 3 : windowWidth < SNAP_SM ? 1 : 2;
        const rowOrder = Math.ceil((j + 1) / divisor);
        panel.style.order = rowOrder;
        panel.classList.add('is-number-' + (j + 1));
        if (dropdown && dropdown.classList.contains('js-flex-dropdown')) {
          dropdown.style.order = rowOrder + 1;
        }
        j++;
      });
    },
    init() {
      this.$el.querySelectorAll('.js-flex-reorder').forEach(container => {
        this.fixFlexOrder(container);
      });
    }
  }"
  x-init="init()"
>
  @if($teams && $peoples)
    @foreach ($teams as $team)
      @php
        $teamPeoples = array_filter($peoples, fn($person) => $person['teams'] === $team->slug);
      @endphp

      @if(count($teamPeoples) > 0)
        <div class="people__group">
          <h2 class="people__group-title">{!! $team->name !!}</h2>

          <div class="row js-flex-reorder">
            @foreach($teamPeoples as $person)
              @php
                $person_slug = $person['slug'] . '-' . uniqid();
              @endphp

              <div class="people__item col-12 col-md-6 col-lg-4 js-flex-item js-flex-panel">
                <div role="article" class="people__item-wrapper"
                  :class="openPerson === '{{ $person_slug }}' ? '' : 'collapsed'"
                  @click="togglePerson('{{ $person_slug }}')"
                  :aria-expanded="openPerson === '{{ $person_slug }}'"
                  aria-controls="personDropdown-{{ $person_slug }}"
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
        </div>
      @endif
    @endforeach
  @endif
</div>
