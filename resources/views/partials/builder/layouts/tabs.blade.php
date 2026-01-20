@if($type === 'collapse')
  @include('partials.builder.layouts.types.collapse')
@else
  @include('partials.builder.layouts.types.popup')
@endif
