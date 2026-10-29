<x-guest-layout>

    <div class="flex flex-col border-0 border-red-900 w-full">
        <section class="flex flex-row justify-between border-b border-gray-200 py-2 px-8 md:px-10 mt-6">
                <div class="text-2xl font-semibold ">
                    REVIEW OF PROPOSALS
                </div>               
                
        </section>
       
        
       
        @if($already_reviewed == false)
                @php
                    $max_total = $scoring_guides->sum(fn ($guides) => $guides->sum('mark_obtainable'));
                @endphp
                <section class="w-[94%] md:w-[72%] lg:w-[62%] mx-auto py-8">

                    <!-- proposal summary //-->
                    <div class="border border-green-600 rounded-md p-4 mb-5 bg-green-50">
                        <div class="text-xs uppercase tracking-wide text-green-800 font-semibold">Proposal for review</div>
                        <div class="text-lg font-semibold mt-1">{{ $proposal_reviewer->proposal_application->proposal_title }}</div>
                        <div class="mt-3">
                            <a href="{{ route('documents.review', ['review' => $proposal_reviewer->uuid, 'type' => 'proposal']) }}" target="_blank" class="inline-block bg-green-600 text-white text-sm font-semibold py-2 px-4 rounded-md hover:bg-green-700">Open proposal document</a>
                        </div>
                        <p class="text-sm text-gray-700 mt-3">Score each criterion from 0 up to its maximum (decimals are allowed). Your total updates as you go. <strong>You can submit only once</strong>, so please check your scores before submitting.</p>
                    </div>

                    @if(session()->has('error'))
                        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-300 text-red-700">
                            <strong>Error:</strong> {{ session('error') }}
                        </div>
                    @endif

                    @if(session()->has('success'))
                        <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-300 text-green-700">
                            <strong>Success:</strong> {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-300 text-red-700">
                            <strong>Your review was not submitted.</strong> Please correct the highlighted scores below:
                            <ul class="list-disc ml-5 mt-2 text-sm">
                                @foreach ($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="review-form" method="POST"
                          action="{{ route('guests.call_for_proposals.proposal_applications.review.store',['call_for_proposal' => $call_for_proposal, 'proposal_application' => $proposal_application, 'reviewer' => $reviewer, 'review' => $review]) }}">
                        @csrf

                        @foreach ($scoring_guides as $section => $guides)
                            @php $section_index = $loop->index; @endphp
                            <div class="mb-6 border border-green-600 rounded-md overflow-hidden">
                                <div class="bg-green-700 text-white px-4 py-3 flex flex-row justify-between items-center">
                                    <span class="font-semibold text-lg">Section {{ $section }}</span>
                                    <span class="text-sm" id="section-total-{{ $section_index }}" data-max="{{ $guides->sum('mark_obtainable') }}">0 / {{ $guides->sum('mark_obtainable') }}</span>
                                </div>

                                @foreach ($guides as $guide)
                                    <div class="px-4 py-4 border-t border-gray-200 flex flex-col md:flex-row gap-4">
                                        <div class="flex flex-row gap-3 flex-1">
                                            <span class="flex-shrink-0 bg-green-100 text-green-800 font-semibold rounded-full text-sm text-center" style="width:28px;height:28px;line-height:28px;">{{ $loop->iteration }}</span>
                                            <div>{!! nl2br($guide->criterium) !!}</div>
                                        </div>

                                        <div class="md:w-40 flex-shrink-0">
                                            <label for="scoring_guide_{{ $guide->id }}" class="block text-xs text-gray-600 mb-1">Score (max {{ $guide->mark_obtainable }})</label>
                                            <input type="number" inputmode="decimal" step="any" min="0" max="{{ $guide->mark_obtainable }}"
                                                   id="scoring_guide_{{ $guide->id }}"
                                                   name="scoring_guide_{{ $guide->id }}"
                                                   value="{{ old('scoring_guide_'.$guide->id) }}"
                                                   data-score data-section="{{ $section_index }}"
                                                   required
                                                   class="w-full border border-1 {{ $errors->has('scoring_guide_'.$guide->id) ? 'border-red-500' : 'border-gray-400' }} bg-gray-50 p-3 rounded-md text-center focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-100"
                                                   style="font-size:16px;font-weight:500;">
                                            @error('scoring_guide_'.$guide->id)
                                                <span class="text-red-700 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <!-- comment //-->
                        <div class="mb-6">
                            <label for="comment" class="block font-semibold mb-2">Comments <span class="text-gray-500 font-normal text-sm">(optional)</span></label>
                            <textarea id="comment" name="comment" rows="5"
                                      class="border border-1 border-gray-400 bg-gray-50 w-full p-4 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-100"
                                      placeholder="Any general comments on the proposal"
                                      style="font-size:16px;font-weight:500;">{{ old('comment') }}</textarea>
                            @error('comment')
                                <span class="text-red-700 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- running total + submit (stays in view while scrolling) //-->
                        <div class="flex flex-row justify-between items-center gap-4 bg-white border border-green-600 rounded-md px-4 py-3 shadow-md" style="position: sticky; bottom: 12px;">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500">Your total</div>
                                <div class="text-xl font-semibold text-green-800"><span id="total-display">0</span> / {{ $max_total }}</div>
                                <div class="text-xs text-gray-500"><span id="filled-display">0</span> of <span id="count-display">0</span> criteria scored</div>
                            </div>
                            <button type="submit" class="bg-green-600 text-white font-semibold py-3 px-8 rounded-md hover:bg-green-700">Submit review</button>
                        </div>
                    </form>

                    <script>
                        (function () {
                            var inputs = document.querySelectorAll('[data-score]');
                            var totalEl = document.getElementById('total-display');
                            var filledEl = document.getElementById('filled-display');
                            document.getElementById('count-display').textContent = inputs.length;

                            function round(n) { return Math.round(n * 100) / 100; }

                            function update() {
                                var total = 0, filled = 0, sections = {};

                                inputs.forEach(function (input) {
                                    var value = parseFloat(input.value);
                                    var max = parseFloat(input.max);

                                    if (!isNaN(value)) {
                                        filled++;
                                        total += value;
                                        sections[input.dataset.section] = (sections[input.dataset.section] || 0) + value;
                                    }

                                    var outOfRange = !isNaN(value) && (value < 0 || value > max);
                                    input.style.borderColor = outOfRange ? '#dc2626' : '';
                                });

                                totalEl.textContent = round(total);
                                filledEl.textContent = filled;

                                document.querySelectorAll('[id^="section-total-"]').forEach(function (el) {
                                    var index = el.id.replace('section-total-', '');
                                    el.textContent = round(sections[index] || 0) + ' / ' + el.dataset.max;
                                });
                            }

                            inputs.forEach(function (input) { input.addEventListener('input', update); });
                            update();

                            // submissions cannot be changed or repeated, so confirm first
                            document.getElementById('review-form').addEventListener('submit', function (event) {
                                var message = 'Submit your review with a total of ' + totalEl.textContent + ' / {{ $max_total }}? You will not be able to change it afterwards.';
                                if (!confirm(message)) {
                                    event.preventDefault();
                                }
                            });
                        })();
                    </script>
                </section>
        @else
                  <section class="py-8 mt-2  w-[90%] md:w-[60%] mx-auto"">
                        <div>
                            <div class="flex flex-col mx-auto w-full md:w-[95%] items-center justify-center">
                                
            
                                
            
                                <div class="flex flex-col w-[90%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                                <h2 class="font-semibold text-xl py-1 text-center" >PROPOSAL HAS BEEN REVIEWED</h2>
                                <div class='text-center'>Thank you for taking the time and effort to review the proposal.</div>
                                    
                                </div>
                            </div>
                        </div>
                  </section>
            
        @endif



    </div>

</x-guest-layout>