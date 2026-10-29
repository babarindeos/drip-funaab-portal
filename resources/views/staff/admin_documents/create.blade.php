<x-staff-layout>

    <div class="flex flex-col container mx-4 border border-0 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    Admin Document               
                </div>
                
        </section>
    
        @include('partials._document_submenu1')
    
        <section class="py-8 mt-2">
            <div>
                <form  action="{{ route('staff.admin_documents.store')}} " method="POST" enctype="multipart/form-data" class="flex flex-col mx-auto w-[90%] items-center justify-center">
                    @csrf

                    

                    <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                        <h2 class="font-semibold text-xl py-1" >Upload Document</h2>
                        Provide document and information about the document...
                    </div>


                    @include('partials._session_response')
                    
                    

                    <!-- Document title //-->
                    <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                    
                        
                        <input type="text" name="document_title" class="border border-1 border-gray-400 bg-gray-50
                                                                w-full p-4 rounded-md 
                                                                focus:outline-none
                                                                focus:border-blue-500 
                                                                focus:ring
                                                                focus:ring-blue-100" placeholder="Title of the document"
                                                                
                                                                value="{{ old('document_title') }}"
                                                                
                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                required
                                                                />  
                                                                                                                                    

                                                                @error('document_title')
                                                                    <span class="text-red-700 text-sm">
                                                                        {{$message}}
                                                                    </span>
                                                                @enderror
                        
                    </div><!-- end of document title //-->


                    <!-- category type //-->
                    <div class="flex flex-col w-[80%] md:w-[60%] py-3">
                        <select name="category_type" id="category_type" class="border border-1 border-gray-400 bg-gray-50
                                                                     w-full p-4 rounded-md 
                                                                     focus:outline-none
                                                                     focus:border-blue-500 
                                                                     focus:ring
                                                                     focus:ring-blue-100"
                                                                     
                                                                     
                                                                     style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                     required
                                                                     >
                                                                        <option value=''>-- Select Category Type --</option>
                                                                        @foreach ($admin_category_types as $category)
                                                                                <option value='{{$category->id}}'>{{$category->name}}</option>
                                                                        @endforeach
                                                                        

                                                                    </select>
                                                                    
                                                                     @error('category_type')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                     @enderror
                            
                    </div>                        
                    <!-- end of Category type //-->


                    <!-- category //-->
                    <div class="flex flex-col w-[80%] md:w-[60%] py-3">
                        <select name="category" id='category' class="border border-1 border-gray-400 bg-gray-50
                                                                     w-full p-4 rounded-md 
                                                                     focus:outline-none
                                                                     focus:border-blue-500 
                                                                     focus:ring
                                                                     focus:ring-blue-100"
                                                                     
                                                                     
                                                                     style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                     required
                                                                     >
                                                                        <option value=''>-- Select Category --</option>                                                               
                                                                        

                                                                    </select>
                                                                   
                                                                     @error('category')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                     @enderror
                            
                    </div>                        
                    <!-- end of Category //-->


                    <!-- document file //-->
                    <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-2">
                                
                                
                        <input type="file" name="document" class="border border-1 border-gray-400 bg-gray-50
                                                                 w-full p-4 rounded-md 
                                                                 focus:outline-none
                                                                 focus:border-blue-500 
                                                                 focus:ring
                                                                 focus:ring-blue-100" 
                          
                         style="font-family:'Lato';font-size:16px;font-weight:500;"
                         accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx"
                         required />
                            

                         @error('document')
                            <span class="text-red-700 text-sm">
                                {{$message}}
                            </span>
                         @enderror
                        
                    </div>
                    <!-- end of document file //-->
                   
                    <!-- end of upload //-->


                    <!-- Comment //-->
                    <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                    
                        
                        <textarea name="comment" class="border border-1 border-gray-400 bg-gray-50
                                                                w-full p-4 rounded-md 
                                                                focus:outline-none
                                                                focus:border-blue-500 
                                                                focus:ring
                                                                focus:ring-blue-100" 
                                                                
                                                                value="{{ old('comment') }}"
                                                                
                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                maxlength="140"
                                                                >  </textarea>
                                                                <div class="text-xs text-gray-600">Comment (140 words)</div>
                                                                                                                                    

                                                                @error('comment')
                                                                    <span class="text-red-700 text-sm">
                                                                        {{$message}}
                                                                    </span>
                                                                @enderror
                        
                    </div><!-- end of comment //-->

                    <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                        <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                       hover:bg-gray-500
                                       rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Submit Document</button>
                    </div>
                    
                </form><!-- end of new college form //-->
            <div>

        </section>
        
    </div>
    
    </x-staff-layout>

<script>
$(document).ready(function(){
   $("#category_type").bind('change', function(){
        // get the selected item
        var cat_type = $(this).val();
        var url = "{{ url('staff/admin_documents') }}" + '/' + cat_type + '/get_category';
        
        console.log(url);
        if (cat_type != "")
        {
            $.ajax({
                url: url,
                data: {'category_type': cat_type},
                dataType: 'html',
                method: 'GET',
                success: function(response){
                    console.log(response);
                    $("#category").html(response);

                },
                error: function(xhr, status, error){
                    console.log(error);
                }
            });
        }

   })


})


</script>



