                   <div class="card">
                       <div class="card-header bg-primary text-white">
                           <h5>Display List</h5>
                       </div>
                       <div class="card-body">
                           <table class="table table-bordered table-striped table-hover">
                               <thead class="table">
                                   <tr>
                                       <th>#ID</th>
                                       <th>Option</th>
                                       <th>Number</th>

                                       <th>Qty</th>
                                       <th>Total</th>
                                       <th>Action</th>
                                   </tr>
                               </thead>
                               <tbody>
                                   @forelse ($options as $option)
                                       <tr>
                                           <td>{{ $option->id }}</td>
                                           <td>{{ $option->option }}</td>
                                           <td>{{ $option->number }}</td>
                                           <td>{{ $option->qty }}</td>
                                           <td>{{ $option->total }}</td>
                                           <td>
                                               <button class="btn btn-sm btn-danger"
                                                   wire:click="deleteOption({{ $option->id }})">Delete</button>
                                           </td>
                                       </tr>
                                   @empty
                                       <tr>
                                           <td colspan="6" class="text-center">No records found.</td>
                                       </tr>
                                   @endforelse
                               </tbody>
                           </table>

                           <div>
                               {{ $options->links() }}
                           </div>
                           <div class="row mt-3">
                               <div class="col-12 text-end">
                                   <button class="btn btn-sm btn-primary" wire:click='submitTicket'>Submit
                                       Ticket</button>
                               </div>
                           </div>
                       </div>
                   </div>
