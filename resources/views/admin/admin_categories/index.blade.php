<x-admin-layout>
    <div class="container mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-1 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div class="flex flex-1" >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Admin Categories</h1>
                    </div>

                    <!-- create Admin Document Type  //-->
                    <div class="flex flex-1 justify-end border-0 space-x-4">
                        <a href="{{  route('admin.admin_categories.create') }}" class="border boder px-8 py-2 text-sm border-green-500 rounded-md hover:text-white hover:bg-green-600">
                            Create Admin Category
                        </a>

                        <a href="{{ route('admin.admin_category_types.index') }}" class="border boder px-8 py-2 text-sm border-green-500 rounded-md hover:text-white hover:bg-green-600">
                            Category Types
                        </a>


                    </div>
                    <!-- end of Admin Document Type //-->                    
                    
            </div>
        </section>
        <!-- end of page header //-->



        
        <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-4 py-2">
                    
                        <table class="table-auto border-collapse border border-1 border-gray-200">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th width="5%" class="text-center font-semibold py-2 w-16">SN</th>
                                    <th width="30%" class="font-semibold py-2 text-start">Category Type</th>               
                                    <th width="35%" class="font-semibold py-2 text-start">Category Name</th>    
                                    <th width="10%" class="font-semibold py-2 text-start">Category Code</th>  
                                    <th width="20%" class="font-semibold py-2 text-center">Action</th>                         
                                </tr>                        
                            </thead>
                            <tbody>
                                @php
                                    $counter = 0;
                                @endphp

                                @foreach ($admin_categories as $category)
                                    <tr class="border border-b border-gray-200">
                                        <td class="text-center py-4"> {{ ++$counter }}. </td>
                                        <td>
                                                {{ $category->admin_category_type->name}}
                                        </td>
                                        <td>
                                            <a class="hover:underline" href="">
                                                {{ $category->name }}
                                            </a>                                        
                                        </td>

                                        <td>
                                            <a class="hover:underline" href="">
                                                {{ $category->code }}
                                            </a>                                        
                                        </td>
                                        
                                        
                                        
                                        <td class="text-center">
                                            <span class="text-sm">
                                                <a class="hover:bg-blue-500 bg-blue-400 text-white rounded-md 
                                                        px-4 py-1 text-xs" href="{{ route('admin.admin_categories.edit',['admin_category'=>$category->id]) }}">Edit</a>
                                            </span>
                                            <span> 
                                                <a class="hover:bg-red-500 bg-red-400 text-white rounded-md 
                                                        px-4 py-1 text-xs disabled" href="" 
                                                >Delete</a>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
        </section>



       
    </div>

</x-admin_layout>