<x-admin-layout>
    <div class="container mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-1 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div class="flex flex-1" >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Admin Documents</h1>
                    </div>

                    <!-- create Admin Document Type  //-->
                    <div class="flex flex-1 justify-end border-0 space-x-4">
                        <a href="{{  route('admin.admin_categories.index') }}" class="border boder px-8 py-2 text-sm border-green-500 rounded-md hover:text-white hover:bg-green-600">
                            Admin Categories
                        </a>

                        <a href="{{ route('admin.admin_category_types.index') }}" class="border boder px-8 py-2 text-sm border-green-500 rounded-md hover:text-white hover:bg-green-600">
                            Category Types
                        </a>


                    </div>
                    <!-- end of Admin Document Type //-->                    
                    
            </div>
        </section>
        <!-- end of page header //-->

       
    </div>



</x-admin-layout>