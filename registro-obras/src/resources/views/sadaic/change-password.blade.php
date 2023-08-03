@extends('member.layout.dashboard')

@section('content')
<div id="embed-content">
{!! $content !!}
</div>
@endsection

@push('scripts')
<script>
function checkform(form) {
  if (form.clave.value == '') {
    alert('Por favor, ingrese su clave anterior. Muchas Gracias');
    form.clave.focus();
    return false;
  }

  if (form.clavenueva1.value == '') {
    alert('Por favor, ingrese su nueva clave. Si no posee, deje el 0 (cero). Muchas Gracias');
    form.clavenueva1.focus();
    return false;
  }

  if (form.clavenueva2.value == '') {
    alert('Por favor, retipee su nueva clave. Muchas Gracias');
    form.clavenueva2.focus();
    return false;
  }

  return true;
}
</script>
<script src="{{ asset('js/sadaic.js') }}" defer></script>
@endpush