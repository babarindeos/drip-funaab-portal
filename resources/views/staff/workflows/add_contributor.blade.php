<x-staff-layout>

    <div class="flex flex-col container mx-4 border border-0 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    Workflow - Add Contributor          
                </div>
                
        </section>
    
        @include('partials._document_submenu1')


        <section class="py-8 mt-2">

            <div class="flex flex-col md:flex-row w-full space-y-4 md:space-y-0 md:space-x-4 ">
                    <!-- left panel //-->
                    <div class="flex flex-col w-full md:w-3/5 border border-1 rounded-md py-2 px-4">
                            <div class="border-b border-1 py-2">
                                    <div class="font-semibold text-lg">
                                        {{ $document->title}}
                                    </div>
                                    <div class="flex flex-col md:flex-row border border-0 justify-between md:items-center ">

                                        <div class="text-xs">
                                            Submitted by {{ $document->staff->surname}} {{ $document->staff->firstname}} @ {{ $document->created_at->format('l jS F, Y  g:i a') }}
                                        </div>

                                       
                                        
                                    </div>
                            </div>
                            <div class="flex flex-row text-sm py-4 justify-content items-start">
                                        <div class="flex flex-col w-1/5">
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
                                        </div>
                                        <div class="px-4 py-2 border border-0 w-full">
                                               

                                                <!-- workflow start or continue //-->
                                                <div class="flex flex-col md:flex-row py-4 border">

                                                    <!-- forward to contributor //-->
                                                    <div class="flex flex-col flex-1">
                                                            <div class="font-semibold py-2 text-base text-gray-600">Add a contributor to this document</div>

                                                            <form action="{{ route('staff.workflows.search_staff', ['document'=>$document->id]) }}" method="post">
                                                                @csrf

                                                                <!-- Contributor panel //-->
                                                                <div class="flex flex-col md:flex-row w-full border border-0">
                                                                    <!-- Select List to Contributors //-->
                                                                    <div class="flex flex-col border-red-900 w-[97%] md:w-[70%] py-2 border-0">
                                                                            
                                                                            
                                                                            <input type="text" name="staffno" class="border border-1 border-gray-400 bg-gray-50
                                                                                                                    w-[100%] p-2 md:rounded-l-md
                                                                                                                    focus:outline-none
                                                                                                                    focus:border-blue-500 
                                                                                                                    focus:ring
                                                                                                                    focus:ring-blue-100"                                                                                                                                                                                                                                                                                                                                                
                                                                                                                    placeholder="Enter Staff No."

                                                                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                                                                    required
                                                                                                                    />
                                                                                                                    
                                                                                                                                                                                            
                                                                            

                                                                            @error('staffno')
                                                                                <span class="text-red-700 text-sm">
                                                                                    {{$message}}
                                                                                </span>
                                                                            @enderror
                                                                        
                                                                    </div>                                                                
                                                                    <!-- end of List of Contributors //-->

                                                                    <!-- search button //-->
                                                                    <div class="flex border border-0 md:justify-center md:items-center">
                                                                        <button type="submit" class="border border-1
                                                                            md:rounded-r-md py-3 px-4 text-xs text-green-500 border-green-500 hover:bg-green-500 hover:text-white font-semibold">
                                                                            Search
                                                                        </button>
                                                                    </div>
                                                                    <!-- end of search button //-->

                                                                </div>                                                            
                                                                <!-- end of contributor panel //-->
                                                            </form>


                                                            



                                                                <!-- submit button //-->
                                                                <div class="flex py-1">
                                                                    @if (session('error'))
                                                                        @if (session('status')=='fail')
                                                                                <span class="flex flex-col w-[80%] md:w-[60%] py-4 px-2 my-2 bg-red-50 rounded text-red-800 font-medium" 
                                                                                    style="font-family:'Lato'; font-size:16px;"> 
                                                                                    <div class="font-semibold text-xs mb-1">[Process Error]</div>
                                                                                    {{ session('message') }}
                                                                                </span>
                                                                        @endif

                                                                    @else
                                                                        @if (session('status')=='success' && session('staff'))
                                                                            @php
                                                                                $staff = session('staff');
                                                                            @endphp
                                                                            <div class="flex flex-col border border-1 w-full py-4 px-4">
                                                                                    <div class="flex flex-row">
                                                                                            <div class="py-2 px-4">
                                                                                                    <div class="rounded-full w-50 h-50 bg-gray-200">
                                                                                                        @if ($staff->profile!=null && $staff->profile->avatar!="" )
                                                                            
                                                                                                        <img src="{{ asset('storage/'.$staff->profile->avatar)}}" class='w-12 h-10 rounded-full' />
                                                                                                    
                                                                                                        @else
                                                                                                            <img class="w-12" src="{{ asset('images/avatar_64.jpg')}}" />  
                                                                                                        @endif
                                                                                                   
                                                                                                    </div>
                                                                                            </div>
                                                                                            <div>
                                                                                                    <div>
                                                                                                            <div class='font-semibold'>{{ $staff->title}} {{$staff->surname}} {{$staff->firstname}}, {{$staff->fileno}}</div>
                                                                                                            <div>{{ session('organ')->name }} ({{ session('organ')->code }})</div>
                                                                                                            <div>{{$staff->segment->name}}</div>
                                                                                                    </div>

                                                                                                    <form action="{{ route('staff.workflows.post_add_contributor',['document'=>$document->id]) }}" method="POST">
                                                                                                        @csrf
                                                                                                        <div class="mt-2">
                                                                                                            <input type="hidden" name='user_id' value="{{$staff->user_id}}" />
                                                                                                            <button type="submit" class="border border-1 border-green-500
                                                                                                            bg-green-500 text-white rounded-md py-2 px-4 text-xs font-semibold">
                                                                                                                Add as Contributor
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </form>
                                                                                            </div>

                                                                                    </div>

                                                                                    
                                                                            </div>

                                                                        @endif
                                                                    
                                                                            
                                                                                                                                                   
                                                                                
                                                                           
                                                                    @endif
                                                                </div>
                                                                <!-- end of submit button //-->



                                                            
                                                            <!-- end of forward button //-->



                                                    </div>
                                                    <!-- end of forward to contributor //-->

                                                    <!-- Add contributor //-->
                                                    <div class="flex flex-col justify-center border border-0 md:items-center md:px-2 py-2 ">
                                                                
                                                               

                                                    </div>
                                                    <!-- end of Add contributor //-->

                                                    

                                                        
                                                </div>
                                        </div>
                            </div>

                    </div>
                    <!-- end of left panel //-->

                    <!-- right panel //-->
                    <div class="w-full md:w-2/5 md:flex-grow border border-1 py-4 px-4 rounded-md">
                            <div class="font-semibold text-gray-500 mb-2" >
                                    Workflow Contributors
                            </div>

                            <div class="w-full ">
                                    @foreach($workflow_contributors as $contributor)
                                        <div class="w-full ">
                                                <div class="flex flex-row w-full text-sm border-b ">
                                                        <div class="flex flex-col justify-center px-2 py-2 items-center">
                                                                
                                                                @if ($contributor->user->profile!=null && $contributor->user->profile->avatar!="" )
                                                                            
                                                                    <img src="{{ asset('storage/'.$contributor->user->profile->avatar)}}" class='w-12 h-10 rounded-full' />
                                                            
                                                                @else
                                                                    <img class="w-12" src="{{ asset('images/avatar_64.jpg')}}" />  
                                                                @endif                                                         
                                                        </div>
                                                        <div class="flex flex-col py-2 w-full">
                                                            <div class="font-bold">{{ $contributor->user->staff->surname}}  {{ $contributor->user->staff->firstname}}</div>
                                                            @if ($contributor->user->profile != null)
                                                                <div>{{ $contributor->user->profile->designation }}</div>
                                                            @endif

                                                            <div class="w-full">
                                                                @if (Auth::user()->id == $contributor->add_initiator->id)
                                                                    <div class="flex text-end justify-end "> 
                                                                        <a class="flex text-xs bg-red-300 px-2 py-1 text-white rounded-md hover:bg-red-400">   
                                                                            Delete
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
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