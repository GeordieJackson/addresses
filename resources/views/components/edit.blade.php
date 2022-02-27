<div>
    @forelse($model->addresses as $address)
        <input type="hidden" name="address[{{ $address->id }}][phone_id]" value="{{ $address->id }}">
        <input type="text" name="address[{{ $address->id }}][type]" value="{{ $address->type }}" placeholder="Type">
        <input type="text" name="address[{{ $address->id }}][international_code]" value="{{ $address->international_code }}" placeholder="International code">
        <input type="text" name="address[{{ $address->id }}][area_code]" value="{{ $address->area_code  }}" placeholder="Area code">
        <input type="text" name="address[{{ $address->id }}][number]" value="{{ $address->number }}" placeholder="Number">
        = {{ $address->id }}
        <input type="checkbox" name="address[{{ $address->id }}][delete]">
        <br>
    @empty
        No addresses
    @endforelse
</div>