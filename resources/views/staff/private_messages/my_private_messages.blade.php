<x-staff-layout>

    <div class="flex flex-col container mx-4 border border-0 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    My Private Messages               
                </div>
                
        </section>

        <section>
            <div class="flex flex-row space-x-4 md:space-x-8">
                    <div class="flex">
                            <a onclick="history.back();" class="border px-4 py-1 mt-1 cursor-pointer 
                                                            hover:font-semibold hover:border-gray-300">    
                                    &laquo; Back
                            </a>
                    </div>
                    <div class="flex md:justify-center items-center">
                            <a href="{{ route('staff.workflows.flow', ['document'=>$document->id]) }}" class="font-semibold hover:underline cursor-pointer">
                            {{ $document->title }}
                            </a>
                    </div>
            </div>
        </section>
        

        <section class="flex flex-row border border-0 py-4 w-full">
             <div class="flex flex-col border border-1 rounded-md py-2 px-2 md:px-4 w-[90%] md:w-1/2 mx-auto">
                    
                    <!-- list of messages //-->
                    <div class="flex flex-col border-0 border-blue-900 h-50 overflow-y-auto py-2 mt-2">
                        
                        @foreach ($private_messages as $pmessage)
                            <div class="flex flex-row my-2">
                                    <div class="px-3 border-0">
                                             
                                            @if ($pmessage->sender->profile!=null && $pmessage->sender->profile->avatar!="" )
                                            
                                                <img src="{{ asset('storage/'.$pmessage->sender->profile->avatar)}}" class='w-12 h-10 rounded-full' />
                                        
                                            @else
                                                <img class="w-12" src="{{ asset('images/avatar_64.jpg')}}" />  
                                            @endif 
                                    </div>
                                    <div class="px-3 py-1 rounded-md bg-gray-100 w-full">
                                            <div class="font-semibold text-sm">
                                                    {{ $pmessage->sender->surname }} {{ $pmessage->sender->firstname }}
                                            </div>
                                            <div class="text-xs">
                                                    {{ $pmessage->created_at->format('l jS F, Y @ g:i a') }}
                                            </div>
                                            <div class="text-sm py-2">
                                                    {{ $pmessage->message}}
                                            </div>
                                            <div class="flex items-center justify-end space-x-4">
                                                        @php
                                                                        // get recipient id i.e the other party as recipient
                                                                        if ($pmessage->recipient_id != Auth::id())
                                                                        {
                                                                                $recipient_id = $pmessage->recipient_id;
                                                                        }
                                                                        else
                                                                        {
                                                                                $recipient_id = $pmessage->sender_id;
                                                                        }

                                                                        // get the number of messages in the chat
                                                        @endphp

                                                        <div class="text-xs text-gray-700">                                                       
                                                               {{ $pmessage->message_count}} messages
                                                        </div>
                                            
                                                        <div>
                                                                <a class="border border-1 rounded-md py-1 px-4 text-xs border-green-600 text-green-600 
                                                                          hover:bg-green-500 hover:text-white hover:border-green-500" href="{{ route('staff.workflows.private_message.index', ['document'=>$document->id, 'recipient'=>$recipient_id]) }}" class="text-xs">
                                                                        Open Chat 
                                                                </a>
                                                        </div>
                                            </div>

                                    </div>
                            </div>
                        @endforeach

                        <div class="py-2">
                            {{ $private_messages->links()}}
                        <div>
             </div>
            
        </section>
        
    </div>
    
    </x-staff-layout>