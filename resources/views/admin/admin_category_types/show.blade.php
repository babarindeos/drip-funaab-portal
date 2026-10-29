<x-admin-layout>
    
<div class="container mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-1 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div class="flex flex-1" >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Admin Category Type</h1>
                    </div>

                    <!-- create Admin Document Type  //-->
                    <div class="flex flex-1 justify-end border-0 space-x-4">
                        <a class="border boder px-8 py-2 text-sm border-green-500 rounded-md hover:text-white hover:bg-green-600">
                            Admin Categories
                        </a>

                        <a href="{{ route('admin.admin_category_types.create') }}" class="border boder px-8 py-2 text-sm border-green-500 rounded-md hover:text-white hover:bg-green-600">
                            Create Category Types
                        </a>


                    </div>
                    <!-- end of Admin Document Type //-->                    
                    
            </div>
        </section>
        <!-- end of page header //-->


        <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-4 py-2">
            <div class="text-xl text-gray-600 font-medium">
                {{ $admin_category_type->name }}
            </div>


        </section>








</div>

</x-admin-layout>