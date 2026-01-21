@if($type === 'collapse')
  @include('partials.builder.layouts.grid.collapse')
@elseif($type === 'view-page')
  @include('partials.builder.layouts.grid.view-page')
@else
  @include('partials.builder.layouts.grid.popup')
@endif
