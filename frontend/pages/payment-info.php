<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Information - PropertyConnect</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <main class="registration-container">
        <h2>Payment Information</h2>
        
        <!-- User Details Form -->
        <form id="paymentForm" class="registration-form">
            <div class="form-section">
                <h3>Payment Information</h3>
                <div class="form-group">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" name="cardNumber" required>
                    <span class="error" id="cardNumberError"></span>
                </div>

                <div id="cardType" class="card-type"></div>


                <div class="form-row">
                    <div class="form-group">
                        <label for="expiryMonth">Expiry Month</label>
                        <select id="expiryMonth" name="expiryMonth" required>
                            <!-- months options -->
                            <option value="">Month</option>
                            <?php for($i = 1; $i <= 12; $i++): ?>

                                <!--this is to make the month number 2 digits  for example 01, 02, 03, 04, 05, 06, 07, 08, 09, 10, 11, 12-->
    <!--the argument '2' is for the length of the final disered number and '0' is for the padding character-->
    <!--the argument 'STR_PAD_LEFT' is for the padding direction-->
                                <option value="<?= $i ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="expiryYear">Expiry Year</label>
                        <select id="expiryYear" name="expiryYear" required>
                            <!-- years options -->
                            <option value="">Year</option>
                            <?php 
                            $currentYear = date('Y');
                            for($i = $currentYear; $i <= $currentYear + 10; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" required>
                        <span class="error" id="cvvError"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="billingAddress">Billing Address</label>
                    <textarea id="billingAddress" name="billingAddress" required></textarea>
                    <span class="error" id="billingAddressError"></span>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                    <span class="error" id="phoneError"></span>
                </div>
                <button type="submit" class="submit-btn">Save Payment Information</button>
            </div>     
        </form>
    </main> 

    <?php include '../components/footer.php'; ?>
    <script src="../assets/js/payment.js"></script>
</body>
</html>

