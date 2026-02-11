<div
  x-data="{
    modalOpen: false,
    modalData: {
      photo: '',
      name: '',
      position: '',
      description: ''
    },
    openModal(data) {
      this.modalData = {
        photo: data.photo || '',
        name: data.name || '',
        position: data.position || '',
        description: data.description || ''
      };
      this.modalOpen = true;
      document.body.style.overflow = 'hidden';
    },
    closeModal() {
      this.modalOpen = false;
      document.body.style.overflow = '';
    }
  }"
  @people-modal-open.window="openModal($event.detail)"
  @keydown.escape.window="closeModal()"
  class="people-modal"
>
  {{-- Backdrop --}}
  <div
    x-show="modalOpen"
    x-transition:enter="tw-transition tw-ease-out tw-duration-300"
    x-transition:enter-start="tw-opacity-0"
    x-transition:enter-end="tw-opacity-100"
    x-transition:leave="tw-transition tw-ease-in tw-duration-200"
    x-transition:leave-start="tw-opacity-100"
    x-transition:leave-end="tw-opacity-0"
    class="people-modal__backdrop tw-fixed tw-inset-0 tw-z-40 tw-backdrop-blur-sm"
    @click="closeModal()"
    aria-hidden="true"
  ></div>

  {{-- Modal Dialog --}}
  <div
    x-show="modalOpen"
    x-transition:enter="tw-transition tw-ease-out tw-duration-300"
    x-transition:enter-start="tw-opacity-0 tw-translate-y-[1rem] sm:tw-translate-y-0 sm:tw-scale-95"
    x-transition:enter-end="tw-opacity-100 tw-translate-y-0 sm:tw-scale-100"
    x-transition:leave="tw-transition tw-ease-in tw-duration-200"
    x-transition:leave-start="tw-opacity-100 tw-translate-y-0 sm:tw-scale-100"
    x-transition:leave-end="tw-opacity-0 tw-translate-y-[1rem] sm:tw-translate-y-0 sm:tw-scale-95"
    x-cloak
    class="people-modal__container tw-fixed tw-inset-0 tw-z-50 tw-overflow-y-auto"
    role="dialog"
    aria-modal="true"
  >
    <div class="tw-flex tw-min-h-full tw-items-center tw-justify-center tw-p-[1rem] tw-text-center sm:tw-p-0">
      <div
        class="people-modal__dialog tw-relative tw-w-full tw-max-w-[900px] tw-transform md:tw-max-w-[732px] lg:tw-max-w-[832px] xl:tw-max-w-[1024px] 2xl:tw-max-w-[1389px]"
        @click.stop
      >
        <div class="people-modal__content tw-relative">
          <button class="people-modal-close btn-reset tw-absolute tw-top-0 tw-right-0 tw-z-10" aria-label="Close" @click="closeModal()">
            <svg width="53" height="55" viewBox="0 0 53 55" fill="none" xmlns="http://www.w3.org/2000/svg">
              <mask id="path-1-inside-1_2774_7392" fill="white">
              <path d="M0.885254 0H52.1602V54.9321H0.885254V0Z"/>
              </mask>
              <path d="M0.885254 0H52.1602V54.9321H0.885254V0Z" fill="#1C2426"/>
              <path d="M52.1602 54.9321V51.275H0.885254V54.9321V58.5892H52.1602V54.9321Z" fill="#D3420D" mask="url(#path-1-inside-1_2774_7392)"/>
              <rect x="17.6309" y="16.0876" width="28.3959" height="3.64593" transform="rotate(45 17.6309 16.0876)" fill="white"/>
              <rect x="37.71" y="18.6661" width="28.3959" height="3.64593" transform="rotate(135 37.71 18.6661)" fill="white"/>
            </svg>
          </button>
          <div class="people-modal__body tw-bg-white tw-py-80 tw-px-40 tw-pb-50">
            <div class="people-modal__body-wrapper">
              <div class="tw-flex tw-flex-wrap -tw-mx-20">
                <div class="tw-w-full lg:tw-w-1/3 tw-px-20">
                  <div class="people-modal__photo" x-html="modalData.photo"></div>
                </div>
                <div class="tw-w-full md:tw-w-2/3 tw-px-20">
                  <div class="people-modal__info last-of-type">
                    <h2 class="people-modal__name" x-text="modalData.name"></h2>
                    <p class="people-modal__position" x-text="modalData.position"></p>
                    <div class="people-modal__description last-of-type" x-html="modalData.description"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
