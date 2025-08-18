 <div class="card-body bg-white">
     <div class="row mb-3" x-data @focus-cross-abc.window="document.getElementById('corss_abc').focus()"
         @focus-cross-abc-combination.window="document.getElementById('cross_combination').focus()"
         @focus-cross-abc-qty.window="document.getElementById('cross_qty').focus()">
         <div class="col-3">
             <label for="corss_abc">ABC </label>
             <input type="text" id="corss_abc"
                 wire:keydown.enter="enterKeyPressOnCrossAbc('focus-cross-abc-qty','cross_abc_input')"
                 wire:model="cross_abc_input" class="form-control cross_number" placeholder="Enter ABC">
             @error('cross_abc_input')
                 <span class="text-danger">{{ $message }}</span>
             @enderror
         </div>
         <div class="col-3">
             <label for="cross_qty">Amt</label>
             <input type="text" id="cross_qty"
                 wire:keydown.enter="enterKeyPressOnCrossAbc('focus-cross-abc-combination','cross_abc_amt')"
                 wire:model="cross_abc_amt" class="form-control" id="cross_qty" placeholder="Enter Qty">
             @error('cross_abc_amt')
                 <span class="text-danger">{{ $message }}</span>
             @enderror
         </div>
         <div class="col-3">
             <div>
                 <label for="cross_combination">Combination</label>
                 <input type="text" class="form-control" id="cross_combination" wire:model="cross_combination"
                     wire:keydown.enter="enterKeyPressOnCrossAbc('focus-cross-abc','cross_combination')"
                     placeholder="Enter Combination">
                 @error('cross_combination')
                     <span class="text-danger">{{ $message }}</span>
                 @enderror
             </div>
         </div>

     </div>
     {{-- AB --}}
     <div class="row mb-3" x-data @focus-cross-ab.window="document.getElementById('cross_ab').focus()"
         @focus-cross-ab-amt.window="document.getElementById('cross_ab_amt').focus()">
         <div class="col-3">
             <label for="cross_ab">AB</label>
             <input type="text" wire:keydown.enter="enterKeyPressOnCrossAb('focus-cross-ab-amt','cross_ab')"
                 id="cross_ab" placeholder="Enter AB" class="form-control" wire:model="cross_ab">
             @error('cross_ab')
                 <span class="text-danger"> {{ $message }}</span>
             @enderror
         </div>
         <div class="col-3">
             <label for="cross_ab_amt">Amt</label>
             <input type="text" id="cross_ab_amt" placeholder="Enter Amt" class="form-control"
                 wire:keydown.enter="enterKeyPressOnCrossAb('focus-cross-ab','cross_ab_amt')" wire:model="cross_ab_amt">
             @error('cross_ab_amt')
                 <span class="text-danger"> {{ $message }}</span>
             @enderror
         </div>
     </div>
     {{-- AC --}}
     <div class="row mb-3" x-data @focus-cross_ac.window="document.getElementById('cross_ac').focus()"
         @focus-cross-ac-amt.window="document.getElementById('cross_ac_amt').focus()">
         <div class="col-3">
             <label for="cross_ac">AC</label>
             <input type="text" id="cross_ac" placeholder="Enter AC" class="form-control" wire:model="cross_ac"
                 wire:keydown.enter="enterKeyPressOnCrossAc('focus-cross-ac-amt','cross_ac')">
             @error('cross_ac')
                 <span class="text-danger"> {{ $message }}</span>
             @enderror
         </div>
         <div class="col-3">
             <label for="cross_ac_amt">Amt</label>
             <input type="text" id="cross_ac_amt" placeholder="Enter AC" class="form-control"
                 wire:model="cross_ac_amt" wire:keydown.enter="enterKeyPressOnCrossAc('focus-cross-ac','cross_ac_amt')">
             @error('cross_ac_amt')
                 <span class="text-danger"> {{ $message }}</span>
             @enderror
         </div>
     </div>
     {{-- BC --}}
     <div class="row mb-3" x-data @focus-cross-bc.window="document.getElementById('cross_bc').focus()"
         @focus-cross-bc-amt.window="document.getElementById('cross_bc_amt').focus()">
         <div class="col-3">
             <label for="cross_bc">BC</label>
             <input type="text" id="cross_bc" placeholder="Enter BC" class="form-control" wire:model="cross_bc"
                 wire:keydown.enter="enterKeyPressOnCrossBc('focus-cross-bc-amt','cross_bc')">
             @error('cross_bc')
                 <span class="text-danger"> {{ $message }}</span>
             @enderror
         </div>
         <div class="col-3">
             <label for="cross_bc_amt">Amt</label>
             <input type="text" class="form-control" placeholder="Enter Amount" id="cross_bc_amt"
                 wire:model="cross_bc_amt" wire:keydown.enter="enterKeyPressOnCrossBc('focus-cross-bc','cross_bc_amt')">
             @error('cross_bc_amt')
                 <span class="text-danger"> {{ $message }}</span>
             @enderror
         </div>
     </div>

     {{-- ABC --}}
     <div class="row mb-3">
         <div class="col-12" x-data @focus-cross-a.window="document.getElementById('cross_a').focus()"
             @focus-cross-b.window="document.getElementById('cross_b').focus()"
             @focus-cross-c.window="document.getElementById('cross_c').focus()"
             @focus-cross-single-amt.window="document.getElementById('cross_single_amount').focus()">
             <div class="d-flex justify-content-start gap-3">
                 <div>
                     <label class="form-label mb-1" for="cross_a">A</label>
                     <input type="text" wire:model='cross_a' id="cross_a"
                         class="mynumber form-control text-center" style="width:60px; font-size:24px;"
                         wire:keydown.enter="enterKeyPressOnCrossA('focus-cross-b','cross_a')">
                     @error('cross_a')
                         <span class="text-danger"> {{ $message }}</span>
                     @enderror
                 </div>
                 <div>
                     <label class="form-label mb-1" for="cross_b">B</label>
                     <input type="text" id="cross_b" class="mynumber form-control text-center"
                         style="width:60px; font-size:24px;" wire:model='cross_b'
                         wire:keydown.enter="enterKeyPressOnCrossA('focus-cross-c','cross_b')">
                     @error('cross_b')
                         <span class="text-danger"> {{ $message }}</span>
                     @enderror
                 </div>
                 <div>
                     <label class="form-label mb-1" for="cross_c">C</label>
                     <input type="text" id="cross_c" class="mynumber form-control text-center"
                         style="width:60px; font-size:24px;" wire:model='cross_c'
                         wire:keydown.enter="enterKeyPressOnCrossA('focus-cross-single-amt','cross_c')">
                     @error('cross_c')
                         <span class="text-danger"> {{ $message }}</span>
                     @enderror
                 </div>

                 <div>
                     <label class="form-label mb-1" for="cross_single_amount">Amt</label>
                     <input type="text" id="cross_single_amount" class="mynumber form-control text-center"
                         style="width:100px; font-size:24px;" wire:model='cross_single_amount'
                         wire:keydown.enter="enterKeyPressOnCrossA('focus-cross-a','cross_single_amount')">
                     @error('cross_single_amount')
                         <span class="text-danger"> {{ $message }}</span>
                     @enderror
                 </div>
             </div>
         </div>
     </div>

 </div>
 @include('livewire.cross-data-display')

 @script
     <script>
         $(document).on("input", ".cross_number", function() {
             let val = $(this).val();

             // Remove non-digits
             val = val.replace(/[^0-9]/g, '');

             // Remove repeated digits (keep first occurrence)
             val = val.split('').filter((digit, index, self) => self.indexOf(digit) === index).join('');

             // Limit to 3 digits
             if (val.length > 3) {
                 val = val.substring(0, 3);
             }

             $(this).val(val);
         });
         $(document).on("input", ".mynumber", function() {
             // Replace any non-digit character
             this.value = this.value.replace(/[^0-9]/g, '');
         });
     </script>
 @endscript
