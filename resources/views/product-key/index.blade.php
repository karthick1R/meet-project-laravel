<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Access - Meeting Room Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 30px;
            text-align: center;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
        }
        .btn-primary {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
        .price-badge {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .price-badge .amount {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0"><i class="fas fa-key me-2"></i>Get Access</h2>
            <p class="mb-0 mt-2">Complete your purchase to get started</p>
                    </div>
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <div class="price-badge">
                <div class="amount">₹1</div>
                <small class="text-muted">One-time payment</small>
                    </div>

            <form id="paymentForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                </div>
                                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="+91 1234567890" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="payButton">
                    <i class="fas fa-credit-card me-2"></i>Pay ₹1 & Get Access
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">Already have an account? <a href="{{ route('login') }}">Login here</a></small>
            </div>
        </div>
    </div>

        <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = document.getElementById('payButton');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            const formData = new FormData(this);
            
            
            fetch('{{ route("product-key.create-order") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const options = {
                        key: data.key,
                        amount: data.amount,
                        currency: 'INR',
                        name: 'Meeting Room Booking',
                        description: 'Product Key Access',
                        order_id: data.order_id,
                        handler: function(response) {
                            // Verify payment
                            const verifyData = new FormData();
                            verifyData.append('razorpay_order_id', response.razorpay_order_id);
                            verifyData.append('razorpay_payment_id', response.razorpay_payment_id);
                            verifyData.append('razorpay_signature', response.razorpay_signature);
                            verifyData.append('product_key_id', data.product_key_id);
                            verifyData.append('_token', document.querySelector('input[name="_token"]').value);

                            fetch('{{ route("product-key.verify-payment") }}', {
                                method: 'POST',
                                body: verifyData
                            })
                            .then(res => res.json())
                            .then(result => {
                                if (result.success) {
                                    alert('Payment successful! Check your email for registration link.');
                                    window.location.href = '{{ route("product-key.index") }}';
                                } else {
                                    alert('Payment verification failed: ' + result.message);
                                    button.disabled = false;
                                    button.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay ₹1 & Get Access';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again.');
                                button.disabled = false;
                                button.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay ₹1 & Get Access';
                            });
                        },
                        prefill: {
                            email: formData.get('email'),
                            contact: formData.get('phone')
                        },
                        theme: {
                            color: '#667eea'
                        }
                    };

                    const rzp = new Razorpay(options);
                    rzp.open();
                } else {
                    alert(data.message || 'An error occurred. Please try again.');
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay ₹1 & Get Access';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay ₹1 & Get Access';
            });
        });
        </script>
</body>
</html>
