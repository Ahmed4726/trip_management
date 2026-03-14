@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="content-wrapper">
<div class="container pt-3">

<h4>Payments for Booking #{{ $booking->id }}</h4>

@if ($errors->any())
<div class="alert alert-danger">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif


@foreach (['success','error'] as $msg)
@if(session($msg))
<div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
{{ session($msg) }}
</div>
@endif
@endforeach


<p>Total Price: <b>{{ $booking->price }} USD</b></p>

<p>
Deposit:
{{ $booking->deposit_amount ?? 0 }}
(Due:
{{ $booking->deposit_due_date ? \Carbon\Carbon::parse($booking->deposit_due_date)->format('d-m-Y') : '-' }}
)
</p>

<p>Amount Paid: <b>{{ $booking->amount_paid }}</b></p>

<p>Balance Due: <b>{{ $booking->balance_due }}</b></p>

<p>
Status:

@if($booking->balance_due <= 0)
<span class="badge bg-success">Fully Paid</span>
@else
<span class="badge bg-warning">{{ $booking->status }}</span>
@endif

</p>

<hr>


{{-- Add Payment Form --}}
@if($booking->balance_due > 0)

<h5>Add Payment</h5>

<form id="paymentForm" method="POST" action="{{ route('admin.bookings.payments.store', $booking->id) }}">
@csrf

<div class="mb-2">
<label>Amount</label>
<input
type="number"
name="amount"
id="amount"
class="form-control"
step="0.01"
required>
</div>

<div class="mb-2">
<label>Paid At</label>
<input type="date" name="paid_at" class="form-control" required>
</div>

<div class="mb-2">
<label>Payment Method</label>
<select name="payment_method" class="form-control" required>
<option value="cash">Cash</option>
<option value="card">Card</option>
<option value="bank_transfer">Bank Transfer</option>
</select>
</div>

<button class="btn btn-success">Add Payment</button>

</form>

@endif

<hr>


<h5>Payments Ledger</h5>

<table class="table table-bordered">

<thead>

<tr>
<th>#</th>
<th>Amount</th>
<th>Date Paid</th>
<th>Method</th>
<th>Invoice</th>
<th>Actions</th>
</tr>

</thead>

<tbody>

@foreach($booking->payments as $payment)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $payment->amount }}</td>

<td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d-m-Y') }}</td>

<td>{{ $payment->payment_method }}</td>

<td>{{ $payment->invoice_number }}</td>

<td>

<a
href="{{ route('admin.bookings.payments.invoice',$payment->id) }}"
class="btn btn-sm btn-info"
target="_blank"
title="View Invoice">
<i class="fa-solid fa-eye"></i>
</a>

<a
href="{{ route('admin.bookings.payments.download',$payment->id) }}"
class="btn btn-sm btn-primary"
title="Download Invoice">
<i class="fa-solid fa-download"></i>
</a>

<form
action="{{ route('admin.bookings.payments.delete',$payment->id) }}"
method="POST"
style="display:inline-block">

@csrf
@method('DELETE')

<button
type="submit"
class="btn btn-sm btn-danger"
title="Delete Payment"
onclick="return confirm('Delete this payment?')">

<i class="fa-solid fa-trash"></i>

</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>


</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.getElementById('paymentForm')?.addEventListener('submit',function(e){

let amount = parseFloat(document.getElementById('amount').value);

let due = parseFloat("{{ $booking->balance_due }}");

if(amount > due){

e.preventDefault();

Swal.fire({
icon:'error',
title:'Invalid Amount',
text:'Payment amount cannot exceed remaining balance ({{ $booking->balance_due }})'
});

}

});

</script>

@endsection
