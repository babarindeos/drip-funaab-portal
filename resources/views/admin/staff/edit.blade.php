<x-admin-layout>
    <div class="container mx-auto mb-8">
        <!-- page header //-->
        <section class="flex flex-col w-[90%] md:w-[95%] py-8 px-4 border-red-900 mx-auto">
            
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Edit Staff</h1>
                    </div>                
            </div>
            
        </section>
        <!-- end of page header //-->



        <!-- new college form //-->
        <section>
                <div>
                    <form  action="{{ route('admin.staff.update', ['staff' => $staff->id])}}" method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center">
                        @csrf

                        

                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >Edit Staff</h2>
                            Provide Staff Information
                        </div>


                        @include('partials._session_response')               

                         <!-- Title //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-2">
                                
                                
                            <input type='text' name="title" class="border border-1 border-gray-400 bg-gray-50
                                                                     w-full p-4 rounded-md 
                                                                     focus:outline-none
                                                                     focus:border-blue-500 
                                                                     focus:ring
                                                                     focus:ring-blue-100"
                                                                     value="{{ $staff->title }}"
                                                                     placeholder = "Title"
                                                                     
                                                                     
                                                                     style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                     required
                                                                     >                                                               

                                                                     @error('title')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                     @enderror
                            
                        </div>                        
                        <!-- end of Title //-->


                        
                        <!-- Surname //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="surname" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Surname"
                                                                    
                                                                    value="{{ $staff->surname }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('surname')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Surname //-->


                         <!-- Firstname //-->
                         <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="firstname" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Firstname"
                                                                    
                                                                    value="{{ $staff->firstname }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('firstname')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of firstname //-->


                        <!-- Middlename //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="middlename" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Middlename"
                                                                    
                                                                    value="{{ $staff->middlename }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('middlename')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of middlename //-->


                        <!-- File No. //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="fileno" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Staff No."
                                                                    
                                                                    value="{{ $staff->fileno }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('fileno')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of File No. //-->



                        <!-- Office //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-2">
                                
                                
                            <select name="office" class="border border-1 border-gray-400 bg-gray-50
                                                                     w-full p-4 rounded-md 
                                                                     focus:outline-none
                                                                     focus:border-blue-500 
                                                                     focus:ring
                                                                     focus:ring-blue-100"
                                                                     
                                                                     
                                                                     style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                     required
                                                                     >
                                                                    <option value=''>-- Select Office --</option>
                                                                        @foreach($offices as $office)
                                                                            <option value="{{ $office->id }}" @if($staff->office_id==$office->id) selected @endif>{{ $office->name }}</option> 
                                                                        @endforeach                                                                                                                                       
                                                                    </select>

                                                                     @error('office')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                     @enderror
                            
                        </div>                        
                        <!-- end of Office //-->




                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Update Staff</button>
                        </div>
                        
                    </form><!-- end of new college form //-->
                <div>
        </section>
        <!-- end of new college form //-->


    </div><!-- end of container //-->
</x-admin-layout>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function(){
        
        $("#segment").bind("change", function(){
            var segment_id = $(this).val();

            $.ajax({
                    url: "{{ route('admin.staff.organs.fetch_organ') }}",
                    method: 'GET',
                    data: { segment_id: segment_id },
                    success: function(response){
                        console.log(response)
                        
                        if (!response || $.trim(response) === "" || response.length === 0) 
                        {
                            //console.log("empty");
                            
                        }
                        else
                        {
                            $("#organ").html(response);
                        }                      
                       
                    },
                    error: function(){
                        
                    }
            });

        });
            
            
    });


        
  


</script>