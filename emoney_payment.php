<!--Example Emoney payment using redirect API-->
<html>
    <body>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" 
                integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
                crossorigin="anonymous">
        </script>
        <h1>
            (Loading view can be shown here)
        </h1>
        <!--jQuery for auto submit form-->
        <?php
        include_once './KartukuRedirectAPI.php';

        $redirectApi = new KartukuRedirectAPI();
        $redirectApi->setMerchantToken('your merchant token here');
        $redirectApi->setSecretKey('your secret key here'); // secret key must not be written on source code

        $payment = array(
            'merchantToken' => $redirectApi->getMerchantToken(),
            'merchantReturnUrl' => 'http://localhost/ipg-redirect/response.php',
            'txnStoreCode' => '200',
            'merchantUserCode' => 'test@local',
            'txnReference' => 'test-'.time(),
            'txnBookingCode' => 'FASAQ12',
            'txnAmount' => '100000',
            'txnTradingDate' => date('Y-m-d H:i:s'), // for java : yyyy-MM-dd HH:mm:ss , for php : Y-m-d H:i:s, example : 2016-02-23 13:59:59
            'txnCustom1' => '',
            'txnCustom2' => '',
            'txnCustom3' => '',
            'ipgAcquirer' => 'TCASH',
            'txnCurrency' => 'IDR',
            'txnLang' => 'en',
            'txnItemsName' => '',
            'txnItemsQuantity' => '',
            'txnItemsPrice' => ''
        );

        $form = $redirectApi->generateEmoneyPaymentForm($payment, 'order-form'); // generate form with id 'order-form', usually the form is invisble to user
        echo $form; // write form to page
        ?>
        <script>
            // auto submit form using jquery
            // actual implementation can be vary
            $(document).ready(function () {
                $('#order-form').submit();
            });
        </script>
    </body>
</html>