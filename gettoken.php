<?php
session_start();

$end_point = "oauth/v2/auth";
$scope = 'ZohoBooks.contacts.Create,ZohoBooks.contacts.UPDATE,ZohoBooks.contacts.READ,ZohoBooks.invoices.Create,ZohoBooks.invoices.UPDATE,ZohoBooks.invoices.READ,ZohoBooks.customerpayments.Create,ZohoBooks.customerpayments.UPDATE,ZohoBooks.customerpayments.READ,ZohoBooks.salesorders.Create,ZohoBooks.salesorders.UPDATE,ZohoBooks.salesorders.READ,ZohoBooks.settings.Create,ZohoBooks.settings.UPDATE,ZohoBooks.settings.READ,ZohoBooks.settings.DELETE';
$redirect_uri = '{YOUR_DOMAIN_NAME}'.basename(__FILE__);
$state = 'billing';

//https://accounts.zoho.com/oauth/v2/auth?
//scope=ZohoBooks.invoices.CREATE,ZohoBooks.invoices.READ,ZohoBooks.invoices.UPDATE,ZohoBooks.invoices.DELETE&
//client_id=1000.0SRSZSY37WMZ69405H3TMYI2239V
//&state=testing
//&response_type=code
//&redirect_uri=http://www.zoho.com/books&access_type=offline

if(isset($_POST['client_id'])){
    $_SESSION['data'] = $_POST;
    $client_id = $_POST['client_id'];
    $scope = $_POST['scope'];
    $portal_url = $_POST['portal'];

    $url = "{$portal_url}{$end_point}?scope={$scope}&client_id={$client_id}&state={$state}&access_type=offline&response_type=code&redirect_uri={$redirect_uri}";

    header('Location: '.$url);
    exit;
}elseif(isset($_GET['code']) && $_GET['state'] === $state){

    // https://accounts.zoho.com/oauth/v2/token
    //?code=1000.dd7e47321d48b8a7e312e3d6eb1a9bb8.b6c07ac766ec11da98bf6a261e24dca4
    //&client_id=1000.0SRSZSY37WMZ69405H3TMYI2239V
    //&client_secret=fb0196010f2b70df8db2a173ca2cf59388798abf
    //&redirect_uri=http://www.zoho.com/books
    //&grant_type=authorization_code

    $code = $_GET['code'];
    $client_id = $_SESSION['data']['client_id'];
    $client_secret = $_SESSION['data']['client_secret'];
    $portal_url = $_SESSION['data']['portal'];
    $end_point = 'oauth/v2/token';

    $url = "{$portal_url}{$end_point}?code={$code}&client_id={$client_id}&client_secret={$client_secret}&redirect_uri={$redirect_uri}&grant_type=authorization_code";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);


    echo $output;

    echo "<p><a href='gettoken.php'>Go back</a></p>";

    exit;
}


?>
<html>
<head>
    <title>Get Token</title>
    <style>
        body{
            font-size: 18px;
        }
        input[type=text]{
            display: block;
            padding: 6px;
            font-size: inherit;
        }
        select{
            display: block;
            padding: 6px;
            font-size: inherit;
            width: 300px;
        }

        button{
            padding: 10px 20px;
            font-size: inherit;
        }
    </style>
</head>
<body>
    <h2>Generate Zoho Refresh Token</h2>

    <p>You will have to first register your Server Base Application with Zoho's Developer console in order get your Client ID and Client Secret.</p>
    <p>To register your application, go to <a href="https://accounts.zoho.com/developerconsole" target="_blank">https://accounts.zoho.com/developerconsole</a> and click on Add Client ID. Provide the required details to register your application.</p>

    <form method="post" action="">
        <p>
            <label>Permission:</label>
            <input type="text" name="scope" size="80" value="<?php echo $scope ?>" required>
        </p>
        <p>
            <label>Client ID:</label>
            <input type="text" name="client_id" size="80" required>
        </p>
        <p>
            <label>Client Secret:</label>
            <input type="text" name="client_secret" size="80" required>
        </p>
        <p>
            <label>Redirect URI:</label>
            <input type="text" name="redirect_uri" value="<?php echo $redirect_uri ?>" size="80" readonly required>
        </p>
        <p>
            <label>Data Center (<a href="https://www.zoho.com/books/api/v3/#multidc" target="_blank"><small>Need help?</small></a>)</label>
            <select name="portal">
                <option value="https://accounts.zoho.com/">United States</option>
                <option value="https://accounts.zoho.eu/">Europe</option>
                <option value="https://accounts.zoho.in/">India</option>
                <option value="https://accounts.zoho.com.au/">Australia</option>
            </select>
        </p>
        <p>
            <button>Connect to your Zoho account</button>
        </p>
    </form>
</body>
</html>
