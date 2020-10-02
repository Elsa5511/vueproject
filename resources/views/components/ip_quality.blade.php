@if(\App\Lib\Settings::getValue('ip_quality_enabled', true, 'boolean', true))
{!! config('custom.ip_quality_script') !!}


    @if(isset($autorun)&&$autorun)
    <script>
        if (typeof Startup !== "undefined") {
            Startup.AfterResult(function (result) {
                // redirect or perform other business logic if fraud score is higher than recommended value of 75
                if (result.fraud_chance > 74 || result.country_code!='US') {
                    window.location.href = "{{route('fraud_check')}}?key={{auth()->user()->unique_id}}";	
                }
            });

            Startup.AfterFailure(function (reason) {
                // user has blocked the second JavaScript call
                // can redirect or perform other business logic if JS is not loaded
                window.location.href = "{{route('user.withdrawals.error')}}";
            });
        }

        if (typeof Startup === "undefined") {
            window.location.href = "{{route('user.withdrawals.error')}}";
        }
    </script>
    @endif
@endif