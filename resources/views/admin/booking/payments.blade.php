@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="content-wrapper">
<div class="container py-4">
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

    {{-- Booking Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h3 class="fw-bold mb-2"><i class="fa-solid fa-wallet me-2 text-primary"></i>Booking #{{ $booking->id }} Payments</h3>
        <span class="badge {{ $booking->balance_due <= 0 ? 'bg-success' : 'bg-warning text-dark' }} fs-6">{{ $booking->status }}</span>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        @php
            $summary = [
                ['title'=>'Total Price','value'=>$booking->price_usd,'icon'=>'fa-dollar-sign','color'=>'text-primary'],
                ['title'=>'Deposit','value'=>$booking->deposit_amount ?? 0,'icon'=>'fa-hand-holding-dollar','color'=>'text-warning','extra'=>'Due: '.($booking->deposit_due_date ? \Carbon\Carbon::parse($booking->deposit_due_date)->format('d-m-Y') : '-')],
                ['title'=>'Paid','value'=>$booking->amount_paid,'icon'=>'fa-money-bill-wave','color'=>'text-success'],
                ['title'=>'Balance Due','value'=>$booking->balance_due,'icon'=> $booking->balance_due <=0 ? 'fa-check-circle' : 'fa-exclamation-circle','color'=>$booking->balance_due <=0 ? 'text-success' : 'text-danger','progress'=>($booking->amount_paid / $booking->price_usd)*100]
            ];
        @endphp

        @foreach($summary as $card)
        <div class="col-md-3 d-flex">
            <div class="card shadow-sm border-0 hover-shadow flex-fill">
                <div class="card-body text-center d-flex flex-column justify-content-between" style="min-height: 180px;">
                    <div>
                        <small class="text-muted">{{ $card['title'] }}</small>
                        <h5 class="mt-2 fw-bold">{{ $card['value'] }} USD</h5>
                        @if(isset($card['extra']))<small class="text-muted d-block">{{ $card['extra'] }}</small>@endif
                        @if(isset($card['progress']))
                        <div class="progress my-2" style="height:10px;border-radius:10px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $card['progress'] }}%; background: linear-gradient(90deg,#0d6efd,#6610f2);"></div>
                        </div>
                        @endif
                    </div>
                    <i class="fa-solid {{ $card['icon'] }} fa-2x {{ $card['color'] }} mt-2"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Add Payment Form --}}
    @if($booking->balance_due > 0)
    <div class="card shadow-sm mb-4 border-0 hover-shadow">
        <div class="card-header bg-gradient text-white d-flex align-items-center">
            <i class="fa-solid fa-plus me-2"></i> Add Payment
        </div>
        <div class="card-body">
            <form id="paymentForm" method="POST" action="{{ route('admin.bookings.payments.store', $booking->id) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Amount</label>
                        <input type="number" name="amount" id="amount" class="form-control form-control-lg" step="0.01" placeholder="Enter amount in USD" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Paid At</label>
                        <input type="date" name="paid_at" class="form-control form-control-lg" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <select name="payment_method" class="form-control form-control-lg" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-success btn-lg w-100"><i class="fa-solid fa-check me-1"></i>Add Payment</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Payments Table --}}
    <h5 class="mb-3"><i class="fa-solid fa-receipt me-2"></i>Payments Ledger</h5>
    <div class="table-responsive shadow-sm">
        <table class="table table-hover table-bordered align-middle bg-white">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Amount</th>
                    <th>Date Paid</th>
                    <th>Method</th>
                    <th>Invoice</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($booking->payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $payment->amount }} USD</td>
                    <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d-m-Y') }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td>{{ $payment->invoice_number }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.bookings.payments.invoice',$payment->id) }}" target="_blank" class="btn btn-sm btn-info mx-1" title="View Invoice"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('admin.bookings.payments.download',$payment->id) }}" class="btn btn-sm btn-primary mx-1" title="Download Invoice"><i class="fa-solid fa-download"></i></a>
                        <form action="{{ route('admin.bookings.payments.delete',$payment->id) }}" method="POST" class="d-inline-block mx-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment?')"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No payments recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    transition: all 0.3s ease;
}
.bg-gradient {
    background: linear-gradient(90deg,#0d6efd,#6610f2);
}
.table td, .table th {
    vertical-align: middle;
}
.table-responsive .btn {
    min-width: 40px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
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
