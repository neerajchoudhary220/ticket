  <div class="card-body">
      <div class="row mb-3" x-data @focus-qty.window="document.getElementById('qty').focus()"
          @focus-abc.window="document.getElementById('abc').focus()">
          <div class="col-4">
              <div class="d-flex">
                  <label class="mt-2" for="abc">ABC: </label>
                  <input type="text" class="form-control" wire:model="abc" id="abc"
                      wire:keydown.enter="enterKeyPressOnAbc" placeholder="Enter ABC">
              </div>
              @error('abc')
                  <span class="text-danger">{{ $message }}</span>
              @enderror
          </div>
          <div class="col-4">
              <div class="d-flex">
                  <label class="mt-2" for="qty">QTY: </label>
                  <input type="text" class="form-control" wire:model="abc_qty" id="qty"
                      wire:keydown.enter="enterKeyPressOnQty" placeholder="Enter Qty"><br>
              </div>
              @error('abc_qty')
                  <span class="text-danger">{{ $message }}</span>
              @enderror
          </div>
      </div>

      <table class="table table-bordered">
          <thead>
              <tr>
                  <th>Option</th>
                  <th>#Numbers (0–9)</th>
                  <th>Qty</th>

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
                          wire:keydown.tab="keyTab('a')" class="form-control  zeroToNineNumber">
                  </td>
                  <td>
                      <input type="text" class="form-control  number_qty" id="input_a_qty" wire:model="a_qty"
                          wire:keydown.left="move('focus-a','a')" wire:keydown.down="move('focus-b_qty','a')"
                          wire:keydown.tab="keyTab('a')"
                          wire:keydown.enter="keyEnter('a','focus-a')">{{-- Qty of A --}}
                  </td>

              </tr>
              <tr>
                  <td>B</td>
                  <td><input type="text" wire:model.debounce.250ms='b' id="input_b"
                          wire:keydown.up = "move('focus-a','b')" wire:keydown.down="move('focus-c','b')"
                          wire:keydown.right="move('focus-b_qty','b')" wire:keydown.tab="keyTab('b')"
                          class="form-control zeroToNineNumber">
                  </td>

                  <td>
                      <input type="text" class="form-control  number_qty" id="input_b_qty" wire:model="b_qty"
                          wire:keydown.left="move('focus-b','b')" wire:keydown.down="move('focus-c_qty','b')"
                          wire:keydown.tab="keyTab('b')" wire:keydown.up="move('focus-a_qty','b')"
                          wire:keydown.enter="keyEnter('b','focus-b')">
                  </td>

              </tr>
              <tr>
                  <td>C</td>
                  <td><input type="text" wire:model.debounce.250ms='c' id="input_c"
                          wire:keydown.up = "move('focus-b','c')" wire:keydown.right="move('focus-c_qty','c')"
                          wire:keydown.tab="keyTab('c')" class="form-control zeroToNineNumber">
                  </td>
                  <td>
                      <input type="text" class="form-control  number_qty" id="input_c_qty" wire:model="c_qty"
                          wire:keydown.left="move('focus-c','c')" wire:keydown.up="move('focus-b_qty','c')"
                          wire:keydown.tab="keyTab('c')" wire:keydown.enter="keyEnter('c','focus-c')">
                  </td>

              </tr>
          </tbody>
      </table>


  </div>
  @include('livewire.number-display-list')
