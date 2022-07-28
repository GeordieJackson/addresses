<div>
    <input type="hidden" name="addresses[{{ $random_id = random_int(1000000, 9999999) }}][address_id]" value="">
    <input type="text" name="addresses[{{ $random_id }}][name]" value="" placeholder="Name/Number">
    <input type="text" name="addresses[{{ $random_id }}][address]" value="" placeholder="Address">
    <input type="text" name="addresses[{{ $random_id }}][code]" value="" placeholder="Postcode">
    <input type="text" name="addresses[{{ $random_id }}][country]" value="" placeholder="Country">
</div>