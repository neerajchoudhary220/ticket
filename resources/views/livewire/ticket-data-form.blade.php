<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <div class="d-flex justify-content-start">
                    <div class="d-flex me-auto">
                        <h5 class="text-left ms-3">{{ $this->active_draw_number }}</h5>
                        <h5 class="text-left"> (TN - {{ $user_running_ticket->id }}) {{ $a }}</h5>
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
                                    Livewire.dispatch('timer-finished');
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
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Option</th>
                            <th>#Numbers (0–9)</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody x-data @focus-b.window="document.getElementById('input_b').focus()"
                        @focus-a.window="document.getElementById('input_a').focus()"
                        @focus-c.window="document.getElementById('input_c').focus()"
                        @focus-a_qty.window="document.getElementById('input_a_qty').focus()"
                        @focus-b_qty.window="document.getElementById('input_b_qty').focus()"
                        @focus-c_qty.window="document.getElementById('input_c_qty').focus()">
                        <!-- Example row -->
                        <tr>
                            <td>A</td>
                            <td>
                                <input type="text" id="input_a" wire:model.debounce.250='a'
                                    wire:keydown.down="move('focus-b','a')" wire:keydown.right="move('focus-a_qty','a')"
                                    wire:keydown.tab="keyTab('a')" class="form-control w-25 zeroToNineNumber">
                            </td>
                            <td>
                                <input type="text" class="form-control w-25 number_qty" id="input_a_qty"
                                    wire:model="a_qty" wire:keydown.left="move('focus-a','a')"
                                    wire:keydown.down="move('focus-b_qty','a')" wire:keydown.tab="keyTab('a')"
                                    wire:keydown.enter="keyEnter('a','focus-a')">{{-- Qty of A --}}
                            </td>
                            <td>11</td>
                            <td>{{ $total_a }}</td>
                        </tr>
                        <tr>
                            <td>B</td>
                            <td><input type="text" wire:model.debounce.250ms='b' id="input_b"
                                    wire:keydown.up = "move('focus-a','b')" wire:keydown.down="move('focus-c','b')"
                                    wire:keydown.right="move('focus-b_qty','b')" wire:keydown.tab="keyTab('b')"
                                    class="form-control w-25 zeroToNineNumber"></td>

                            <td>
                                <input type="text" class="form-control w-25 number_qty" id="input_b_qty"
                                    wire:model="b_qty" wire:keydown.left="move('focus-b','b')"
                                    wire:keydown.down="move('focus-c_qty','b')" wire:keydown.tab="keyTab('b')"
                                    wire:keydown.up="move('focus-a_qty','b')"
                                    wire:keydown.enter="keyEnter('b','focus-b')">
                            </td>
                            <td>11</td>
                            <td>{{ $total_b }}</td>
                        </tr>
                        <tr>
                            <td>C</td>
                            <td><input type="text" wire:model.debounce.250ms='c' id="input_c"
                                    wire:keydown.up = "move('focus-b','c')" wire:keydown.right="move('focus-c_qty','c')"
                                    wire:keydown.tab="keyTab('c')" class="form-control w-25 zeroToNineNumber"></td>
                            <td>
                                <input type="text" class="form-control w-25 number_qty" id="input_c_qty"
                                    wire:model="c_qty" wire:keydown.left="move('focus-c','c')"
                                    wire:keydown.up="move('focus-b_qty','c')" wire:keydown.tab="keyTab('c')"
                                    wire:keydown.enter="keyEnter('c','focus-c')">
                            </td>
                            <td>11</td>
                            <td>{{ $total_c }}</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
