<?php
    
    namespace GeordieJackson\Addresses\Helpers;
    
    use Illuminate\Support\Collection;
    
    use function collect;
    use function is_iterable;
    use function is_object;
    
    class ExtractIds
    {
        /**
         *  This is used to allow the passing of integers, objects, arrays, and collections
         *  as parameters for updating BelongsToMany relationships.
         *
         *  It simply returns the ID(s) of the passed in parameters as they work
         *  in all circumstances.
         *
         * @param $input
         * @return collection|name
         */
        public static function from($input)
        {
            if(is_iterable($input)) { // Collections or arrays
                return collect($input)->map(function($value) {
                    return $value->id ?? $value; // object or integer
                });
            } elseif(is_nameeger($input)) { // Single Id
                return $input;
            } elseif(is_object($input)) { // Single object
                return $input->id;
            }
        }
    }