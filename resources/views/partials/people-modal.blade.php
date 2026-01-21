{{-- Global People Modal - Include once in footer --}}
<div x-data="{
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
  @keydown.escape.window="closeModal()">

  <div class="people-modal"
    role="dialog"
    aria-modal="true"
    x-show="modalOpen"
    x-cloak
    style="display: none;">

    {{-- Backdrop --}}
    <div class="people-modal__backdrop"
      x-show="modalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      @click="closeModal()">
    </div>

    {{-- Dialog --}}
    <div class="people-modal__dialog"
      x-show="modalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 translate-y-4"
      x-transition:enter-end="opacity-100 translate-y-0"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 translate-y-0"
      x-transition:leave-end="opacity-0 translate-y-4"
      @click.stop>

      <div class="people-modal__content">
        <button class="people-modal-close btn-reset" aria-label="Close" @click="closeModal()">
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
        <div class="people-modal__body">
          <div class="people-modal__body-wrapper">
            <div class="row">
              <div class="col-12 col-md-4">
                <div class="people-modal__photo" x-html="modalData.photo"></div>
              </div>
              <div class="col-12 col-md-8">
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
