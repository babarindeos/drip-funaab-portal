<x-admin-layout>
    <div class="container border-0 mx-auto">
        <!-- page header //-->
        <section class="flex flex-col w-[95%] md:w-[95%] py-1 mt-6 px-4 border-red-900 mx-auto">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div class="flex flex-row w-1/4">
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Staff</h1>
                    </div>
                    <div class="flex flex-row w-3/4 justify-end space-x-1">

                            
                            <div class="border-0 flex flex-col items-center justify-center">
                                        <a href="{{ route('admin.staff.create') }}" class="bg-white-600 border border-green-600 text-green-800 py-2 px-4 
                                                rounded-lg text-sm hover:bg-green-500 hover:text-white">New Staff</a>
                            </div>
                    </div>
            </div>
        </section>
        <!-- end of page header //-->

        @if ($staffs->count())
               <div class='py-2 w-[90%] md:w-[90%] mx-auto'>{{ $staffs->links() }}</div>
               <section class="flex flex-col py-2 px-2 justify-end w-[93%] mx-auto md:px-1">
                    <div class="flex justify-end border border-0">
                    
                        <input type="text" name="search" class="w-4/5 md:w-2/5 border border-1 border-gray-400 bg-gray-50
                                    p-2 rounded-md 
                                    focus:outline-none
                                    focus:border-blue-500 
                                    focus:ring
                                    focus:ring-blue-100" placeholder="Search"                
                                
                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                  
                        
                        />  
                    </div>
                    
                </section>


                <section class="flex flex-wrap gap-6 mx-auto w-[90%] md:w-[90%] md:flex-row justify-center space-y-2 md:space-y-0  border-0 py-2 mb-16">


                                @foreach ($staffs as $staff)
                                            
                                        <div class='flex flex-col w-full md:flex-row md:w-[27%] border rounded-xl shadow-md px-5 py-6 bg-white'>
                                                <div class='flex flex-col w-full items-center justify-center'>
                                                        <div class='flex justify-center border-0'>
                                                            @if ($staff->profile)
                                                                    
                                                                    <img class='w-36 h-36 rounded-full' src="{{ asset('storage/'.$staff->profile->avatar )}}" alt="User Avatar" />
                                                            @else
                                                                    <img class='w-36 h-36 rounded-full' src="{{ asset('images/avatar_64.jpg') }}" alt="Default Avatar" />
                                                            @endif
                                                        </div>

                                            
                                                    <div class='flex flex-col text-lg font-semibold py-1 justify-center text-center items-center'>
                                                                        <a class='hover:overline underline' href="{{ route('admin.profile.user_profile', ['email'=> $staff->user->email])}}">
                                                                                {{$staff->surname}} {{$staff->firstname}} {{$staff->middlename}}
                                                                        </a>
                                                    </div>
                                                    <div class='flex flex-col justify-center items-center text-center text-sm'>
                                                            {{ $staff->fileno }} 
                                                            @if ($staff->segment != null) ({{ $staff->segment->name }})  @endif 
                                                    </div>

                                                    <div class="text-center py-2">
                                                            <span class="px-1">
                                                                <a class="bg-green-400 hover:bg-green-500 text-white rounded-md px-4 py-1 text-xs"
                                                                    href="{{ route('admin.profile.user_profile', ['email'=> $staff->user->email])}}">
                                                                    View
                                                                </a>
                                                            </span>
                                                            <span class="text-sm">
                                                                <a class="hover:bg-blue-500 bg-blue-400 text-white rounded-md 
                                                                        px-4 py-1 text-xs" href="{{ route('admin.staff.edit', ['staff' => $staff->id]) }}">Edit</a>
                                                            </span>
                                                            <span> 
                                                                <a class="hover:bg-red-500 bg-red-400 text-white rounded-md 
                                                                        px-4 py-1 text-xs" href="#">Delete</a>
                                                            </span>	
                                                    </div>

                                                    

                                                </div>
                                        </div>
                                @endforeach

                                


                </section>
                <div class='py-2 w-[90%] md:w-[90%] mx-auto'>{{ $staffs->links() }}</div>
        @else
                <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-4">
                    <div class="flex flex-row text-2xl font-semibold text-gray-300 justify-center py-8">
                        No Staff record is found
                    </div>
                </section>
        @endif
    </div>
</x-admin-layout>

<script>
    $(document).ready(function(){
        $("input[name='search']").on('keyup', function(){
            var value = $(this).val().toLowerCase();

            $("table tbody tr").filter(function(){
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            })
        });
    });

</script>