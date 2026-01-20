<div class="people-single">
  <div class="people-single__wrapper">
    <div class="container wrap">
      <div class="people-single__row row">
        <div class="people-single__column col-12 col-md-4">
          @if($photo)
            <div class="people-single__image">
              <x-image-plain
                fillclass="ratio-16x9"
                size="full" sizes="{{ $photo['id'] }}"
                src="{{ $photo['id'] }}" srcset="{{ $photo['id'] }}"
                alt="{{ !empty($photo['alt']) ? $photo['alt'] : App\get_filename($photo['id']) }}"
              />
            </div>
          @endif
        </div>
        <div class="people-single__column col-12 col-md-6 offset-md-1">
          @if(!empty($title) || !empty($position))
            <div class="people-single__header">
              @if(!empty($title))
                <h1 class="people-single__title">{!! $title !!}</h1>
              @endif
              @if(!empty($position))
                <p class="people-single__position">{!! $position !!}</p>
              @endif
            </div>
          @endif
        </div>
      </div>

      @if($showRelatedMembers && $relatedPeoples)
        <div class="people-single__related">
          <p class="people-single__related-title h2">
            {!! $relatedMembersTitle !!}
          </p>
          <div class="row">
            @foreach($relatedPeoples as $person)
              <div class="people__item col-12 col-md-6 col-lg-4">
                <a class="people__item-wrapper link-reset" href="{{ $person['link'] }}" target="_self">
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
                </a>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
