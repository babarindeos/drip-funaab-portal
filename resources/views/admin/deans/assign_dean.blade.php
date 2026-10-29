<x-admin-layout>
    <div class="container mx-auto mb-8">
        <!-- page header //-->
        <section class="flex flex-col w-[90%] md:w-[95%] py-8 px-4 border-red-900 mx-auto">
            
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Assign Dean</h1>
                    </div>                
            </div>
            
        </section>
        <!-- end of page header //-->


        
        

                   
           
                    <!--  Get Assigned College  //-->
                    <section>
                            <div>
                                <form action="{{ route('admin.deans.store_assign_dean')}} " method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center">
                                    @csrf

                                    

                                       


                                        @include('partials._session_response')

                                        <!-- College //-->
                                    
                                        <div class="flex flex-1 flex-col border-red-900  w-[90%] md:w-[60%]">
                                                        <!-- College long name and code //-->                          
                                                        <div class="border border-1 border-gray-400 bg-gray-50
                                                                                                w-full p-4 rounded-md 
                                                                                                "                                                                       
                                                                                                
                                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                >          
                                                                        {{ $college->college_name}}                        
                                                        </div><!-- end of college name and code //-->
                                                        <input type="text" name="college" value="{{$college->id}}" />


                                                         

                                                    @if ($current_dean)     

                                                        <div class="flex flex-col mt-6 font-semibold border-b border-gray-200 py-2" style="font-family:'Lato'; font-size:17px; ">
                                                            Current Dean Information
                                                        </div>
                                                        

                                                        <!-- Dean title, names //-->                          
                                                        <div name="start_date" class="border border-1 border-gray-400 bg-gray-50
                                                        w-full p-4 rounded-md mt-4"                                                                       
                                                        
                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                        >                                  
                                                                        {{ $current_dean->staff->title}} {{ $current_dean->staff->surname}} {{ $current_dean->staff->firstname }}
                                                        </div>
                                                        <input type="text" name="current_dean_id" value="{{$current_dean->staff_id}}" />
                                                        <!-- end of dean title, names //-->

                                                        <!-- Start Date //-->
                                                        <div class="mt-2 py-2 font-semibold text-sm">
                                                                Start Date
                                                        </div>
                                                        @php
                                                            $datetime = \Carbon\Carbon::parse($current_dean->start_date);
                                                        @endphp
                                                                                  
                                                        <div class="border border-1 py-3 border-gray-400 bg-gray-50
                                                                                                w-full p-4 rounded-md 
                                                                                                "                                                                       
                                                                                                
                                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                >          
                                                                {{ $datetime->format('l, j F Y')}}        
                                                        </div><!-- end of start date //-->

                                                    <!-- end date /-->
                                                    <div class="mt-2 font-semibold text-sm">
                                                        End Date
                                                    </div>
                                                    <!-- Dates //-->
                                                    <div class="flex py-2 space-x-2">     
                                                        
                                                                <!-- Day //-->
                                                                <div class="flex flex-col border-red-900 ">
                                                                    <select name="end_day" class="border border-1 border-gray-400 bg-gray-50
                                                                        w-full text-sm md:text-base py-4 md:px-8 rounded-md 
                                                                        focus:outline-none
                                                                        focus:border-blue-500 
                                                                        focus:ring
                                                                        focus:ring-blue-100"
                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                        
                                                                    >
                                                                        <option value=''>-- Select Day --</option>
                                                                        @for ($i= 1; $i <= 31; $i++)
                                                                            <option>{{ $i }}</option>
                                                                        @endfor                                                                 
                                                                    </select>

                                                                    @error('end_day')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror                                                          
                                                                </div><!-- end of day //-->


                                                                <!-- Month //-->
                                                                <div class="flex flex-col border-red-900 ">
                                                                    <select name="end_month" class="border border-1 border-gray-400 bg-gray-50
                                                                        w-full text-sm md:text-base py-4 md:px-8 rounded-md 
                                                                        focus:outline-none
                                                                        focus:border-blue-500 
                                                                        focus:ring
                                                                        focus:ring-blue-100"
                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                        
                                                                    >
                                                                        <option value=''>-- Select Month --</option>
                                                                        <option value='01'>January</option>
                                                                        <option value='02'>February</option>
                                                                        <option value='03'>March</option>
                                                                        <option value='04'>April</option>
                                                                        <option value='05'>May</option>
                                                                        <option value='06'>June</option>
                                                                        <option value='07'>July</option>
                                                                        <option value='08'>August</option>
                                                                        <option value='09'>September</option>
                                                                        <option value='10'>October</option>
                                                                        <option value='11'>November</option>
                                                                        <option value='12'>December</option>
                                                                                                                                  
                                                                    </select>

                                                                    @error('end_month')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror                                                          
                                                                </div><!-- end of month //-->


                                                                <!-- Year //-->
                                                                <div class="flex flex-col border-red-900 ">
                                                                    <select name="end_year" class="border border-1 border-gray-400 bg-gray-50
                                                                        w-full text-sm md:text-base py-4 md:px-8 rounded-md 
                                                                        focus:outline-none
                                                                        focus:border-blue-500 
                                                                        focus:ring
                                                                        focus:ring-blue-100"
                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                        
                                                                    >
                                                                        <option value=''>-- Select Year --</option>
                                                                        @for ($i=2020; $i <= 2050; $i++)
                                                                            <option>{{ $i }}</option>
                                                                        @endfor
                                                                                                                                  
                                                                    </select>

                                                                    @error('end_year')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror                                                          
                                                                </div><!-- end of year //-->

                                                    </div><!-- end of dates //-->
                                                    <!-- End Dates //-->
                                        @endif


                                        <!-- New Dean //-->


                                        <!-- end of new dean //-->
                                        <div class="flex flex-col mt-8 font-semibold border-b border-gray-200 py-2" style="font-family:'Lato'; font-size:17px; ">
                                            New Dean Information
                                        </div>

                                        <!-- Staff //-->
                                        <div class="flex flex-col border-red-900 mt-3">
                                            <select name="new_dean" class="border border-1 border-gray-400 bg-gray-50
                                                w-full text-sm md:text-base py-4 md:px-8 rounded-md 
                                                focus:outline-none
                                                focus:border-blue-500 
                                                focus:ring
                                                focus:ring-blue-100"
                                                style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                
                                            >
                                                <option value=''>-- Select Staff --</option>
                                                @foreach ($staff as $st)
                                                    <option value="{{$st->id}}">{{ $st->title }} {{ $st->surname }} {{ $st->firstname }}</option>
                                                @endforeach                                                           
                                            </select>

                                            @error('end_day')
                                                <span class="text-red-700 text-sm">
                                                    {{$message}}
                                                </span>
                                            @enderror                                                          
                                        </div><!-- end of staff //-->

                                        <!-- start date /-->
                                        <div class="mt-2 font-semibold text-sm">
                                            Start Date
                                        </div>
                                        <!-- Dates //-->
                                        <div class="flex py-2 space-x-2">     
                                            
                                                    <!-- Day //-->
                                                    <div class="flex flex-col border-red-900 ">
                                                        <select name="start_day" class="border border-1 border-gray-400 bg-gray-50
                                                            w-full text-sm md:text-base py-4 md:px-8 rounded-md 
                                                            focus:outline-none
                                                            focus:border-blue-500 
                                                            focus:ring
                                                            focus:ring-blue-100"
                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                            
                                                        >
                                                            <option value=''>-- Select Day --</option>
                                                            @for ($i= 1; $i <= 31; $i++)
                                                                <option>{{ $i }}</option>
                                                            @endfor                                                                 
                                                        </select>

                                                        @error('start_day')
                                                            <span class="text-red-700 text-sm">
                                                                {{$message}}
                                                            </span>
                                                        @enderror                                                          
                                                    </div><!-- start of day //-->


                                                    <!-- Month //-->
                                                    <div class="flex flex-col border-red-900 ">
                                                        <select name="start_month" class="border border-1 border-gray-400 bg-gray-50
                                                            w-full text-sm md:text-base py-4 md:px-8 rounded-md 
                                                            focus:outline-none
                                                            focus:border-blue-500 
                                                            focus:ring
                                                            focus:ring-blue-100"
                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                            
                                                        >
                                                            <option value=''>-- Select Month --</option>
                                                            <option value='01'>January</option>
                                                            <option value='02'>February</option>
                                                            <option value='03'>March</option>
                                                            <option value='04'>April</option>
                                                            <option value='05'>May</option>
                                                            <option value='06'>June</option>
                                                            <option value='07'>July</option>
                                                            <option value='08'>August</option>
                                                            <option value='09'>September</option>
                                                            <option value='10'>October</option>
                                                            <option value='11'>November</option>
                                                            <option value='12'>December</option>
                                                                                                                      
                                                        </select>

                                                        @error('start_month')
                                                            <span class="text-red-700 text-sm">
                                                                {{$message}}
                                                            </span>
                                                        @enderror                                                          
                                                    </div><!-- start of month //-->


                                                    <!-- Year //-->
                                                    <div class="flex flex-col border-red-900 ">
                                                        <select name="start_year" class="border border-1 border-gray-400 bg-gray-50
                                                            w-full text-sm md:text-base py-4 md:px-8 rounded-md 
                                                            focus:outline-none
                                                            focus:border-blue-500 
                                                            focus:ring
                                                            focus:ring-blue-100"
                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                            
                                                        >
                                                            <option value=''>-- Select Year --</option>
                                                            @for ($i=2020; $i <= 2050; $i++)
                                                                <option>{{ $i }}</option>
                                                            @endfor
                                                                                                                      
                                                        </select>

                                                        @error('start_year')
                                                            <span class="text-red-700 text-sm">
                                                                {{$message}}
                                                            </span>
                                                        @enderror                                                          
                                                    </div><!-- start of year //-->

                                        </div><!-- end of dates //-->
                                        <!-- Start Dates //-->

                                        <div class="flex flex-col border-red-900 w-[100%] md:w-[100%] mt-4">
                                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                                           hover:bg-gray-500
                                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Assign Dean</button>
                                        </div>
                                
                                </form>                    

                            </div>
                    </section>
                    <!--  End of Assigned Dean//-->
                    
                
            
       


    </div><!-- end of container //-->
</x-admin-layout>