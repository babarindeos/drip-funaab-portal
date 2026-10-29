<x-staff-layout>

    <div class="flex flex-col container mx-4 border border-0 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    My Documents               
                </div>
                
        </section>
    
        @include('partials._document_submenu1')


        <!-- Check if user has documens //-->
        @if ($documents->count())

                <section class="flex flex-col py-1 mt-0 justify-end">
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
                                <th class="text-center font-semibold py-4 w-[15%] md:w-[8%]">SN</th>
                                <th class="font-semibold py-2 text-left">Documents</th>
                                                                
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter = ($documents->currentPage() - 1) * $documents->perPage();
                            @endphp
                            @foreach($documents as $document)
                                <tr class="border border-b border-gray-200 ">
                                    <td class='text-center py-8'>{{ ++$counter }}.</td>
                                    <td class="py-2 pr-4">
                                        <div class='flex flex-col md:flex-row'>
                                                    <div class='flex flex-col md:flex-col w-full md:w-4/5'>
                                                            <div>
                                                                <a href="{{ route('staff.documents.show', ['document'=>$document->id]) }}" class='text-blue-500 underline font-semibold' href=''>
                                                                    {{ $document->title }}
                                                                </a>
                                                            </div>
                                                            <div class='flex flex-col space-y-1 md:flex-row justify-between text-xs'>
                                                                <div>
                                                                        <div>{{ $document->filetype }} ({{ $document->filesize }})</div>

                                                                        <div class="flex flex-col md:flex-row space-y-1 py-1 md:space-x-8 md:space-y-0">
                                                                            @if ($document->workflows->count())
                                                                                <a class="hover:underline cursor-pointer" href="{{ route('staff.workflows.flow', ['document'=>$document->id]) }}" > 
                                                                                    Workflow ({{ $document->workflows->count() }})
                                                                                </a>
                                                                            @endif

                                                                            @if ($document->general_messages->count())
                                                                                <a href="{{ route('staff.workflows.general_message', ['document'=>$document->id]) }}" class="hover:underline cursor-pointer">
                                                                                        General Messages ({{ $document->general_messages->count() }})
                                                                                </a>
                                                                            @endif

                                                                            
                                                                                

                                                                            @if ($document->private_messages->count())
                                                                                <a href="{{ route('staff.workflows.private_messages.my_private_messages', ['document'=>$document->id, 'recipient'=>$document->uploader])}}">
                                                                                    @php
                                                                                        $pmCount = 0;
                                                                                        foreach($document->private_messages as $pm)
                                                                                        {
                                                                                            if ($pm->sender_id==Auth::id() || $pm->recipient_id==Auth::id())
                                                                                            {
                                                                                                $pmCount++;
                                                                                            }
                                                                                        }
                                                                                    @endphp
                                                                                    Private Messages ({{ $pmCount }})
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                </div>
                                                                <!--
                                                                <div class="md:px-4 border border-0">
                                                                    <span class="px-2 py-1 rounded-md" style="background-color: #daf1e6;">
                                                                        {{ $document->uuid }}
                                                                    </span>
                                                                </div>
                                                                //-->
                                                            </div>
                                                    </div>
                                                    <div class='flex flex-col w-full md:w-1/5 text-sm md:text-md'>
                                                            {{ $document->created_at->format('l jS F, Y @ g:i a')}}
                                                    </div>
                                        </div>
                                    
                                    </td>
                                    
                                    
                                </tr>
                            @endforeach
                            
                        </tbody>

                    </table>

                    {{ $documents->links() }}


                </section>
        @else
                <div class="my-16 border border-1 rounded md:w-1/2">
                        <div class="flex p-8">
                                You currently have no documents uploaded by you.
                        </div>

                </div>
        
        @endif
    </div>
    
    </x-staff-layout>

<script>
    $(document).ready(function(){
        $("input[name='search']").bind("keyup", function(){
            var value = $(this).val().toLowerCase();
            
            $("table tbody tr").filter(function(){
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    })


</script>