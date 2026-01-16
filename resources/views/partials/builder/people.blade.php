@if($type === 'popup')
  @include('partials.builder.layouts.popup')
@elseif($type === 'view-page')
  @include('partials.builder.layouts.view-page')
@elseif($type === 'slider')
  @include('partials.builder.layouts.slider')
@else
  @include('partials.builder.layouts.tabs')
@endif
