<div class="section people people--grid">
  @if($teams && $peoples)
    @foreach ($teams as $team)
      @php
        $teamPeoples = array_filter($peoples, fn($person) => $person['teams'] === $team->slug);
      @endphp

      @if(count($teamPeoples) > 0)
        <div class="people__group">
          <h2 class="people__group-title">{!! $team->name !!}</h2>

          <div class="row">
            @foreach($teamPeoples as $person)
              <div class="people__item col-12 col-md-6 col-lg-4">
                <a class="people__item-wrapper link-reset" href="{{ $person['link'] }}" target="_self">
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
                </a>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    @endforeach
  @endif
</div>
