@if($type === 'view-page')
  @include('partials.builder.layouts.slider.view-page')
@else
  @include('partials.builder.layouts.slider.popup')
@endif
