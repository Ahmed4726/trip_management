@component('mail::message')
# Spot Available!

Hi {{ $waitingList->name }},

Good news! A spot has opened up for **{{ $waitingList->availability->title ?? 'a trip' }}**.  
You requested **{{ $waitingList->party_size }} guest(s)**.

Please confirm your booking as soon as possible.

@component('mail::button', ['url' => route('public.widget', ['trip' => $waitingList->availability_id])])
Book Now
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
