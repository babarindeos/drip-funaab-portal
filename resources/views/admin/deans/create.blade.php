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


        
        <!-- new college form //-->
        <section>
                <div>
                    <form action="{{ route('admin.deans.get_assigned_dean')}} " method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center">
                        @csrf

                        

                        <div class="flex flex-col w-[90%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            
                            Provide College Information
                        </div>


                        @include('partials._session_response')
                        
                        

                         <!-- College //-->
                         <div class="flex  space-x-2 w-[90%] md:w-[60%]">
                                    <div class="flex flex-1 flex-col border-red-900  py-2">
                                            
                                            
                                        <select name="college" class="border border-1 border-gray-400 bg-gray-50
                                                                                w-full p-4 rounded-md 
                                                                                focus:outline-none
                                                                                focus:border-blue-500 
                                                                                focus:ring
                                                                                focus:ring-blue-100"
                                                                                
                                                                                
                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                                required
                                                                                >
                                                                                <option value=''>-- Select College --</option>
                                                                                    @foreach($colleges as $college)
                                                                                        <option class='py-4' value="{{$college->id}}">{{$college->college_name}} ({{$college->college_code}})</option>
                                                                                    @endforeach                                                                    
                                                                                </select>

                                                                                @error('college')
                                                                                    <span class="text-red-700 text-sm">
                                                                                        {{$message}}
                                                                                    </span>
                                                                                @enderror
                                        
                                    </div> 

                                    <div class="flex flex-col justify-center items-center">
                                        <button type="submit" class="border border-1 bg-gray-400 py-4 px-2 text-white 
                                             hover:bg-gray-500 rounded-md text-sm md:text-lg" style="font-family:'Lato';font-weight:500;">Get College</button>
                                    </div>
                        </div>                       
                        <!-- end of College name //-->

                    </form><!-- end of new college form //-->                        
                        
                    
                <div>
        </section>
        <!-- end of new college form //-->
        

        

    </div><!-- end of container //-->
</x-admin-layout>