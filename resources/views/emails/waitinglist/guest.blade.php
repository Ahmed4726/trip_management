@component('mail::message')
# Waiting List Confirmation

Hi {{ $entry->name }},

Thank you for joining the waiting list for **{{ $entry->availability->title ?? 'a trip' }}**.  
We have received your request for **{{ $entry->party_size }} guest(s)**.

We’ll notify you if a spot becomes available.

@component('mail::button', ['url' => config('app.url')])
Visit Our Website
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
