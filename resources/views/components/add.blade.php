<div>
    <input type="hidden" name="address[{{ $random_id = random_int(1000000, 9999999) }}][address_id]" value="">
    <input type="text" name="address[{{ $random_id }}][type]" value="" placeholder="Type">
    <input type="text" name="address[{{ $random_id }}][international_code]" value="" placeholder="International code">
    <input type="text" name="address[{{ $random_id }}][area_code]" value="" placeholder="Area code">
    <input type="text" name="address[{{ $random_id }}][number]" value="" placeholder="Number">
</div>