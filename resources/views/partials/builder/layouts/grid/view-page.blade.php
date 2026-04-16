<div class="section people people--grid py-40 md:py-100">
  @if($teams && $peoples)
    @foreach ($teams as $team)
      @php
        $teamPeoples = array_filter($peoples, fn($person) => $person['teams'] === $team->slug);
      @endphp

      @if(count($teamPeoples) > 0)
        <div class="people__group mb-60">
          <h2 class="people__group-title mb-30">{!! $team->name !!}</h2>

          <div class="flex flex-wrap -mx-20">
            @foreach($teamPeoples as $person)
              <div class="people__item w-full md:w-1/2 lg:w-1/3 px-20 mb-40">
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
        </div>
      @endif
    @endforeach
  @endif
</div>
