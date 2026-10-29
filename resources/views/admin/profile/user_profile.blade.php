<x-admin-layout>
    <div class="flex flex-col container mx-4 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    User Profile              
                </div>                
        </section>

       

                <section class="flex flex-col md:flex-row rounded w-full mt-3 space-x-4">
                    <div class="flex flex-col w-full  md:w-[30%] justify-center items-center 
                                border px-8 py-4 rounded-md">
                            <div class="">
                                @if ($userprofile->user->profile!=null && ($userprofile->user->profile->avatar != "" || $userprofile->user->profile->avatar != null))
                                    <img src="{{ asset('storage/'.$userprofile->user->profile->avatar) }}" class="w-36 h-36 rounded-full" />
                                @else
                                    <img src="{{ asset('images/avatar_150.jpg') }}" />
                                @endif
                            </div>
                            

                    </div>
                    <div class="flex flex-col justify-center md:border rounded-md md:w-[70%] py-4 px-4">
                            
                            <div class="mb-4  mx-[10%] md:mx-0 ">
                                    <div class="text-xl font-semibold">
                                            {{ $userprofile->user->surname }} {{ $userprofile->user->firstname }} {{ $userprofile->user->middlename }}                                
                                    </div>
                                     @if ( $userprofile->user->profile != null)
                                        <div class="text-sm">
                                                {{ $userprofile->user->profile->designation}}, {{ $userprofile->user->staff->fileno}}
                                        </div>   
                                     @else
                                        <div class="text-sm">
                                                {{ $userprofile->user->staff->fileno }}
                                        </div>   
                                     @endif                         
                            </div>

                        
                            <div class="py-4 mx-[10%] md:mx-0">
                                    
                                    <div>
                                                <div class='font-semibold'>Office </div>
                                                <div>
                                                        {{ $userprofile->user->staff->office->name }}
                                                </div>
                                            
                                    </div>                            
                            </div>


                           <div class="py-4 mx-[10%] md:mx-0">
                                    <div class='font-semibold'>Contact </div>
                                    <div>
                                            {{ $userprofile->user->email }}
                                    </div>
                                   @if ( $userprofile->user->profile != null)
                                        <div>
                                                {{ $userprofile->user->profile->phone}}
                                        </div>
                                    @endif

                           </div>
                        
                           

                            
                    </div>
                </section>

    
    


    </div>

</x-admin-layout>