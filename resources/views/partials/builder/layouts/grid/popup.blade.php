<div class="section people people--grid tw-py-40 md:tw-py-100"
  x-data="{
    openModal(el) {
      const data = JSON.parse(el.dataset.person || '{}');
      this.$dispatch('people-modal-open', data);
    }
  }"
>
  @if($teams && $peoples)
    @foreach ($teams as $team)
      @php
        $teamPeoples = array_filter($peoples, fn($person) => $person['teams'] === $team->slug);
      @endphp

      @if(count($teamPeoples) > 0)
        <div class="people__group tw-mb-60">
          <h2 class="people__group-title tw-mb-30">{!! $team->name !!}</h2>

          <div class="tw-flex tw-flex-wrap -tw-mx-20">
            @foreach($teamPeoples as $person)
              @php
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

              <div class="people__item tw-w-full md:tw-w-1/2 lg:tw-w-1/3 tw-px-20">
                <div class="people__item-wrapper people-modal-toggle"
                  data-person='@json($personData)'
                  @click="openModal($el)"
                  style="cursor: pointer;">

                  @if($person['photo'])
                    <div class="people__image">
                      <x-image-plain
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
        </div>
      @endif
    @endforeach
  @endif
</div>
