<div class="section people people--tabs py-40 md:py-100"
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
    }
  }"
  x-init="init()"
>
  @if($teams && $peoples)
    <div class="people__nav border-b-0 mb-40 list-none pl-0 md:flex" @if(count($teams) <= 1) style="display: none;" @endif>
      @foreach ($teams as $team)
        <div class="people__nav-item mb-20 grow text-center border-b border-grey-dark md:mb-0" role="presentation">
          <a href="#" class="people__nav-link block py-30 border-b-4 cursor-pointer no-underline hover:border-current focus:border-current {{ $loop->first ? 'border-primary' : 'border-transparent' }}" data-filter="{{ $team->slug }}"
            :class="activeFilter === '{{ $team->slug }}' ? 'active border-primary' : 'border-transparent'">{!! $team->name !!}</a>
        </div>
      @endforeach
    </div>

    <div class="flex flex-wrap -mx-20 js-flex-reorder">
      @foreach($peoples as $person)
        <div class="people__item w-full md:w-1/2 lg:w-1/3 px-20 mb-40 js-flex-item js-flex-panel" data-teams="{{ $person['teams'] }}">
          <a class="people__item-wrapper link-reset" href="{{ $person['link'] }}" target="_self">
            @if($person['photo'])
              <div class="people__image">
                <x-picture-plain
                  fillclass="aspect-[16/9]"
                  size="full" sizes="{{ $person['photo']['id'] }}"
                  src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                  alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
                />
              </div>
            @endif
            @if(!empty($person['title']) || !empty($person['position']))
              <div class="people__content">
                @if(!empty($person['title']))
                  <p class="people__title text-h5">{!! $person['title'] !!}</p>
                @endif
                @if(!empty($person['position']))
                  <p class="people__position">{!! $person['position'] !!}</p>
                @endif
              </div>
            @endif
          </a>
        </div>
      @endforeach
    </div>
  @endif
</div>
