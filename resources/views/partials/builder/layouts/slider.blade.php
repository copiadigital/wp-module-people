<div class="people-slider" x-data="peopleSlider()" x-init="init()">
  @if($peoples)
    @php $enableViewPage = \People\Providers\PeopleSettings::isViewPageEnabled(); @endphp
    <div class="people-slider__swiper swiper">
      <div class="people-slider__swiper-wrapper swiper-wrapper">
        @foreach($peoples as $key => $person)
          <div class="people-slider__swiper-slide swiper-slide">
            <div class="people__item">
              <{{ $enableViewPage ? 'a' : 'div' }}
                class="people__item-wrapper{{ $enableViewPage ? ' link-reset' : '' }}"
                @if($enableViewPage) href="{{ $person['link'] }}" @endif
              >
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
              </{{ $enableViewPage ? 'a' : 'div' }}>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    <div class="swiper-footer">
      <div class="swiper-pagination"></div>
      <div class="swiper-buttons">
        <div class="swiper-button swiper-button-prev"></div>
        <div class="swiper-button swiper-button-next"></div>
      </div>
    </div>
  @endif
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('peopleSlider', () => ({
      swiper: null,
      init() {
        if (!window.Swiper || !window.SwiperModules) return;

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
