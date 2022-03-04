@component('mail::message')
    # Welcome to {{ config('app.name') }}

    This is a community of fellow developers and we love that you have joined us.


    Happy to Help,<br>
    Thanks and regards,<br>


    @component('mail::button', ['url' => 'https://unsubscribe.com'])
        Unsubscribe
    @endcomponent
@endcomponent



