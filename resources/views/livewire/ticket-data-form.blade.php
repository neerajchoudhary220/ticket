       <div>
           <div class="card">

               <div class="card-header bg-warning text-white">
                   <div class="d-flex justify-content-start">
                       <div class="d-flex me-auto">


                           <h5 class="text-left ms-3">
                               @if ($active_draw)
                                   {{ Carbon\Carbon::createFromFormat('H:i', $active_draw->end_time)->format('h:i a') }}
                               @endif
                           </h5>
                           @if ($user_running_ticket)
                               <h5 class="text-left">, Ticket Number: {{ $user_running_ticket->ticket_number }}
                                   {{ $a }}</h5>
                           @endif
                       </div>

                       <div class="d-flex" x-data="{
                           minutes: @entangle('duration'), // duration in minutes
                           timeLeft: 0,
                           startCountdown() {
                               this.timeLeft = this.minutes * 60;
                       
                               let interval = setInterval(() => {
                                   if (this.timeLeft > 0) {
                                       this.timeLeft--;
                                   } else {
                                       clearInterval(interval);
                                       // Optional: location.reload();
                                   }
                               }, 1000);
                           }
                       }" x-init="startCountdown()">
                           <h5
                               x-text="'Time Left: ' + String(Math.floor(timeLeft / 60)).padStart(2, '0') + ':' + String(timeLeft % 60).padStart(2, '0')">
                           </h5>
                       </div>

                   </div>

               </div>
               <ul class="nav nav-tabs" role="tablist">
                   <li class="nav-item">
                       <a class="nav-link {{ $activeTab === 'simple_abc' ? 'active' : '' }}" data-bs-toggle="tab"
                           href="#simple_abc" wire:click.prevent="setTab('simple_abc')">Simple ABC</a>
                   </li>
                   <li class="nav-item">
                       <a class="nav-link {{ $activeTab === 'cross_abc' ? 'active' : '' }}" id="cross_abc_tab"
                           data-bs-toggle="tab" href="#cross_abc" wire:click.prevent="setTab('cross_abc')">Cross ABC</a>
                   </li>
               </ul>

               <!-- Tab panes -->
               <div class="tab-content">
                   <div id="simple_abc"
                       class="container tab-pane {{ $activeTab === 'simple_abc' ? 'active show' : '' }}">
                       <br>
                       @include('livewire.simple-abc')
                   </div>
                   <div id="cross_abc"
                       class="container tab-pane fade {{ $activeTab === 'cross_abc' ? 'active show' : '' }}">
                       <br>
                       @include('livewire.cross-abc')
                   </div>

               </div>


               <div class="card-footer mt-3">
                   <div class="col-12">
                       <div class="text-center mt-3">

                           @error('submit_error')
                               <span class="text-danger"><i class="fa fa-warning"></i> {{ $message }}</span>
                           @enderror
                       </div>
                   </div>
                   <div class="col-12 text-end">
                       <button class="btn btn-sm btn-primary" wire:click='submitTicket'>Submit
                           Ticket</button>
                   </div>

               </div>
           </div>

       </div>
