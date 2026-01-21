@if($type === 'collapse')
  @include('partials.builder.layouts.tabs.collapse')
@elseif($type === 'view-page')
  @include('partials.builder.layouts.tabs.view-page')
@else
  @include('partials.builder.layouts.tabs.popup')
@endif
