<div class="people-single tw-mt-[calc(var(--header-height-mobile)+1.875rem)] tw-mb-50 md:tw-mt-[calc(var(--header-height)+3.125rem)]">
  <div class="people-single__wrapper">
    <div class="people-single__row tw-flex tw-flex-wrap -tw-mx-20">
      <div class="people-single__column tw-w-full md:tw-w-1/3 tw-px-20">
        @if($photo)
          <div class="people-single__image">
            <x-picture-plain
              fillclass="tw-aspect-[16/9]"
              size="full" sizes="{{ $photo['id'] }}"
              src="{{ $photo['id'] }}" srcset="{{ $photo['id'] }}"
              alt="{{ !empty($photo['alt']) ? $photo['alt'] : App\get_filename($photo['id']) }}"
            />
          </div>
        @endif
      </div>
      <div class="people-single__column tw-w-full md:tw-w-1/2 md:tw-ml-[8.333333%] tw-px-20">
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
      <div class="people-single__related tw-mt-70">
        <p class="people-single__related-title tw-text-h2 tw-mb-30">
          {!! $relatedMembersTitle !!}
        </p>
        <div class="tw-flex tw-flex-wrap -tw-mx-20">
          @foreach($relatedPeoples as $person)
            <div class="people__item tw-w-full md:tw-w-1/2 lg:tw-w-1/3 tw-px-20">
              <a class="people__item-wrapper link-reset" href="{{ $person['link'] }}" target="_self">
                @if($person['photo'])
                  <div class="people__photo">
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
              </a>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
