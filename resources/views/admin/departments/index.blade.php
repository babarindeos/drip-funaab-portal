<x-admin-layout>
    <div class="container mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-2 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Departments </h1>
                    </div>
                    <div>
                            <a href="{{ route('admin.departments.create') }}" class="bg-green-600 text-white py-2 px-4 
                                            rounded-lg text-xs md:text-sm hover:bg-green-500">
                                            <i class="fa-regular fa-building mr-2 opacity-50 hover:opacity-100"></i>
                                            New Department</a>
                    </div>
            </div>
        </section>
        <!-- end of page header //-->



        @if (count($departments) > 0)
                <section class="flex flex-col py-2 px-2 justify-end w-[93%] mx-auto md:px-1">
                    <div class="flex justify-end border border-0">
                    
                        <input type="text" name="search" class="w-4/5 md:w-2/5 border border-1 border-gray-400 bg-gray-50
                                    p-2 rounded-md 
                                    focus:outline-none
                                    focus:border-blue-500 
                                    focus:ring
                                    focus:ring-blue-100" placeholder="Search"                
                                
                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                  
                        
                        />  
                    </div>
                    
                </section>

                <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-4">
                    <table class="table-auto border-collapse border border-1 border-gray-200" 
                                >
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="text-center font-semibold py-4 w-16">SN</th>
                                <th class="font-semibold py-2 text-left">Directorate</th>
                                <th class="font-semibold py-2 text-left">Department Name</th>
                                <th class="font-semibold py-2 text-left">Department Code</th>
                                <th class="font-semibold py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter = ($departments->currentPage() -1 ) * $departments->perPage();
                            @endphp

                                @foreach ($departments as $department)
                                <tr class="border border-b border-gray-200">
                                    <td class='text-center py-4'>{{ ++$counter }}.</td>
                                    <td class='py-8'>
                                        {{ $department->directorate->name }} <small>({{$department->directorate->code}})</small>
                                        
                                    </td>
                                    <td>
                                        {{ $department->name }}
                                        {{-- <div class="text-sm">
                                            Staff ({{ $department->staff->count()}})
                                        </div> --}}
                                    
                                    </td>
                                    <td>{{ $department->code }}</td>
                                    <td class="text-center">
                                        <span class="text-sm">
                                            <a class="hover:bg-blue-500 bg-blue-400 text-white rounded-md 
                                                    px-4 py-1 text-xs" href="{{ route('admin.departments.edit', ['department'=>$department->id])}}">Edit</a>
                                        </span>
                                        <span> 
                                            <a class="hover:bg-red-500 bg-red-400 text-white rounded-md 
                                                    px-4 py-1 text-xs" href="{{ route('admin.departments.confirm_delete', ['department'=>$department->id])}}"
                                            >Delete</a>
                                        </span>
                                    </td>

                                </tr>
                                @endforeach
                            
                            
                        </tbody>

                    </table>

                    <div class="mt-1">
                        {{ $departments->links() }}

                    </div>


                </section>
        @else
                <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-4">
                        <div class="flex flex-row border-0 justify-center 
                                    text-2xl font-semibold text-gray-300 py-8">
                                There is currently No Department 
                        </div>
                </section>
        @endif
        
        
    </div>
</x-admin-layout>

<script>
    $(document).ready(function(){
         $("input[name='search']").on('keyup', function(){
                var value = $(this).val().toLowerCase();
                
                $("table tbody tr").filter(function(){
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1 );
                });
         });
    });

</script>