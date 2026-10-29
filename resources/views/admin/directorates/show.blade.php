<x-admin-layout>
    <div class="container">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-2 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Directorate</h1>
                        <div>{{ $directorate->name }} ({{ $directorate->code }})</div>
                    </div>
                    
            </div>
        </section>
        <!-- end of page header //-->
    


        <section class="flex flex-col md:flex-row w-[95%] md:w-[95%] py-2 mt-6 px-4 mx-auto">
            

            <!-- Departments //-->
            <div class="flex flex-col flex-1 border border-0">
                    <div class="flex justify-between border border-0 py-1">
                        <div class="flex flex-row justify-start items-center">
                             <span class="text-lg font-semibold">Departments ({{ $departments->count() }}) </span>
                        </div>
                        <input type="text" name="search" class="w-3/5 md:w-3/5 border border-1 border-gray-400 bg-gray-50
                                    p-2 rounded-md 
                                    focus:outline-none
                                    focus:border-blue-500 
                                    focus:ring
                                    focus:ring-blue-100" placeholder="Search"                
                                
                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                  
                        
                        />  
                    </div>

                    <table  class="table-auto border-collapse border border-1 border-gray-200 w-full">
                        <thead>
                            <tr class="border-b-1 border border-1 border-gray-200" >
                                <th class="py-1">SN</th>
                                <th class="text-left">Departments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter = 0;
                            @endphp
                            @foreach ($departments as $department)
                                <tr class="border-b-1 border-gray-200">
                                    <td class="text-center py-1">{{ ++$counter }}.</td>
                                    <td>{{ $department->department_name }}</td>
                                </tr>
                            @endforeach                            
                        </tbody>
                    </table>

                    <div>
                        {{ $departments->links()}}
                    </div>

            </div>
            <!-- end of departments //-->

            <!-- Users //-->
            <div class="flex flex-1">

            </div>
            <!-- end of User //-->

        </section>

    </div><!-- end of container //-->


</x-admin-layout>