<div>
    @include('livewire.ticket-data-form')

    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5>Display List</h5>
            </div>
            <div class="card-body">
                {{-- <div class="row mb-3">
                    <div class="col-md-6">
                        <input wire:model.debounce.250="search" type="text" class="form-control" placeholder="Search...">
                    </div>
                    <div class="col-md-4">
                        <select wire:model="filterOption" class="form-control">
                            <option value="">-- Filter by Option --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </div>
                </div> --}}
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
                        <button class="btn btn-sm btn-primary" wire:click='submitTicket'>Submit Ticket</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
