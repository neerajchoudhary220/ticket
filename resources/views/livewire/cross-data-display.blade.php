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
                           <div class="table-responsive cross-data-list" style="max-height: 250px; overflow-y: auto; ">
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
                               </table>




                           </div>
                           <div class="row mt-3">
                               <div class="col-12 d-flex justify-content-start">
                                   {{-- <label class=" me-auto">TQ:{{ collect($stored_options)->sum('total') }}, Final
                                       TQ:{{ $final_total_qty }}</label> --}}


                               </div>

                           </div>
                       </div>

                   </div>
