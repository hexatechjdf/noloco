<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA9SQetjbchWmEJVV1uKsl4Q_gQID3FGBQ&libraries=places"></script>
<script>
    function initAutocomplete() {
        var addressInput = document.querySelector(".address");
        var autocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ['geocode'],
            componentRestrictions: { country: "US" } // Change this to restrict to specific country if needed
        });

        autocomplete.setFields(["address_component", "formatted_address"]);

        autocomplete.addListener("place_changed", function () {
            var place = autocomplete.getPlace();
            if (!place.address_components) return;

            let street = "";
            let city = "";
            let state = "";
            let postal_code = "";

            place.address_components.forEach(component => {
                const types = component.types;
                if (types.includes("street_number")) {
                    street = component.long_name;
                }
                if (types.includes("route")) {
                    street += (street ? " " : "") + component.long_name;
                }
                if (types.includes("locality")) {
                    city = component.long_name;
                }
                if (types.includes("administrative_area_level_1")) {
                    state = component.long_name;
                }
                if (types.includes("postal_code")) {
                    postal_code = component.long_name;
                }
            });

            // Fill the form fields
            document.querySelector(".address").value = street || "";
            document.querySelector(".address1").value = street || "";
            // document.querySelector(".street").value = street;
            document.querySelector(".city").value = city;
            document.querySelector(".state").value = state;
            document.querySelector(".postal_code").value = postal_code;
        });
    }

    window.onload = function () {
        initAutocomplete();
    };
</script>
