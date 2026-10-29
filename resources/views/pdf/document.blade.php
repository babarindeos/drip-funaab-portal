<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Staff Stats') }}</title>

    <meta name="csrf_token" content="{{ csrf_token() }}" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Sacramento&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
            /* Custom CSS classes */
        .header-title {
            font-size: 34px;
            font-weight: 900;
            color: navy;
        }

        .managing-director {
            font-family: 'Dancing Script', cursive;
        }

        .ref-label {
            font-family: 'Dancing Script', cursive;
        }

        .border-b-black {
            border-bottom: 1px solid black;
        }

        .logo-img {
            width: 100px;
        }

        .headquarters-text {
            display: flex;
            justify-content: end;
        }
    </style>
    
</head>
<body>
    <div style="font-size:24px; color:navy; font-weight:bold; border:0px solid black; text-align:center;">
        OGUN-OSHUN RIVER BASIN DEVELOPMENT AUTHORITY
    </div>
    <div style="font-family:'Dancing Script'; font-size:16px;text-align:center;">
        Managing Director's Office
    </div>
    
    <!-- Table  //-->
    <div style="text-align: center; margin-top:10px;">
        <table style="margin: 0 auto; width:100%; border:0px solid black; " >
            <tr>
                <td style="width:40%">
                    <table style="width:100%;padding:3px;">
                        <tr>
                            <td style="width:30%; padding-top:0px;font-size:14px">Our Ref:</td>
                            <td style="border-bottom:1px solid #000000; width:70%; padding-top:0px;"></td>
                        </tr>
                        <tr>
                            <td style="width:30%;padding-top:5px;font-size:14px">Your Ref:</td>
                            <td style="border-bottom:1px solid #000000; width:70%; padding-top:5px;"></td>
                        </tr>
                        <tr colspan="2">
                            <td colspan="2" style="font-size:14px;"> <span style="font-weight:bold">website:</span> www.oorbda.org</td>
                        </tr>
                        <tr colspan="2">
                            <td colspan="2" style="font-size: 14px;"> <span style="font-weight:bold;">e-mail:</span> info@oorbda.org</td>
                        </tr>


                    </table>

                </td>
                <td style="width:20%; text-align:center">
                        <img src="{{ public_path('images/logo.jpg') }}" style="width:90px;"  />
                </td>
                <td style="width:40%">
                    <table style="border:0px solid #000000;width:100%; font-size:14px;">
                        <tr>
                            <td style="text-align:right;font-weight:bold;">Headquarters:</td>
                        </tr>
                        <tr>
                            <td style="text-align:right;font-weight:bold;">Alabata Road, P.M.B 2115</td>
                        </tr>
                        <tr>
                            <td style="text-align:right;font-weight:bold;">Abeokuta, Ogun State.</td>
                        </tr>
                        <tr>
                            <td style="text-align:right;font-weight:bold;"><i class="fa-solid fa-tty px-1"></i>  :039-779422</td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
    </div>
    <!-- end of Table //-->

    <!-- body //-->
    <div style="padding-left:10px; padding-right:10px; margin-top:30px;line-height:1.5">
        {{ $message }}
    </div>
    <!-- end of body //-->


</body>
</html>