<div>
    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-12">
                    @include('livewire.ticket-data-form')
                </div>
                <div class="col-12 mt-3">
                    @include('livewire.number-display-list')
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="row">
                <div class="col-12">
                    @include('livewire.ticket-list')
                </div>
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5>Current Status</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Draw Number</th>
                                        <th>Total Collection</th>
                                        <th>Total Distribution</th>
                                    </tr>
                                </thead>
                                <tbody class="ticket-list-box">
                                    <tr>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>

                                    </tr>
                                </tbody>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
