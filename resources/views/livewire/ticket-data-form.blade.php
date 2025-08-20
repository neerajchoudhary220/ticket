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

               @include('livewire.number-display-list')
               <div class="row">
                   <div class="col-12" style="border-bottom: 3px solid">
                       @include('livewire.simple-abc')
                   </div>
                   <div class="col-qw">
                       @include('livewire.cross-abc')

                   </div>
               </div>





               <!-- Tab panes -->




           </div>

       </div>
