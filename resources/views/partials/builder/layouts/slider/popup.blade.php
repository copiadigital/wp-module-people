<div class="people-slider" x-data="{
    ...peopleSlider(),
    openModal(el) {
      const data = JSON.parse(el.dataset.person || '{}');
      this.$dispatch('people-modal-open', data);
    }
  }" x-init="init()">
  @if($peoples)
    <div class="swiper mt-30 md:mt-60 md:overflow-hidden">
      <div class="swiper-wrapper">
        @foreach($peoples as $key => $person)
          @php
            $photoHtml = '';
            if($person['photo']) {
              $photoHtml = '<div class="aspect-[16/9]">
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

          <div class="swiper-slide h-auto">
            <div class="people__item">
              <div class="people__item-wrapper people-modal-toggle cursor-pointer"
                data-person='@json($personData)'
                @click="openModal($el)">
                @if($person['photo'])
                  <div class="people__photo">
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
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    <div class="swiper-footer flex items-center mt-60">
      <div class="swiper-pagination flex items-center h-[2px] flex-1 bg-grey mr-20 md:mr-50"></div>
      <div class="swiper-buttons flex flex-wrap shrink-0">
        <div class="swiper-button swiper-button-prev cursor-pointer relative flex items-center justify-center bg-primary w-[50px] h-[50px] md:w-[60px] md:h-[60px] xl:hover:bg-secondary mr-[0.375rem]"></div>
        <div class="swiper-button swiper-button-next cursor-pointer relative flex items-center justify-center bg-primary w-[50px] h-[50px] md:w-[60px] md:h-[60px] xl:hover:bg-secondary"></div>
      </div>
    </div>
  @endif
</div>

@once
  <style>
    .people-slider .swiper-button::before {
      content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20' fill='none'%3E%3Cg clip-path='url(%23clip0_2378_3029)'%3E%3Cpath d='M0.841127 10.666H14' stroke='%23BBE8D5' stroke-width='1.63447' stroke-linecap='square' stroke-linejoin='round'/%3E%3Cpath d='M9.33463 17.2448L15.9141 10.6654L9.33463 4.08594' stroke='%23BBE8D5' stroke-width='1.63447' stroke-linecap='square' stroke-linejoin='bevel'/%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='clip0_2378_3029'%3E%3Crect width='19.6136' height='19.6136' fill='white' transform='matrix(-1 0 0 1 20 0)'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E");
      width: 19.614px;
      height: 19.614px;
    }
    .people-slider .swiper-button-prev::before {
      transform: rotate(180deg);
    }
    .people-slider .swiper-scrollbar-drag {
      cursor: pointer;
      height: 4px;
      background-color: var(--color-primary);
    }
  </style>
  @vite('modules/wp-module-people/resources/scripts/people.js')

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('peopleSlider', () => ({
        swiper: null,
        init() {
          // Swiper arrives from resources/scripts/people.js, which loads
          // after Alpine has already started (app.js is in <head>).
          if (window.Swiper && window.SwiperModules) {
            this.buildSwiper();
            return;
          }

          document.addEventListener(
            'people:swiper-ready',
            () => this.buildSwiper(),
            { once: true }
          );
        },
        buildSwiper() {
          // The element carries x-init="init()" and Alpine calls init()
          // itself, so guard against building twice.
          if (this.swiper) return;

          const container = this.$el;

          this.swiper = new Swiper(container.querySelector('.swiper'), {
            modules: [window.SwiperModules.Navigation, window.SwiperModules.Scrollbar],
            spaceBetween: 48,
            slidesPerView: 4,
            loop: false,
            autoHeight: false,
            scrollbar: {
              el: container.querySelector('.swiper-pagination'),
              draggable: true,
            },
            navigation: {
              nextEl: container.querySelector('.swiper-buttons .swiper-button-next'),
              prevEl: container.querySelector('.swiper-buttons .swiper-button-prev'),
              addIcons: false,
            },
            breakpoints: {
              320: {
                spaceBetween: 48,
                slidesPerView: 1,
              },
              768: {
                spaceBetween: 48,
                slidesPerView: 2,
              },
              1200: {
                spaceBetween: 48,
                slidesPerView: 3,
              },
            },
            on: {
              sliderMove: function () {
                document.querySelectorAll('.people-slider').forEach(function (el) {
                  const swiperEl = el.querySelector('.swiper');
                  if (swiperEl) swiperEl.style.pointerEvents = 'none';
                });
              },
              slideChangeTransitionEnd: function () {
                document.querySelectorAll('.people-slider').forEach(function (el) {
                  const swiperEl = el.querySelector('.swiper');
                  if (swiperEl) swiperEl.style.pointerEvents = 'unset';
                });
              },
              touchEnd: function () {
                setTimeout(() => {
                  document.querySelectorAll('.people-slider').forEach(function (el) {
                    const swiperEl = el.querySelector('.swiper');
                    if (swiperEl) swiperEl.style.pointerEvents = 'unset';
                  });
                }, 400);
              },
            },
          });

          if (this.swiper.slides.length <= this.swiper.params.slidesPerView) {
            container.querySelectorAll('.swiper-buttons').forEach(function (el) {
              el.style.setProperty('display', 'none', 'important');
            });
          }
        }
      }));
    });
  </script>
@endonce
