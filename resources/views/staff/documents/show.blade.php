<x-staff-layout>

    <div class="flex flex-col container mx-4 border border-0 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    Documents               
                </div>
                
        </section>
    
        @include('partials._document_submenu1')


        <section class="py-8 mt-2">

            <div class="flex flex-col md:flex-row w-full space-y-4 md:space-y-0 md:space-x-4 ">
                    <!-- left panel //-->
                    <div class="flex flex-col w-full md:w-3/5 border border-1 rounded-md py-2 px-4">
                            <div class="border-b border-1 py-2">
                                    <div class="font-semibold">
                                        {{ $document->title}}
                                    </div>
                                    <div class="flex flex-col md:flex-row border border-0 justify-between md:items-center ">

                                        <div class="text-xs">
                                            Submitted by {{ $document->staff->surname}} {{ $document->staff->firstname}} @ {{ $document->created_at->format('l jS F, Y  g:i a') }}
                                        </div>

                                        <div class="flex flex-row space-x-4 border border-0">
                                                <div class="text-sm underline">Edit</div>
                                                <div class="text-sm underline">Delete</div>
                                        </div>
                                        
                                    </div>
                            </div>
                            <div class="flex flex-row text-sm py-8 justify-content items-center">
                                        <a href="{{ asset('storage/'.$document->document) }}" target="_blank" 
                                                class="px-6 py-6 text-center border border-1 hover:bg-blue-100 
                                                    rounded-md hover:border-blue-100 justify-center">
                                                <div class="flex justify-center">
                                                        @if ($document->filetype == "MS Word")
                                                        <img src="{{ asset('images/icon_doc_50.jpg') }}"  />
                                                        @elseif ($document->filetype == "PDF")
                                                        <img src="{{ asset('images/icon_pdf_50.jpg') }}" />
                                                        @elseif ($document->filetype == "Image | jpg")
                                                        <img src="{{ asset('images/icon_image_50.jpg') }}"/>
                                                        @endif
                                                </div>

                                                <div class="">
                                                        {{ $document->filetype }}
                                                </div>
                                                <div class="text-xs">
                                                        {{ $document->filesize }}
                                                </div>
                                        </a>
                                        <div class="px-4 py-2">
                                                @if ($document->comment=='')
                                                    No Comment

                                                @else
                                                    {{ $document->comment }}
                                                @endif 

                                                <!-- workflow start or continue //-->
                                                <div class="py-4">
                                                        @if ($workflowCount == 0)
                                                                <span class="border border-1 
                                                                               py-1 px-4 text-sm rounded-md
                                                                               bg-green-500 text-white border-green-500">
                                                                        <a href="{{ route('staff.workflows.flow', ["document"=>$document->id])}}">
                                                                                Start Workflow
                                                                        </a>
                                                                    
                                                                </span>
                                                        @else
                                                                <span class="border border-1 
                                                                                py-1 px-4 text-sm rounded-md
                                                                                bg-green-500 text-white border-green-500">
                                                                        <a href="{{ route('staff.workflows.flow', ["document"=>$document->id])}}">
                                                                                Workflow
                                                                        </a>
                                                                
                                                                </span>
                                                        @endif
                                                </div>
                                        </div>
                            </div>

                    </div>
                    <!-- end of left panel //-->

                    <!-- right panel //-->
                    <div class="w-full md:w-2/5 md:flex-grow border border-1 py-4 px-4 rounded-md">
                            <div class="font-semibold text-gray-500">
                                    Workflow Contributors
                            </div>

                            <div class="w-full mt-2">
                                @foreach($workflow_contributors as $contributor)
                                    <div class="w-full py-1">
                                            <div class="flex flex-row w-full text-sm border-b ">
                                                    <div class="flex flex-col justify-center px-4 py-2 items-center">
                                                                                                                       
                                                            @if ($contributor->user->profile!=null && $contributor->user->profile->avatar!="" )
                                                                            
                                                                            <img src="{{ asset('storage/'.$contributor->user->profile->avatar)}}" class='w-12 h-10 rounded-full' />
                                                                        
                                                            @else
                                                                            <img class="w-12" src="{{ asset('images/avatar_64.jpg')}}" />  
                                                            @endif
                                                    </div>
                                                    <div class="flex flex-col py-2 w-full">
                                                        <a href="{{ route('staff.profile.email_user_profile', ['email'=>$contributor->user->email]) }}" class="font-bold hover:underline">
                                                                {{ $contributor->user->staff->surname}}  {{ $contributor->user->staff->firstname}}
                                                        </a>
                                                        @if ($contributor->user->profile != null)                                                               
                                                                <div>{{ $contributor->user->profile->designation }} </div>
                                                        @endif
                                                        
                                                    </div>
                                            </div>
                                    </div>
                                @endforeach
                            </div>
                    </div>
                    <!-- end of right panel //-->
            </div>
             
        </section>
        
    </div>
    
    </x-staff-layout>