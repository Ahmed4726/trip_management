@component('mail::message')
# New Waiting List Signup

A new guest has joined the waiting list.

**Trip:** {{ $entry->availability->title ?? 'N/A' }}  
**Party Size:** {{ $entry->party_size }}  
**Name:** {{ $entry->name }}  
**Email:** {{ $entry->email }}  
**Phone:** {{ $entry->phone ?? '-' }}  
**Notes:** {{ $entry->notes ?? '-' }}

@component('mail::button', ['url' => route('admin.waitinglists.index')])
View Waiting List
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
