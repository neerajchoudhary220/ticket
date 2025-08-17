                   <div class="card">
                       <div class="card-header bg-primary text-white">
                           @if ($selected_ticket)
                               <h5>Display List of {{ $selected_ticket->ticket_number }}</h5>
                               <hr>
                               <div>
                                   <b>Time:</b> {{ $selected_times }}
                               </div>
                           @endif
                       </div>
                       <div class="card-body">
                           <div class="table-responsive option-list" style="max-height: 250px; overflow-y: auto; ">
                               <table class="table table-bordered table-striped table-hover">
                                   <thead class="table-light position-sticky top-0" style="z-index: 1;">
                                       <tr>
                                           <th>#</th>
                                           <th>Option</th>
                                           <th>Number</th>

                                           <th>Qty</th>
                                           <th>Total</th>
                                           <th>Action</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                       @forelse ($stored_options as $option)
                                           <tr>
                                               <td>{{ $loop->index + 1 }}</td>
                                               {{-- <td>{{ implode(',', $option['draw_ids']) }} --}}
                                               </td>
                                               <td>{{ $option['option'] }}</td>
                                               <td>{{ $option['number'] }}</td>
                                               <td>{{ $option['qty'] }}</td>
                                               <td>{{ $option['total'] }}</td>
                                               <td>
                                                   <button class="btn btn-sm btn-danger"
                                                       wire:click="deleteOption({{ $loop->index }})">Delete</button>
                                               </td>
                                           </tr>
                                       @empty
                                           <tr>
                                               <td colspan="6" class="text-center">No records found.</td>
                                           </tr>
                                       @endforelse
                                   </tbody>
                               </table>
                           </div>
                           <div class="row mt-3">
                               <div class="col-12 d-flex justify-content-start">
                                   <label class=" me-auto">TQ:{{ collect($stored_options)->sum('total') }}, Final
                                       TQ:{{ $final_total_qty }}</label>


                               </div>

                           </div>
                       </div>

                   </div>
