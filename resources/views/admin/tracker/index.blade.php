<x-admin-layout>
    <div class="container flex flex-col mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-2 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Tracker</h1>
                    </div>
                    
            </div>
        </section>
        <!-- end of page header //-->

        <!-- Search //-->
        <Section class="border-0 border-red-600 w-1/2 mx-auto">
                <form method="GET" action="{{ route('admin.tracker.index')}}" class="flex flex-row border-0  border-blue-900 w-full space-x-1">
                        <div class="flex flex-1">
                                <input type="text" name="q" class="border border-1 border-gray-400 bg-gray-50
                                        w-full p-4 rounded-md 
                                        focus:outline-none
                                        focus:border-blue-500 
                                        focus:ring
                                        focus:ring-blue-100" placeholder="Track Documents..."
                                        
                                        value="{{ old('name') }}"
                                        
                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                        required
                                        />  
                                                                                                            

                                        @error('name')
                                            <span class="text-red-700 text-sm">
                                                {{$message}}
                                            </span>
                                        @enderror

                        </div><!-- end of search //-->
                        <!-- button //-->
                        <div>
                            <button type="submit" class="border border-1 bg-green-600 py-4 px-4 text-white 
                                     hover:bg-green-500 rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Search</button>
                        </div>
                        <!-- end of button //-->
                </form>
        </Section>
        <!-- end of Search //-->


        @if ($isPostBack)
            <!-- Search Results //-->
           
            @if ($documents->total() > 0 )
                <Section class="w-[95%] md:w-[95%] mx-auto px-4">
                    <div class="py-8">
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

                    </div>
                </Section>
                <!-- end of Search Results //-->
            @else
                <div class="mx-auto py-8 md:py-12">
                        <div class="text-lg">
                            No record is found that meets the search criteria, try again.
                        </div>
                </div>
            @endif
        @endif

    </div><!-- end of container //-->
</x-admin-layout>