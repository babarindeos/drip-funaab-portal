<x-admin-layout>
    <div class="container mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-1 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div class="flex flex-1" >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Documents</h1>
                    </div>
                    
                    <!-- search //-->
                    <div class="flex flex-1 justify-end border-0">
                    
                        <input type="text" name="search" class="w-full md:w-4/5 border border-1 border-gray-400 bg-gray-50
                                    p-2 rounded-md 
                                    focus:outline-none
                                    focus:border-blue-500 
                                    focus:ring
                                    focus:ring-blue-100" placeholder="Search"                
                                
                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                  
                        
                        />  
                    </div>



                    <!-- end of search //-->
            </div>
        </section>
        <!-- end of page header //-->

        @if (count($documents))
                <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-4 py-2">
                    
                        <table class="table-auto border-collapse border border-1 border-gray-200">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th width="5" class="text-center font-semibold py-2 w-16">SN</th>
                                    <th width="35%" class="font-semibold py-2 text-start">Title</th>
                                    <th width="20%" class="font-semibold py-2 text-start">File</th>
                                    <th width="20%" class="font-semibold py-2 text-start">Owner</th>                    
                                    <th width="20%" class="font-semibold py-2 text-start">Date</th>                            
                                </tr>                        
                            </thead>
                            <tbody>
                                @php
                                    $counter = ($documents->currentPage() - 1 ) * $documents->perPage();
                                @endphp

                                @foreach ($documents as $document)
                                    <tr class="border border-b border-gray-200">
                                        <td class="text-center py-4"> {{ ++$counter }}. </td>
                                        <td>
                                            <a class="hover:underline" href="{{ route('admin.documents.show', ['document'=>$document->id])}}">
                                                {{ $document->title }}
                                            </a>
                                            <div>
                                                <div class="text-sm">Workflows ({{$document->workflows->count()}})</div>

                                            </div>
                                        
                                        </td>
                                        <td class="text-sm">
                                            <a class="hover:underline" title="{{ $document->comment }}" href="{{ asset('storage/'.$document->document)}}">
                                                {{ $document->filetype }} ({{ $document->filesize}})
                                            </a>
                                        </td>
                                        <td>{{ $document->owner->staff->surname }} {{ $document->owner->firstname}}</td>
                                        <td>{{ $document->created_at->format('l jS F, Y')}}</td>
                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                        <div class="py-2">
                            {{ $documents->links()}}
                        </div>
                    

                </section>
        @endif

    </div>


</x-admin-layout>