                   <div class="card">
                       <div class="card-header bg-primary text-white">
                           <h5>Display List</h5>
                       </div>
                       <div class="card-body">
                           <div x-data="{
                               page: 1,
                               loading: false,
                               observer: null,
                               init() {
                                   this.setupObserver();
                                   Livewire.hook('commit', () => {
                                       this.$nextTick(() => {
                                           this.setupObserver();
                                       });
                                   });
                               },
                               setupObserver() {
                                   if (this.observer) this.observer.disconnect();
                                   this.observer = new IntersectionObserver((entries) => {
                                       entries.forEach(entry => {
                                           if (entry.isIntersecting && !this.loading) {
                                               this.page++;
                                               this.loading = true;
                                               $wire.set('option_page', this.page).then(() => {
                                                   this.loading = false;
                                               });
                                           }
                                       });
                                   }, { threshold: 1.0 });
                                   if (this.$refs.loader) this.observer.observe(this.$refs.loader);
                               }
                           }" x-init="init" class="table-responsive"
                               style="max-height: 250px; overflow-y: auto;">
                               <table class="table table-bordered table-striped table-hover">
                                   <thead class="table-light position-sticky top-0" style="z-index: 1;">
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
                                       @forelse ($option_list as $option)
                                           <tr>
                                               <td>{{ $option->id }}</td>
                                               <td>{{ $option->option }}</td>
                                               <td>{{ $option->number }}</td>
                                               <td>{{ $option->qty }}</td>
                                               <td>{{ $option->total }}</td>
                                               <td>
                                                   <button class="btn btn-sm btn-danger"
                                                       wire:click="deleteOption({{ $option->id }},{{ $loop->index }})">Delete</button>
                                               </td>
                                           </tr>
                                       @empty
                                           <tr>
                                               <td colspan="6" class="text-center">No records found.</td>
                                           </tr>
                                       @endforelse
                                   </tbody>
                               </table>
                               <div x-ref="loader" class="text-center mt-3">
                                   <div x-show="loading" x-cloak></div>
                               </div>


                           </div>
                           <div class="row mt-3">
                               <div class="col-12 text-end">
                                   <button class="btn btn-sm btn-primary" wire:click='submitTicket'>Submit
                                       Ticket</button>
                               </div>
                           </div>
                       </div>
                   </div>
