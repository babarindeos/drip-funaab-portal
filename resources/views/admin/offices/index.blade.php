<x-admin-layout>
    <div class="container border-0 mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-2 mt-6 px-4 md:px-1 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Offices</h1>
                    </div>
                    <div>
                            <a href="{{ route('admin.offices.create') }}" class="bg-green-600 text-white py-2 px-4 
                                            rounded-lg text-sm hover:bg-green-500">New Office</a>
                    </div>
            </div>
        </section>
        <!-- end of page header //-->

        @if (count($offices))
            
        
            <section class="flex flex-col py-2 px-2 justify-end w-[90%] md:w-[80%] mx-auto md:px-1">
                <div class="flex justify-end border border-0">
                
                    <input type="text" name="search" class="w-full md:w-2/5 border border-1 border-gray-400 bg-gray-50
                                p-2 rounded-md 
                                focus:outline-none
                                focus:border-blue-500 
                                focus:ring
                                focus:ring-blue-100" placeholder="Search"                
                            
                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                  
                    
                    />  
                </div>
                
            </section>

            <section class="flex flex-col w-[90%] md:w-[80%] mx-auto px-2 md:px-1">
                <table class="table-auto border-collapse border border-1 border-gray-200" 
                            >
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="text-center font-semibold py-2 py-4" width='10%'>SN</th>
                            <th class="font-semibold py-2 text-left">Office</th>
                            <th class="font-semibold py-2 text-left">Parent Office</th>
                            <th class="font-semibold py-2 text-center">Action</th>
                        </tr>
                    </head>
                    <tbody>
                        @php
                            $counter = ($offices->currentPage() -1) * $offices->perPage();
                        @endphp
                        
                        @foreach($offices as $office)
                            <tr class="border border-b border-gray-200 ">
                                <td class='text-center py-4'>{{ ++$counter }}.</td>
                                <td class='py-8'>
                                    <a class="hover:underline" href="{{ route('admin.offices.show', ['office'=>$office->id]) }}" >
                                            {{ $office->name }}
                                    </a>                      
                                </td>
                                <td>        @if ($office->parent)
                                                {{ $office->parent->name }}
                                            @endif
                                </td>
                               
                                <td class="text-center">
                                    <span class="text-sm">
                                        <a class="hover:bg-blue-500 bg-blue-400 text-white rounded-md 
                                                px-4 py-1 text-xs" href="{{ route('admin.offices.edit', ['office'=>$office->id])}}">Edit</a>
                                    </span>
                                    <span> 
                                        <a class="hover:bg-red-500 bg-red-400 text-white rounded-md 
                                                px-4 py-1 text-xs" href="{{ route('admin.offices.confirm_delete', ['office'=>$office->id])}}"
                                        >Delete</a>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                <div class="py-2">
                        {{ $offices->links() }}
                </div>


            </section>
        @else
            <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-4">
                    <div class="flex flex-row border-0 justify-center 
                                text-2xl font-semibold text-gray-300 py-8">
                            There is currently No Office
                    </div>
            </section>
        @endif
    </div>
</x-admin-layout>

<script>
    $(document).ready(function(){
        var value;
        $("input[name='search']").on("keyup", function(){
            value = $(this).val().toLowerCase();
            $("table tbody tr").filter(function(){
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

