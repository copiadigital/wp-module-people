@vite(['modules/wp-module-people/resources/scripts/people.js'])

@php
  $peopleSectionId = 'peopleSection-' . uniqid();
@endphp

<div class="section people" id="{{ $peopleSectionId }}">
  @if($teams && $peoples)
    <div class="people__nav">
      @foreach ($teams as $team)
        <div class="people__nav-item" role="presentation">
          <a class="people__nav-link" data-filter="{{ $team->slug }}">{!! $team->name !!}</a>
        </div>
      @endforeach
    </div>
    <div class="row js-flex-reorder">
      @foreach($peoples as $person)
        @php
          $person_slug = $person['slug'] . '-' . uniqid();
        @endphp

        <div class="col-12 col-md-6 col-lg-4 js-flex-item js-flex-panel" data-teams="{{ $person['teams'] }}">
          <div role="article" class="collapsed" data-bs-toggle="collapse" data-bs-target="#personDropdown-{{ $person_slug }}" aria-expanded="false" aria-controls="personDropdown-{{ $person_slug }}">
            @if($person['photo'])
              <x-image-progressive
                width="{{ $person['photo']['width'] }}"
                height="{{ $person['photo']['height'] }}"
                size="full" sizes="{{ $person['photo']['id'] }}"
                src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
              />
            @endif
            @if(!empty($person['title']) || !empty($person['position']))
              <div class="person__content">
                @if(!empty($person['title']))
                  <p class="person__title text-primary h5">{!! $person['title'] !!}</p>
                @endif
                @if(!empty($person['position']))
                  <p class="person__position">{!! $person['position'] !!}</p>
                @endif
              </div>
            @endif
          </div>
        </div>
        <div class="col-12 js-flex-item js-flex-dropdown">
          <div class="panel__dropdown collapse" id="personDropdown-{{ $person_slug }}" data-bs-parent="#{{ $peopleSectionId }}">
            @if(!empty($person['descriptions']))
              <div class="person__description">
                {!! $person['descriptions'] !!}
              </div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
