<?php
header("Access-Control-Allow-Origin: https://shopifyofficials.com");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $host = "localhost";
        $user = "shopifyof_shopifyof";
        $password = "&&STB9MhlxuD"; // Replace with your MySQL password
        $database = "shopifyof_lp";
        
        $conn = new mysqli($host, $user, $password, $database);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $rawData = file_get_contents("php://input");
            $jsonData = json_decode($rawData, true);
            if (is_array($jsonData)) {
                $_POST = $jsonData;
            }
        }
        if (isset($_POST['g-recaptcha-response'])) {
            unset($_POST['g-recaptcha-response']);
        }
        if (isset($_POST['_wpcf7'])) {
            unset($_POST['_wpcf7']);
        }
        if (isset($_POST['_wpcf7_version'])) {
            unset($_POST['_wpcf7_version']);
        }
        if (isset($_POST['_wpcf7_locale'])) {
            unset($_POST['_wpcf7_locale']);
        }
        if (isset($_POST['_wpcf7_unit_tag'])) {
            unset($_POST['_wpcf7_unit_tag']);
        }
        if (isset($_POST['_wpcf7_container_post'])) {
            unset($_POST['_wpcf7_container_post']);
        }
        if (isset($_POST['_wpcf7_posted_data_hash'])) {
            unset($_POST['_wpcf7_posted_data_hash']);
        }
        if (isset($_POST['vx_width'])) {
            unset($_POST['vx_width']);
        }
        if (isset($_POST['vx_height'])) {
            unset($_POST['vx_height']);
        }
        if (isset($_POST['post_id'])) {
            unset($_POST['post_id']);
        }
        if (isset($_POST['form_id'])) {
            unset($_POST['form_id']);
        }
        if (isset($_POST['referer_title'])) {
            unset($_POST['referer_title']);
        }
        if (isset($_POST['queried_id'])) {
            unset($_POST['queried_id']);
        }


        // Step 2: Parse query string from referer
        $utmParams = [
            // Standard UTM parameters
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'utm_term',
            'utm_id',
        
            // Google Ads
            'gclid',             // Google Click Identifier
            'gad_source',
            'gad_campaignid',
            'gad_medium',
            'gad_term',
            'gad_content',
        
            // Facebook/Meta
            'fbclid',            // Facebook Click Identifier
            'fbc',               // Facebook cookie-based campaign tracking
            'fbp',               // Facebook pixel ID
        
            // Instagram (Meta uses same as Facebook)
            // No unique param, but you can track via `utm_source=instagram` or use `fbclid`
        
            // Microsoft Ads / Bing
            'msclkid',           // Microsoft click ID
        
            // TikTok
            'ttclid',            // TikTok click ID
        
            // Twitter Ads
            'twclid',            // Twitter click ID
        
            // LinkedIn Ads
            'li_fat_id',         // LinkedIn Click ID
        
            // Snapchat Ads
            'sccid',             // Snapchat click ID
        
            // Pinterest Ads
            'epik',              // Pinterest click ID
        
            // Generic click IDs used by some platforms/tools
            'clickid',
            'ad_id',
            'adgroup_id'
        ];

        $link = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (!empty($link)) {
            $urlParts = parse_url($link);
        
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $queryParams);
        
                // Step 3: Add UTM params to $_POST if present in referer URL
                foreach ($utmParams as $param) {
                    if (isset($queryParams[$param])) {
                        // if ($param == 'utm_term') {
                        //     $utm_term = urldecode($queryParams[$param]);
                        //     $_POST[$param] = $utm_term;
                        // }else {
                        // }
                        $_POST[$param] = $queryParams[$param];
                    }
                }
            }
        }
        
        // Step 1: Normalize wpforms fields if present
        // if (isset($_POST['wpforms']['fields']) && is_array($_POST['wpforms']['fields'])) {
        //     $fields = $_POST['wpforms']['fields'];
        
        //     $renamedFields = [];
        //     foreach ($fields as $key => $value) {
        //         if ($key === '1') $renamedFields['name'] = $value;
        //         elseif ($key === '2') $renamedFields['email'] = $value;
        //         elseif ($key === '3') $renamedFields['phone'] = $value;
        //         elseif ($key === '4') $renamedFields['message'] = $value;
        //         elseif ($key === '5') $renamedFields['service'] = $value;
        //     }
        
        //     // Merge renamed into $_POST for consistent field access
        //     $_POST = array_merge($_POST, $renamedFields);
        
        //     // Also replace fields in wpforms for all_data consistency
        //     $_POST['wpforms']['fields'] = $renamedFields;
        // }
        
        // Wp form 
        if (isset($_POST['wpforms']['fields']) && is_array($_POST['wpforms']['fields'])) {
            $fields = $_POST['wpforms']['fields'];
        
            $renamedFields = [];
            foreach ($fields as $key => $value) {
                switch ($key) {
                    case '1':
                        $renamedFields['Name'] = $value;
                        break;
                    case '2':
                        $renamedFields['Email'] = $value;
                        break;
                    case '3':
                        $renamedFields['Phone'] = $value;
                        break;
                    case '4':
                        $renamedFields['Message'] = $value;
                        break;
                    case '5':
                        $renamedFields['Service'] = $value;
                        break;
                    default:
                        // If you want to keep other keys as they are, just copy them
                        $renamedFields[$key] = $value;
                        break;
                }
            }
        
            // Replace original fields with renamed keys
            $_POST['wpforms']['fields'] = $renamedFields;
        
            // Also merge renamed fields into top-level $_POST for convenience
            $_POST = array_merge($_POST, $renamedFields);
        }
        // WP Form End 
        
        // Elementor Form 
            if (isset($_POST['form_fields']) && is_array($_POST['form_fields'])) {
                $fields = $_POST['form_fields'];  // Pehle form_fields ko $fields mein daal do
            
                $renamedFields = [];
            
                if (isset($fields['name'])) {
                    $renamedFields['Name'] = $fields['name'];  // capital 'N' in 'Name'
                }
                if (isset($fields['email'])) {
                    $renamedFields['Email'] = $fields['email'];  // capital 'E' in 'Email'
                }
            
                foreach ($fields as $key => $value) {
                    if (strpos($key, 'phone') !== false || strpos($key, 'field_') === 0) {
                        $renamedFields['Phone'] = $value;  // capital 'P' in 'Phone'
                        break;
                    }
                }
            
                if (isset($fields['message'])) {
                    $renamedFields['Message'] = $fields['message'];  // capital 'M' in 'Message'
                }
            
                // Merge the renamed fields into $_POST, so keys like 'Email', 'Name' are directly accessible
                $_POST = array_merge($_POST, $renamedFields);
            
                // Optional: save the full original data
                // $_POST['all_data'] = json_encode($fields);
            }

        // Elementor Form End

        
        
        // Get POST data and escape it
        $name = $conn->real_escape_string($_POST['Name'] ?? $_POST['your-name']??'');
        $email = $conn->real_escape_string($_POST['Email'] ??$_POST['your-email'] ?? '');
        $phone = $conn->real_escape_string($_POST['PhoneNumber']?? $_POST['your-phone']??$_POST['Phone']?? '');
        $message = $conn->real_escape_string($_POST['TextArea'] ?? $_POST['url'] ??$_POST['your-message'] ?? $_POST['Message'] ?? '');
        $package_name = $conn->real_escape_string($_POST['Packagename'] ?? ($_POST['PackageTitle'] ?? ''));
        $package_price = $conn->real_escape_string($_POST['PackagePrice'] ?? '');
        $alldata = $conn->real_escape_string(json_encode($_POST));
        $ip_address = $conn->real_escape_string($_SERVER['REMOTE_ADDR']);

        
        
        // Insert data into table
        $sql = "INSERT INTO logo_lp_leads (name, email,phone,message,package_name,package_price,all_data,link,ip_address) VALUES ('$name', '$email', '$phone', '$message', '$package_name', '$package_price','$alldata','$link','$ip_address')";
        $conn->query($sql);
        $conn->close();


        $to = 'info@shopifyofficials.com';  // Replace with your email
        $subject = 'New Quote Request From Shopify Officials';


        $message = "<html>
                <head>
                    <title>New Quote Request From Shopify Officials</title>
                    <style>
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid #ccc;
                            padding: 8px;
                        }
                        th {
                            background-color: #f2f2f2;
                            text-align: left;
                        }
                    </style>
                </head>
                <body>
                    <h2>{$_POST['form_title']}</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>";
                foreach ($_POST as $key => $value) {
                    // Sanitize values for HTML output
                    $key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                    $value = nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));

                   $message .= "
                                <tr>
                                    <td><strong>$key</strong></td>
                                    <td>" . ($key == 'PackagePrice' ? '$' . $value : $value) . "</td>
                                </tr>";
                }
                $message .= "
                        </tbody>
                    </table>
                </body>
                </html>";

        // Set content-type header for HTML email
    
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
        // Additional headers
    
        $headers .= "From: no-reply@shopifyofficials.com" . "\r\n";  // Replace with your domain
    
        // Send email
    
        // echo 'success';
        if (mail($to, $subject, $message, $headers)) {
            echo 'success';
        } else {
            echo 'error';
        }
    // }
}

?>