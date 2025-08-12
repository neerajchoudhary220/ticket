<div>
    <div class="modal fade" id="claimModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="claimModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="claimModalLabel">Enter Claim Number For
                        <strong>{{ $end_time }}</strong>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="otpForm" wire:submit.prevent="save">
                    <div class="modal-body text-center">

                        <div class="d-flex justify-content-center gap-3">
                            <div>
                                <label class="form-label mb-1">A</label>
                                <input type="text" wire:model='claim_a' maxlength="1"
                                    class="form-control text-center otp-input" style="width:60px; font-size:24px;">
                            </div>
                            <div>
                                <label class="form-label mb-1">B</label>
                                <input type="text" maxlength="1" class="form-control text-center otp-input"
                                    style="width:60px; font-size:24px;" wire:model='claim_b'>
                            </div>
                            <div>
                                <label class="form-label mb-1">C</label>
                                <input type="text" maxlength="1" class="form-control text-center otp-input"
                                    style="width:60px; font-size:24px;" wire:model='claim_c'>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="row">
                                <div class="col-12 text-center">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li class="text-danger d-block">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $(document).ready(function() {
            //show modal listener
            $wire.on('show-claim-modal', (event) => {
                $("#claimModal").modal('show');
            })


        })
    </script>
@endscript
