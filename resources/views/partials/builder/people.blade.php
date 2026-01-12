@if($build['style'] === 'plain')

  @if($peoples)
    <div class="section people -plain">
      <div class="row">
        @foreach($peoples as $person)
          <div class="col-12 col-md-6 col-lg-3 js-flex-panel people__card" data-teams="{{ $person['teams'] }}">
            <div class="people__card-wrapper">
              @if($person['photo'])
                <div class="people__card-image {{ !empty($person['is_default_photo']) ? 'is-default' : '' }}">
                  <x-image-plain
                    width="301"
                    height="344"
                    size="full" sizes="{{ $person['photo']['id'] }}"
                    src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                    alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
                  />
                </div>
              @endif
              @if(!empty($person['title']) || !empty($person['position']))
                <div class="people__card-content">
                  @if(!empty($person['title']))
                    <p class="people__card-title">{!! $person['title'] !!}</p>
                  @endif
                  @if(!empty($person['position']))
                    <p class="people__card-position">{!! $person['position'] !!}</p>
                  @endif
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
@else
  @php
    // Prepare data for filtering
    $peopleByTeam = collect($peoples)->groupBy('teams')->toArray();
    $allOrder = get_option('people_post_order_all', []);

    // Sort "All" view by custom order if available
    if (!empty($allOrder)) {
      $indexed = collect($peoples)->groupBy('ID')->toArray();
      $peopleForAll = [];

      foreach ($allOrder as $personId) {
        if (isset($indexed[$personId])) {
          $peopleForAll = array_merge($peopleForAll, $indexed[$personId]);
          unset($indexed[$personId]);
        }
      }

      foreach ($indexed as $remaining) {
        $peopleForAll = array_merge($peopleForAll, $remaining);
      }
    } else {
      $peopleForAll = $peoples;
    }
  @endphp

  <div class="section people"
    x-data="{
      activeFilter: 'all'
    }"
  >
    @if($teams && $peoples)
      <div class="people__filter">
        <select class="people__filter-select" aria-label="Filter by team"
          x-model="activeFilter">
          <option value="all">All</option>
          @foreach ($teams as $team)
            <option value="{{ $team->slug }}">{!! $team->name !!}</option>
          @endforeach
        </select>
      </div>

      {{-- Render "All" view --}}
      <div class="row" x-show="activeFilter === 'all'">
        @foreach($peopleForAll as $person)
          @php
            $person_slug = $person['slug'] . '-' . uniqid();
          @endphp

          <div class="col-12 col-md-6 col-lg-3 people__card">
            <a href="#bs-modal-{{ $person_slug }}" role="button" class="people__card-wrapper link-reset" data-bs-toggle="modal" data-bs-target="#bs-modal-{{ $person_slug }}">
              @if($person['photo'])
                <div class="people__card-image {{ !empty($person['is_default_photo']) ? 'is-default' : '' }}">
                  <x-image-plain
                    width="301"
                    height="344"
                    size="full" sizes="{{ $person['photo']['id'] }}"
                    src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                    alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
                  />
                </div>
              @endif
              @if(!empty($person['title']) || !empty($person['position']))
                <div class="people__card-content">
                  @if(!empty($person['title']))
                    <p class="people__card-title">{!! $person['title'] !!}</p>
                  @endif
                  @if(!empty($person['position']))
                    <p class="people__card-position">{!! $person['position'] !!}</p>
                  @endif
                </div>
              @endif
            </a>
          </div>

          {{-- Person Modal for All view --}}
          <div class="modal fade people__modal" id="bs-modal-{{ $person_slug }}" tabindex="-1" aria-labelledby="modalLabel-{{ $person_slug }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <button
                    type="button"
                    class="people__modal-close d-none d-lg-inline-flex"
                    data-bs-dismiss="modal"
                    aria-label="Close">Close
                  </button>
                </div>
                <div class="modal-body">
                  <div class="people__modal-content">
                    <div class="row gx-6">
                      <div class="col-lg-4">
                        @if($person['photo'])
                          <div class="people__modal-image {{ !empty($person['is_default_photo']) ? 'is-default' : '' }}">
                            <x-image-plain
                              width="366"
                              height="418"
                              size="full" sizes="{{ $person['photo']['id'] }}"
                              src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                              alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
                            />
                          </div>
                        @endif
                      </div>
                      <div class="col-lg-8">
                        @if(!empty($person['title']))
                          <p class="people__modal-title h4">{!! $person['title'] !!}</p>
                        @endif
                        @if(!empty($person['position']))
                          <p class="people__modal-position text-xl">{!! $person['position'] !!}</p>
                        @endif
                        @if(!empty($person['descriptions']))
                          <div class="person__modal-description">
                            {!! $person['descriptions'] !!}
                          </div>
                        @endif
                        <button
                          type="button"
                          class="people__modal-close people__modal-close--mobile d-inline-flex d-lg-none"
                          data-bs-dismiss="modal"
                          aria-label="Close">Close
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Render team-specific views --}}
      @foreach($teams as $team)
        <div class="row" x-show="activeFilter === '{{ $team->slug }}'">
          @if(isset($peopleByTeam[$team->slug]))
            @foreach($peopleByTeam[$team->slug] as $person)
              @php
                $person_slug_team = $person['slug'] . '-' . $team->slug . '-' . uniqid();
              @endphp

              <div class="col-12 col-md-6 col-lg-3 people__card">
                <a href="#bs-modal-{{ $person_slug_team }}" role="button" class="people__card-wrapper link-reset" data-bs-toggle="modal" data-bs-target="#bs-modal-{{ $person_slug_team }}">
                  @if($person['photo'])
                    <div class="people__card-image {{ !empty($person['is_default_photo']) ? 'is-default' : '' }}">
                      <x-image-plain
                        width="301"
                        height="344"
                        size="full" sizes="{{ $person['photo']['id'] }}"
                        src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                        alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
                      />
                    </div>
                  @endif
                  @if(!empty($person['title']) || !empty($person['position']))
                    <div class="people__card-content">
                      @if(!empty($person['title']))
                        <p class="people__card-title">{!! $person['title'] !!}</p>
                      @endif
                      @if(!empty($person['position']))
                        <p class="people__card-position">{!! $person['position'] !!}</p>
                      @endif
                    </div>
                  @endif
                </a>
              </div>

              {{-- Person Modal for team view --}}
              <div class="modal fade people__modal" id="bs-modal-{{ $person_slug_team }}" tabindex="-1" aria-labelledby="modalLabel-{{ $person_slug_team }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button
                        type="button"
                        class="people__modal-close d-none d-lg-inline-flex"
                        data-bs-dismiss="modal"
                        aria-label="Close">Close
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="people__modal-content">
                        <div class="row gx-6">
                          <div class="col-lg-4">
                            @if($person['photo'])
                              <div class="people__modal-image {{ !empty($person['is_default_photo']) ? 'is-default' : '' }}">
                                <x-image-plain
                                  width="366"
                                  height="418"
                                  size="full" sizes="{{ $person['photo']['id'] }}"
                                  src="{{ $person['photo']['id'] }}" srcset="{{ $person['photo']['id'] }}"
                                  alt="{{ !empty($person['photo']['alt']) ? $person['photo']['alt'] : App\get_filename($person['photo']['id']) }}"
                                />
                              </div>
                            @endif
                          </div>
                          <div class="col-lg-8">
                            @if(!empty($person['title']))
                              <p class="people__modal-title h4">{!! $person['title'] !!}</p>
                            @endif
                            @if(!empty($person['position']))
                              <p class="people__modal-position text-xl">{!! $person['position'] !!}</p>
                            @endif
                            @if(!empty($person['descriptions']))
                              <div class="person__modal-description">
                                {!! $person['descriptions'] !!}
                              </div>
                            @endif
                            <button
                              type="button"
                              class="people__modal-close people__modal-close--mobile d-inline-flex d-lg-none"
                              data-bs-dismiss="modal"
                              aria-label="Close">Close
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>
      @endforeach
    @endif
  </div>
@endif
