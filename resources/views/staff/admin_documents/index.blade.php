<x-staff-layout>

    <div class="flex flex-col container mx-4 border border-0 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    Admin Documents               
                </div>
                
        </section>
    
        @include('partials._document_submenu1')


        <section class="my-4 border-0">
            <div class="flex flex py-4 justify-end">
                    <a href="{{ route('staff.admin_documents.create') }}" class="underline hover:font-semibold hover:no-underline hover:text-green-900">
                        Create Document
                    <a>
            </div>
        </section>



        @if (count($documents))
                
            <section class="flex flex-col py-1  mt-4 justif-end">
                <div class="flex justify-end border border-0">
                
                    <input type="text" name="search" class="w-3/5 md:w-2/5 border border-1 border-gray-400 bg-gray-50
                                p-2 rounded-md 
                                focus:outline-none
                                focus:border-blue-500 
                                focus:ring
                                focus:ring-blue-100" placeholder="Search"                
                            
                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                  
                    
                    />  
                </div>
                
            </section>

            <section class="flex flex-col py-2 ">
                <table class="table-auto border-collapse border border-1 border-gray-200" 
                            >
                    <thead>
                        <tr class="bg-gray-200">
                            <th width="10%" class="text-center font-semibold py-2 w-16">SN</th>
                            <th width="45%" class="font-semibold py-2 text-left">Documents</th>
                            <th width="30%" class="font-semibold py-2 text-left">Category</th>
                            <th width="15%" class="font-semibold py-2 text-left">Date Created</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $counter = ($documents->currentPage() - 1) * $documents->perPage();
                        @endphp
                        @foreach($documents as $document)
                            <tr class="border border-b border-gray-200 ">
                                <td class='text-center py-4'>{{ ++$counter }}.</td>
                                <td class="py-2 pr-4">
                                    
                                    <div>
                                        <a href="#" class='text-blue-500 underline font-semibold' href=''>
                                            {{ $document->title }}
                                        </a>
                                    </div>
                                    <div class='flex flex-col space-y-1 md:flex-row justify-between text-xs'>
                                        <div class="flex flex-col">
                                                <div class="flex flex-row space-x-2">
                                                    <div>{{ $document->filetype }} ({{ $document->filesize }})</div>
                                                    <div>{{ $document->owner->surname }} {{ $document->owner->firstname }}</div>
                                                </div>                                                
                                        </div>                                        
                                    </div>
                                
                                </td>

                                <td>
                                        {{ $document->admin_category->name }}
                                        <div class="text-sm">
                                            {{ $document->admin_category->admin_category_type->name }}
                                        </div>

                                </td>


                                <td width="20%" class="text-sm">
                                        <div class="px-0">
                                            {{ $document->created_at->format('l jS F, Y @ g:i a')}}
                                        </div>
                                </td>
                                
                            </tr>
                        @endforeach
                        
                    </tbody>

                </table>

                {{ $documents->links() }}


            </section>
        @else
            <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-4">
                <div class="flex flex-row border-0 justify-center 
                            text-2xl font-semibold text-gray-300 py-8">
                        There is currently no Admin Document
                </div>
            </section>
        @endif
        
        
        
    </div>
    
    </x-staff-layout>

<script>
    $(document).ready(function() {
        $("input[name='search']").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            
            $("table tbody tr").filter(function() {
                // Get the text content excluding the title link
                // Get the text content excluding the title link
                var rowText = $(this).find("td").not(":first").text().toLowerCase();
                $(this).toggle(rowText.indexOf(value) > -1 || $(this).find("td").length === 1); // Keep the heading row visible
            });
        });
    });
</script>