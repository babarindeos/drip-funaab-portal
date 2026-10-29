<x-admin-layout>

<div class="container mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-1 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div class="flex flex-1" >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Admin Category</h1>
                    </div>

                    <!-- create Admin Document Type  //-->
                    <div class="flex flex-1 justify-end border-0 space-x-4">
                        <a href="{{ route('admin.admin_categories.index') }}" class="border boder px-8 py-2 text-sm border-green-500 rounded-md hover:text-white hover:bg-green-600">
                            Admin Categories
                        </a>

                        <a href="{{ route('admin.admin_category_types.index') }}" class="border boder px-8 py-2 text-sm border-green-500 rounded-md hover:text-white hover:bg-green-600">
                            Admin Category Types
                        </a>
                    </div>
                    <!-- end of Admin Document Type //-->                    
                    
            </div>
        </section>
        <!-- end of page header //-->


        <!-- new admin category type form //-->
        <section>
                <div>
                    <form  action="{{ route('admin.admin_categories.store')}} " method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center">
                        @csrf
                       

                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >New Admin Category</h2>
                            Select Category Type and provide Admin Category
                        </div>


                        @include('partials._session_response')
                        

                        <!-- Category Type //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-2">
                                
                                
                            <select name="category_type" class="border border-1 border-gray-400 bg-gray-50
                                                                     w-full p-4 rounded-md 
                                                                     focus:outline-none
                                                                     focus:border-blue-500 
                                                                     focus:ring
                                                                     focus:ring-blue-100"                                                                                                                                                                                                                                                                                                                                                
                                                                     
                                                                     style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                     required
                                                                     >
                                                                    <option value=''>-- Select Category Type --</option>
                                                                    @foreach($admin_category_types as $cat_types)
                                                                        <option value="{{ $cat_types->id }}">{{ $cat_types->name }}</option>
                                                                    @endforeach
                                                                                                                                
                            </select>

                            @error('category_type]')
                            <span class="text-red-700 text-sm">
                                {{$message}}
                            </span>
                            @enderror
                            
                        </div>
                        
                        <!-- end of Category Type //-->

                        

                        <!-- Category name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="name" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Category Name"
                                                                    
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


                        <!-- Category code //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="code" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Category Code"
                                                                    
                                                                    value="{{ old('code') }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('code')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of category code //-->

                        

                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Create Category</button>
                        </div>
                        
                    </form><!-- end of new college form //-->
                <div>
        </section>
        <!-- end of new college form //-->





</div>



</x-admin-layout>