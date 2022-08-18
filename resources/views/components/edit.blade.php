<div>
    @forelse($model->addresses as $address)
        <input type="hidden" name="addresses[{{ $address->id }}][address_id]" value="{{ $address->id }}">
        <input type="text" name="addresses[{{ $address->id }}][name]" value="{{ $address->name }}" placeholder="Name/Number">
        <input type="text" name="addresses[{{ $address->id }}][address]" value="{{ $address->address }}" placeholder="Address">
        <input type="text" name="addresses[{{ $address->id }}][code]" value="{{ $address->code  }}" placeholder="Post Code">
        <input type="text" name="addresses[{{ $address->id }}][country]" value="{{ $address->country }}" placeholder="Country">
        = {{ $address->id }}
        <input type="checkbox" name="addresses[{{ $address->id }}][delete]">
        <br>
    @empty
        No addresses
    @endforelse
</div>