<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get ticket data from POST
$ticketData = isset($_POST['ticket_data']) ? json_decode($_POST['ticket_data'], true) : null;

if (!$ticketData) {
    header('Location: buses.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - KosovoMove</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-white: #ffffff;
            --radius-lg: 0.5rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            --transition-normal: all 0.3s ease;
            --font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-family);
        }

        body {
            font-size: 16px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .payment-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .payment-container {
            width: 100%;
            max-width: 800px;
            background-color: var(--color-white);
            border-radius: var(--radius-2xl);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .payment-header {
            padding: 2rem;
            text-align: center;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }

        .payment-logo {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1rem;
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--color-white);
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            box-shadow: 0 4px 6px rgba(34, 197, 94, 0.2);
        }

        .payment-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
            letter-spacing: -0.5px;
        }

        .payment-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            font-weight: 400;
        }

        .payment-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 2rem;
        }

        .ticket-summary {
            background-color: #f0fdf4;
            padding: 1.5rem;
            border-radius: var(--radius-xl);
            border: 1px solid #dcfce7;
        }

        .ticket-details {
            margin-bottom: 1.5rem;
        }

        .ticket-details h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #16a34a;
            letter-spacing: -0.25px;
        }

        .ticket-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            color: #166534;
            font-size: 0.9375rem;
            font-weight: 500;
        }

        .ticket-info.total {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #bbf7d0;
            font-weight: 600;
            color: #16a34a;
            font-size: 1rem;
        }

        .payment-form {
            padding: 1.5rem;
            background-color: var(--color-white);
            border-radius: var(--radius-xl);
            border: 1px solid #dcfce7;
        }

        .bank-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f0fdf4;
            border-radius: var(--radius-lg);
            border: 1px solid #dcfce7;
        }

        .bank-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background-color: white;
            border-radius: var(--radius-lg);
            border: 1px solid #dcfce7;
            transition: var(--transition-normal);
        }

        .bank-logo:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(34, 197, 94, 0.1);
        }

        .bank-logo img {
            height: 2rem;
            width: auto;
        }

        .bank-logo span {
            font-weight: 600;
            color: #16a34a;
            font-size: 0.9375rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #16a34a;
            font-size: 0.9375rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid #bbf7d0;
            border-radius: var(--radius-lg);
            font-size: 1rem;
            font-weight: 500;
            transition: var(--transition-normal);
        }

        .form-group input:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .card-details {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 1rem;
        }

        .submit-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: var(--color-white);
            border: none;
            border-radius: var(--radius-lg);
            font-size: 1.0625rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-normal);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            letter-spacing: 0.25px;
        }

        .submit-button:hover {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        }

        .secure-payment {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #16a34a;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .secure-payment i {
            color: #22c55e;
        }

        @media (max-width: 768px) {
            .payment-content {
                grid-template-columns: 1fr;
            }

            .ticket-summary {
                order: 2;
            }

            .payment-form {
                order: 1;
            }

            .bank-logos {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 640px) {
            .payment-page {
                padding: 1rem;
            }

            .payment-header {
                padding: 1.5rem;
            }

            .payment-content {
                padding: 1.5rem;
            }

            .card-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="payment-page">
        <div class="payment-container">
            <div class="payment-header">
                <div class="payment-logo">
                    <img src="img/logo_tiny.png" alt="KosovoMove Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <h1 class="payment-title">Complete Your Payment</h1>
                <p class="payment-subtitle">Secure payment powered by our banking partners</p>
            </div>

            <div class="payment-content">
                <div class="payment-form">
                    <div class="bank-logos">
                        <div class="bank-logo">
                            <img src="img/raiffaisen_logo.png" alt="Raiffeisen Bank">
                        </div>
                        <div class="bank-logo">
                            <img src="img/teb.png" alt="TEB Bank">
                        </div>
                    </div>

                    <form action="process-payment.php" method="POST">
                        <div class="form-group">
                            <label for="card-number">Card Number</label>
                            <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" required>
                        </div>

                        <div class="card-details">
                            <div class="form-group">
                                <label for="card-name">Name on Card</label>
                                <input type="text" id="card-name" name="card-name" placeholder="John Doe" required>
                            </div>

                            <div class="form-group">
                                <label for="expiry">Expiry</label>
                                <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required>
                            </div>

                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" placeholder="123" required>
                            </div>
                        </div>

                        <button type="submit" class="submit-button">
                            <i class="fas fa-lock"></i>
                            Pay Now
                        </button>

                        <div class="secure-payment">
                            <i class="fas fa-shield-alt"></i>
                            <span>Your payment is secured by our banking partners</span>
                        </div>
                    </form>
                </div>

                <div class="ticket-summary">
                    <div class="ticket-details">
                        <h3>Ticket Summary</h3>
                        <div class="ticket-info">
                            <span>Ticket Type</span>
                            <span><?php echo htmlspecialchars($ticketData['name']); ?></span>
                        </div>
                        <div class="ticket-info">
                            <span>Description</span>
                            <span><?php echo htmlspecialchars($ticketData['description']); ?></span>
                        </div>
                        <div class="ticket-info">
                            <span>Features</span>
                            <span><?php echo htmlspecialchars(implode(', ', $ticketData['features'])); ?></span>
                        </div>
                        <div class="ticket-info total">
                            <span>Total Amount</span>
                            <span><?php echo htmlspecialchars($ticketData['price']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Card number formatting
        const cardNumber = document.getElementById('card-number');
        cardNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(.{4})/g, '$1 ').trim();
            e.target.value = value;
        });

        // Expiry date formatting
        const expiry = document.getElementById('expiry');
        expiry.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2,4);
            }
            e.target.value = value;
        });

        // CVV validation
        const cvv = document.getElementById('cvv');
        cvv.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.slice(0,3);
            e.target.value = value;
        });
    </script>
</body>
</html> 