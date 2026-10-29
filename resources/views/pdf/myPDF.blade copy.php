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
    </head>
<body>
        <div class="border-0 flex flex-row justify-center items-center font-bold text-blue-900" style="font-size:34px;font-weight:900;color:navy;">
            OGUN-OSHUN RIVER BASIN DEVELOPMENT AUTHORITY
        </div>
        <div class="border-0 flex flex-row justify-center items-center text-2xl font-semibold" style="font-family: 'Dancing Script';">
            Managing Director's Office
        </div>

        <div class="border-0 flex flex-row justify-center items-center"> 
            <div class="flex w-[80%] border-0">           
                    <!-- left pane//-->
                    <div class="flex flex-1 flex-col">
                          <div class="flex flex-row w-full py-1" >
                                <div class="flex " style="font-family:'Dancing Script'" >
                                     Our Ref:
                                </div>
                                <div class="flex-1 border-b px-1" style="border-bottom:1px solid black;">

                                </div>
                          </div>
                          <div class="border-1 flex flex-row w-full py-1">
                                <div class="flex " style="font-family:'Dancing Script'">
                                        Your Ref:
                                </div>
                                <div class="flex-1 border-b px-1" style="border-bottom:1px solid black;">
                                        hhhhhh
                                </div>
                          </div>
                          <div>
                                Website: www.oorbda.org
                          </div>
                          <div>
                                E-mail: info@oorbda.org
                          </div>
                    </div>
                    <!-- end of left pane //-->

                    <!-- middle pane //-->
                    <div class="border-0 flex flex-1 justify-center items-center">
                            <img src="{{ asset('images/logo.jpg') }}" style="width:100px;" />
                    </div>
                    <!-- end of middle pane //-->

                    <!-- right pane //-->
                    <div class="border-0 flex flex-col flex-1 justify-end">
                            <div class="border-0 flex justify-end">Headquarters:</div>
                            <div class="flex justify-end">Alabata Road, P.M.B 2115</div>
                            <div class="flex justify-end">Abeokuta, Ogun State.</div>
                            <div class="flex justify-end items-center"><i class="fa-solid fa-tty px-1"></i>  :039-779422</div>
                    </div>
                    <!-- end of right pane //-->
            </div>

        </div>


        

</body>
</html>