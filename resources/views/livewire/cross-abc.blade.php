 <div class="card-body bg-white">
     <div class="row">
         <div class="col-3">
             <label for="corss_abc">ABC:</label>
             <input type="text" id="corss_abc" wire:model="cross_abc_input" class="form-control  cross_number"
                 placeholder="Enter ABC">
             @error('cross_abc_qty')
                 <span class="text-danger">{{ $message }}</span>
             @enderror
         </div>
         <div class="col-3">
             <label for="cross_qty">Qty</label>
             <input type="text" id="cross_qty" wire:model="cross_abc_qty" class="form-control" id="cross_qty"
                 placeholder="Enter Qty">
             @error('cross_abc_qty')
                 <span class="text-danger">{{ $message }}</span>
             @enderror
         </div>
         <div class="col-3">
             <div class="mt-3 pt-3">
                 <label for="cross_combination">Combination:</label>
                 <select id="cross_combination" wire:model="cross_combination">
                     <option value="3">3</option>
                     <option value="27">27</option>
                 </select>
                 @error('cross_combination')
                     <span class="text-danger">{{ $message }}</span>
                 @enderror
             </div>
         </div>
         <div class="col-3 text-end">
             <div class="mt-4 pt-2">
                 <button class="btn btn-primary btn-sm" wire:click.prevent='crossSubmit'>Submit</button>
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
     </script>
 @endscript
