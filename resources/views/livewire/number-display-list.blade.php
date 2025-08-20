                   <div class="card mb-3">
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
                           <div class="mb-3" style="border-bottom: 2px solid ; ">
                               <div class="table-responsive option-list" style="max-height: 250px; overflow-y: auto; ">
                                   <h5>Simple ABC</h5>
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
                                       <tfoot>
                                           <div class="col-12 d-flex justify-content-start">
                                               <label class=" me-auto">TQ:{{ collect($stored_options)->sum('total') }},
                                                   Final
                                                   TQ:{{ $final_total_qty }}</label>


                                           </div>
                                       </tfoot>
                                   </table>
                               </div>

                           </div>

                           <div class="table-responsive cross-data-list" style="max-height: 250px; overflow-y: auto; ">
                               <h5>Cross ABC</h5>
                               <table class="table table-bordered table-striped table-hover">
                                   <thead class="table-light position-sticky top-0" style="z-index: 1;">
                                       <tr>
                                           <th>#</th>
                                           <th>Option</th>
                                           <th>Number</th>
                                           <th>Combination</th>
                                           <th>Amt</th>
                                           <th>Total</th>
                                           <th>Action</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                       @foreach ($stored_cross_abc_data as $d)
                                           <tr>
                                               <td>
                                                   {{ $loop->index + 1 }}
                                               </td>
                                               <td>
                                                   {{ $d['option'] }}
                                               </td>
                                               <td>
                                                   {{ $d['number'] }}
                                               </td>
                                               <td>
                                                   {{ $d['combination'] }}
                                               </td>
                                               <td>
                                                   {{ $d['amt'] }}
                                               </td>
                                               <td>
                                                   {{ $d['combination'] * $d['amt'] }}
                                               </td>
                                               <td>
                                                   <button class="btn btn-sm btn-danger"
                                                       wire:click="deleteCrossAbc({{ $loop->index }})">Delete</button>
                                               </td>
                                           </tr>
                                       @endforeach

                                   </tbody>
                                   <tfoot>
                                       <div class="col-12 d-flex justify-content-start">
                                           <label class=" me-auto">TQ:{{ collect($stored_options)->sum('total') }},
                                               Final
                                               TQ:{{ $final_total_qty }}</label>


                                       </div>
                                   </tfoot>
                               </table>




                           </div>

                       </div>

                       <div class="row mb-3">
                           <div class="mt-3 px-3">
                               <div class="col-12">
                                   <div class="text-center mt-3">

                                       @error('submit_error')
                                           <span class="text-danger"><i class="fa fa-warning"></i>
                                               {{ $message }}</span>
                                       @enderror
                                   </div>
                               </div>
                               <div class="col-12 text-end">
                                   <button class="btn  btn-primary" wire:click='submitTicket'>Submit
                                       Ticket</button>
                               </div>

                           </div>
                       </div>

                   </div>
