<!--Example handling payment response using redirect API-->
<!--for both CC and Emoney payment-->
<html>
    <body>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" 
                integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
                crossorigin="anonymous">
        </script>
        <table>
            <?php
            include_once './KartukuRedirectAPI.php';

            $redirectApi = new KartukuRedirectAPI();
            $redirectApi->setMerchantToken('your merchant token here');
            $redirectApi->setSecretKey('your secret key here'); // secret key must not be written on source code
            
            // get response
            $responseData = $redirectApi->getResponseData($_POST);
            
            // check payment status response, based on ipgResponseCode field
            $paymentStatus = $responseData['ipgResponseCode'] == '0'; // payment success if ipgResponseCode is '0'
            
            // check respSignature
            $signatureStatus = $redirectApi->checkRespSignature($responseData);
            
            if($paymentStatus){
                echo '<h3>Payment success!</h3>';
            }else{
                echo '<h3>Payment failed!</h3>';
            }
            
            if($signatureStatus){
                echo '<p>Signature status is ok</p>';
            }else{
                echo '<p>Signature status is missmatch, do not trust success transaction with missmatch signature</p>';
            }
            
            echo '<hr><p>Detail : </p>';
            foreach ($responseData as $key => $value) {
                echo '<tr><td><label>' . $key . '</label></td>';
                echo '<td><input size="100" value="' . htmlspecialchars($value) . '" /></td></tr>';
            }            
            ?>
        </table>
    </body>
</html>