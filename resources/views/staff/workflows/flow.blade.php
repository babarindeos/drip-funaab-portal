<x-staff-layout>

    <div class="flex flex-col container mx-4 mb-8 border border-0 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    Workflow            
                </div>
                
        </section>
    
        @include('partials._document_submenu1')


        <section class="flex flex-col md:flex-row md:space-x-4 py-8 mt-2 border-0 border-blue-900">

            <!-- left column //-->
            <div class="flex flex-col w-full md:w-3/5 space-y-4 md:space-y-8  border-0 border-green-900 ">                   
                    

                    <!-- Top Panel - Document //-->                    
                    <div class="flex flex-col w-full md:w-full border border-1 rounded-md py-2 px-4">
                            <div class="border-b border-1 py-2">
                                    <div class="font-semibold">
                                        {{ $document->title}}
                                    </div>
                                    <div class="flex flex-col md:flex-row border border-0 justify-between md:items-center ">

                                        <div class="text-xs">
                                            Submitted by {{ $document->staff->surname}} {{ $document->staff->firstname}} @ {{ $document->created_at->format('l jS F, Y  g:i a') }}
                                        </div>

                                       
                                        
                                    </div>
                            </div>
                            <div class="flex flex-row text-sm py-4 justify-content items-start">
                                        <div class="flex flex-col w-2/5 md:w-1/5">
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
                                                @if ($document->comment=='')
                                                    No Comment

                                                @else
                                                    {{ $document->comment }}
                                                @endif 


                                                <!-- check if the user is the current handler //-->
                                            @if ($current_handler == Auth::user()->id)

                                                <!-- workflow start or continue //-->
                                                <div class="flex flex-col md:flex-row py-4">

                                                    <!-- forward to contributor //-->
                                                    <div class="flex flex-col flex-1">
                                                            <div class="font-semibold py-2">Forward to</div>

                                                            <form action="{{ route('staff.workflows.forward_document',['document'=>$document->id]) }}" method="POST">
                                                                @csrf

                                                                <!-- Contributor panel //-->
                                                                <div class="flex flex-col md:flex-row w-full border border-0">
                                                                    <!-- Select List to Contributors //-->
                                                                    <div class="flex flex-col border-red-900 w-[97%] md:w-[72%] py-2">                                                                            
                                                                            
                                                                            <select name="contributor" id="contributor" class="border border-1 border-gray-400 bg-gray-50
                                                                                                                    w-full py-1 px-2 rounded-md 
                                                                                                                    focus:outline-none
                                                                                                                    focus:border-blue-500 
                                                                                                                    focus:ring
                                                                                                                    focus:ring-blue-100"                                                                                                                                                                                                                                                                                                                                                
                                                                                                                    
                                                                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                                                                    required
                                                                                                                    >
                                                                                                                    <option value=''>-- Select Contributor --</option>

                                                                                                                    @foreach ($workflow_contributors as $contributor)
                                                                                                                        @if ($contributor->user_id != Auth::user()->id)
                                                                                                                            <option value='{{ $contributor->user_id}}'>{{ $contributor->user->staff->surname }} {{  $contributor->user->staff->firstname }}</option>
                                                                                                                        @endif
                                                                                                                    @endforeach
                                                                                                                                                                                            
                                                                            </select>

                                                                            @error('contributor')
                                                                                <span class="text-red-700 text-sm">
                                                                                    {{$message}}
                                                                                </span>
                                                                            @enderror
                                                                        
                                                                    </div>                                                                
                                                                    <!-- end of List of Contributors //-->



                                                                    <!-- Add contributor //-->
                                                                    <div class="flex flex-col justify-center border border-0 md:items-center md:px-2 py-2 ">
                                                                                            
                                                                                    <div>
                                                                                        <a href="{{ route('staff.workflows.add_contributor', ['document'=>$document->id])}}" class="border border-1
                                                                                            rounded-md py-2 px-4 text-xs text-green-500 border-green-500 hover:bg-green-500 hover:text-white font-semibold">
                                                                                            Add Contributor
                                                                                        </a>
                                                                                    </div>

                                                                    </div>
                                                                    <!-- end of Add contributor //-->

                                            
                                                                </div>                                                            
                                                                <!-- end of contributor panel //-->




                                                                    <div id='status_comment_panel' style="display:none;"><!-- group div for status and comment //-->

                                                                            <!-- Select Status //-->
                                                                            <div class="flex flex-col border-red-900 w-[97%] md:w-[95%] py-2">                                                                            
                                                                                    
                                                                                <select name="status" class="border border-1 border-gray-400 bg-gray-50
                                                                                                                        w-full px-2 py-1 rounded-md 
                                                                                                                        focus:outline-none
                                                                                                                        focus:border-blue-500 
                                                                                                                        focus:ring
                                                                                                                        focus:ring-blue-100"                                                                                                                                                                                                                                                                                                                                                
                                                                                                                        
                                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                                                                        required
                                                                                                                        >
                                                                                                                        <option value=''>-- Select Status --</option>
                                                                                                                        <option value='approved'>Approved </option>
                                                                                                                        <option value='unapproved'>Not Approved </option>
                                                                                                                                                                                                
                                                                                </select>

                                                                                @error('status')
                                                                                    <span class="text-red-700 text-sm">
                                                                                        {{$message}}
                                                                                    </span>
                                                                                @enderror
                                                                            
                                                                            </div>                                                                
                                                                            <!-- end of Status //-->


                                                                            <!-- Comment //-->
                                                                            <div class="flex flex-col border-red-900 w-[80%] md:w-[95%] py-1">
                                                                            
                                                                                
                                                                                <textarea name="comment" class="border border-1 border-gray-400 bg-gray-50
                                                                                                                        w-full p-1 py-2 rounded-md 
                                                                                                                        focus:outline-none
                                                                                                                        focus:border-blue-500 
                                                                                                                        focus:ring
                                                                                                                        focus:ring-blue-100" 
                                                                                                                        
                                                                                                                        value="{{ old('comment') }}"
                                                                                                                        
                                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                                        maxlength="140"
                                                                                                                        >  </textarea>
                                                                                                                        <div class="text-xs text-gray-600">Comment (140 words)</div>
                                                                                                                                                                                            

                                                                                                                        @error('comment')
                                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                                {{$message}}
                                                                                                                            </span>
                                                                                                                        @enderror
                                                                                
                                                                            </div><!-- end of comment //-->


                                                                            <!-- submit button //-->

                                                                            <button type="submit" class="border border-1 border-green-500
                                                                                bg-green-500 text-white rounded-md py-2 px-4 text-xs font-semibold mt-1">
                                                                                Send
                                                                            </button>
                                                                            <!-- end of submit button //-->

                                                                            <!-- response //-->
                                                                            @if (session('error'))
                                                                                    @if (session('status')=='fail')
                                                                                            <span class="flex flex-col w-[80%] md:w-[60%] py-4 px-2 my-2 bg-red-50 rounded text-red-800 font-medium" 
                                                                                                style="font-family:'Lato'; font-size:16px;"> 
                                                                                                <div class="font-semibold text-xs mb-1">[Process Error]</div>
                                                                                                {{ session('message') }}
                                                                                            </span>
                                                                                    @endif
                                                                            @endif
                                                                            <!-- end of response //-->


                                                                    </div><!-- end of group div for status and comment //-->                                                               

                                                                



                                                            </form>
                                                            <!-- end of forward button //-->



                                                    </div>
                                                    <!-- end of forward to contributor //-->

                                                    

                                                        
                                                </div>
                                                <!-- end of  Workflow start or Continue//-->
                                            @endif



                                        </div>
                            </div>

                    </div>
                    <!-- end of Top panel -Document //-->



                    <!-- Bottom panel - Workflow //-->
                    <section class="border border-0 border-red-900 w-full space-x-2 py-4">
                        <div class="flex flex-col md:flex-row space-x-4">
                            <div class="w-full ">
                                    <div class="border-b py-2 px-4 font-semibold text-gray-700 rounded-md">
                                        <i class="fa fa-envelope-o" aria-hidden="true"></i> Workflow Transactions ({{ $workflow_transactions->count() }})
                                    </div>
                                    <div class="px-4 py-2 space-y-4">
                                        @foreach($workflow_transactions as $transactions)
                                            <div class="rounded-md bg-gray-100 px-2 py-2">
                                                    <div class="text-sm">From <span class="font-medium">{{ $transactions->sender->surname}} {{ $transactions->sender->firstname }}</span> --&raquo; <span class="font-medium">{{$transactions->recipient->surname}} {{$transactions->recipient->firstname}}</span></div>
                                                    <div class="text-xs">
                                                        {{ $transactions->created_at->format('l jS F, Y @ g:i a')}}
                                                    </div>
                                                    <div class="py-2">
                                                        <div>
                                                            @if ($transactions->status=='approved')
                                                                <span class='bg-green-200 px-2 py-1 text-xs rounded-md'>
                                                                    {{ $transactions->status}}
                                                                </span>
                                                            @else
                                                                <span class='bg-red-200 px-2 py-1 text-xs rounded-md'>
                                                                    {{ $transactions->status}}
                                                                </span>
                                                            @endif
                                                            
                                                        </div>
                                                        <div class="text-sm">
                                                            {{ $transactions->comment}}
                                                        </div>

                                                    </div>

                                            </div>
                                        @endforeach

                                    </div>
                            </div>
                        </div>
                    </section>
                    <!-- end of Bottom panel -->

                    
            </div>
            <!-- end of left Column //-->



            <!-- Right column //-->
            <section class="flex flex-col md:w-2/5">
                        <div class="flex flex-col border border-0 w-full space-y-8">
                                    <!-- right panel //-->
                                    <div class="w-full  border border-1 py-4 px-4 rounded-md">
                                        <div class="flex justify-between ">
                                                <div class="font-semibold text-gray-500">
                                                    Workflow Contributors
                                                </div>
                                                <div class="text-xs">                                        
                                                    <a href="{{ route('staff.workflows.general_message',['document'=>$document->id]) }}" class="border border-1 rounded-md border-green-500 py-1 px-3
                                                            hover:bg-green-500 hover:text-white">
                                                            General Message
                                                    </a>
                                                </div>
                                        </div>

                                        <div class="w-full mt-2">
                                            @foreach($workflow_contributors as $contributor)
                                                <div class="w-full py-1">
                                                        <div class="flex flex-row w-full text-sm border-b ">
                                                                <div class="flex flex-col justify-center px-4 py-2 items-center">
                                                                        @if ($contributor->user->profile!=null && $contributor->user->profile->avatar!='')
                                                                            <img class="w-12 h-10 rounded-full" src="{{ asset('storage/'.$contributor->user->profile->avatar)}}" />
                                                                        @else
                                                                            <img class="w-12" src="{{ asset('images/avatar_64.jpg')}}" />
                                                                        @endif
                                                                                                                                       
                                                                </div>
                                                                <div class="flex flex-col py-2 w-full">
                                                                    <a class="font-bold hover:underline" href="{{ route('staff.profile.email_user_profile', ['email'=>$contributor->user->email]) }}">
                                                                        {{ $contributor->user->staff->surname }}  {{ $contributor->user->staff->firstname }}
                                                                    </a>
                                                                    {{-- <div>{{ $contributor->user->staff->segment->name }}  ({{ $contributor->user->staff->department->department_code }})</div> --}}
                                                                    @if ($contributor->user->profile != null) 
                                                                        <div> {{ $contributor->user->profile->designation }} </div>
                                                                    @endif
                                                                    <div class="w-full">
                                                                        @if (Auth::user()->id != $contributor->user_id)
                                                                            @php
                                                                                $unreadCount = 0;
                                                                            @endphp

                                                                            @foreach($my_private_messages as $private_message)
                                                                                @if ($private_message->sender_id==$contributor->user_id && $private_message->read==false)
                                                                                        @php
                                                                                            $unreadCount++;
                                                                                        @endphp
                                                                                        
                                                                                @endif
                                                                            @endforeach

                                                                            <div class="flex text-end justify-end items-center "> 
                                                                                <span class='px-2 text-xs text-red-500'>
                                                                                        @if ($unreadCount>0)
                                                                                            {{ $unreadCount }} unread messages
                                                                                        @endif
                                                                                </span>
                                                                                <a href="{{ route('staff.workflows.private_message.index', ['document'=>$document->id, 'recipient'=>$contributor->user_id]) }}"  class="flex text-xs border border-1 border-green-500 px-2 py-1 rounded-md 
                                                                                        hover:bg-green-500 hover:text-white">   
                                                                                    Message
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
                                <!-- end of top panel //-->


                                <!-- bottom panel //-->
                                <section>
                                            <div class="w-full">
                                                <div class="py-2 px-4 border-b font-semibold text-gray-700 rounded-md">
                                                    <i class="fa fa-envelope-o" aria-hidden="true"></i> General Messages ({{ $general_messages->count() }})
                                                </div>
                                                
                                                <!-- list of messages //-->
                                                <div class="flex flex-col border-0 border-blue-900 h-50 overflow-y-auto py-2 px-2 mt-2">
                                                        
                                                    @foreach ($general_messages as $message)
                                                        <div class="flex flex-row my-2">
                                                                <div class="px-3 border-0">
                                                                        @if ($message->sender->profile!=null && $message->sender->profile->avatar!="" )
                                                                            
                                                                            <img src="{{ asset('storage/'.$message->sender->profile->avatar)}}" class='w-12 h-10 rounded-full' />
                                                                        
                                                                        @else
                                                                            <img class="w-12" src="{{ asset('images/avatar_64.jpg')}}" />  
                                                                        @endif
                                                                       
                                                                </div>
                                                                <div class="px-3 py-1 rounded-md bg-gray-100 w-full">
                                                                        <a href="{{ route('staff.profile.user_profile', ['fileno'=>$message->sender->staff->fileno]) }}"  class="font-semibold text-sm hover:underline">
                                                                                {{ $message->sender->surname }} {{ $message->sender->firstname }}
                                                                        </a>
                                                                        <div class="text-xs">
                                                                                {{ $message->created_at->format('l jS F, Y @ g:i a') }}
                                                                        </div>
                                                                        <div class="text-sm py-2">
                                                                                {{ $message->message}}
                                                                        </div>

                                                                </div>
                                                        </div>
                                                    @endforeach

                                                </div>
                                                <!-- end of list of messages //-->

                                        </div>
                                        
                                </section>
                                <!-- end of bottom panel //-->



                        </div>
            </section>
            <!--end of right column //-->
             
        </section>


            
    </div>
    

    <script>
        $(document).ready(function(){
            
            $('#contributor').bind('change', function(){
                var value = $("#contributor").val();

                if (value=="")
                {
                    $("#status_comment_panel").slideUp(200);
                }
                else
                {
                    $("#status_comment_panel").slideDown(200);
                }
                
            })
        })
    
    </script>
    </x-staff-layout>
    