<x-admin-layout>
    <div class="container mx-auto mb-4">
        <!-- page header //-->
        <section class="flex flex-col w-[90%] md:w-[95%] py-8 px-4 border-red-900 mx-auto">
            
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div >
                        <h1 class="text-2xl font-semibold font-serif text-gray-800">Edit Office</h1>
                    </div>   
                    <div>
                            <a href="{{ route('admin.offices.index') }}" class="border border-green-600 text-green-800 py-2 px-4 
                                            rounded-lg text-sm hover:bg-green-500 hover:text-white">Offices</a>
                    </div>             
            </div>
            
        </section>
        <!-- end of page header //-->



        <!-- new Office form //-->
        <section>
                <div>
                    <form  action="{{ route('admin.offices.update', ['office' => $office->id])}} " method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center">
                        @csrf

                        

                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >Edit Office</h2>
                            Provide Parent Office and Office name
                        </div>


                        @include('partials._session_response')


                        <!-- Parent Office //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <select name="parent" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Directorate code"
                                                                    
                                                                    
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    
                                                                    >
                                                                        <option value=''>-- Select Parent Office --</option>
                                                                        @foreach($offices as $list_office)
                                                                            <option value="{{ $list_office->id }}"  @if($list_office->id == $office->parent_id) selected @endif>{{ $list_office->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                                                                                        

                                                                    @error('parent')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Parent Office //-->

                        
                        

                        <!-- Office name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="name" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Directorate name"
                                                                    
                                                                    value="{{ $office->name }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('name')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of office name //-->

                        
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Update Office</button>
                        </div>
                        
                    </form><!-- end of Office form //-->
                <div>
        </section>
        <!-- end of new directorate form //-->


    </div><!-- end of container //-->
</x-admin-layout>