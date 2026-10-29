<x-staff-layout>
    
    <div class="flex flex-col container mx-4 border border-0 md:mx-auto">
        <!-- Page Header //-->
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    Create Category               
                </div>                
        </section>
         <!-- end of Page Header //-->

         <!-- Category pane //-->
        <section class="flex flex-col md:flex-row border-0 mt-2">
            <div class="flex flex-col w-full md:w-3/5 border-0 py-4 px-4">
                <div>
                    <form  action="{{ route('staff.categories.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col mx-auto w-[90%] items-center justify-center border-0">
                            @csrf

                            <!-- session response //-->
                            <div class="flex flex-col border-0 w-full items-center justify-center">
                                    @if (session('error'))

                                        @if (session('status')=='success')
                                            <span class="flex flex-col w-[80%] md:w-[80%] py-4 px-4 my-2 bg-green-50 rounded text-green-800 font-medium" 
                                                    style="font-family:'Lato'; font-size:16px;"> 
                                                {{ session('message') }}
                                            </span>
                                        @else
                                            <span class="flex flex-col w-[80%] md:w-[80%] py-4 px-4 my-2 bg-red-50 rounded text-red-800 font-medium" 
                                                    style="font-family:'Lato'; font-size:16px;">
                                                {{ session('message') }}
                                            </span>
                                        @endif
                            
                                    @endif
                            </div>
                            <!-- end of session response //-->

                            <!-- Category name //-->
                            <div class="flex flex-col border-red-900 w-[90%] md:w-[80%] py-1 mt-2">
                            
                                
                                <input type="text" name="name" class="border border-1 border-gray-400 bg-gray-50
                                                                        w-full p-4 rounded-md 
                                                                        focus:outline-none
                                                                        focus:border-blue-500 
                                                                        focus:ring
                                                                        focus:ring-blue-100" placeholder="Category name"
                                                                        
                                                                        value="{{ old('name') }}"
                                                                        
                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                        required
                                                                        />  
                                                                                                                                            

                                                                        @error('name')
                                                                            <span class="text-red-700 text-sm">
                                                                                {{$message}}
                                                                            </span>
                                                                        @enderror
                                
                            </div><!-- end of category name //-->


                            <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-1">
                                <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                               hover:bg-gray-500
                                               rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Create Category</button>
                            </div>

                    </form>
                </div>
    
            </div>
            <div class="flex flex-col w-full md:w-2/5 border border-1 py-4 px-4">
                    <div class="text-lg font-semibold border-b border-1 border-gray-200">  
                        My Categories
                    </div>
                    <div class="px-8 py-2">
                        <ul class="list-disc px-12">
                        @foreach ($categories as $category)
                            <li class="py-1">
                                {{ $category->name }}
                            </li>
                        @endforeach
                        </ul>

                    </div>
            </div>
        </section>
        <!-- end of category pane //-->
    </div>
   

    



</x-staff-layout>