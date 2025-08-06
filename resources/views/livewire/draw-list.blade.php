<div class="card">
    <div class="card-header bg-success text-white">
        <h5>Draw List</h5>
    </div>
    <div class="card-body">
        <div x-data="{
            page: 1,
            loading: false,
            init() {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.loading) {
                            this.page++;
                            this.loading = true;
                            $wire.set('draw_page', this.page).then(() => {
                                this.loading = false;
                                $wire.call('loadDraws');
                            });
                        }
                    });
                }, {
                    threshold: 0
                });
        
                this.$nextTick(() => {
                    if (this.$refs.loader) {
                        observer.observe(this.$refs.loader);
                    }
                });
            }
        }" x-init="init" class="ticket-list-box">
            @foreach ($draw_list as $draw)
                <div class="form-check" wire:key="draw-{{ $draw->id }}">
                    <input class="form-check-input draw_checkbox" type="checkbox" id="draw_{{ $draw->id }}"
                        value="{{ $draw->id }}" @checked($draw->id == $selected_draw_id)>
                    <label class="form-check-label" for="draw_{{ $draw->id }}">
                        {{ $draw->draw_number }}
                        (TC:{{ $draw->totalCollection($draw->id) }},
                        TD:{{ $draw->totalDistributions($draw->id) }})
                    </label>
                </div>
            @endforeach

            <div x-ref="loader" class="text-center mt-3">
                <div x-show="loading" x-cloak></div>
            </div>
        </div>


    </div>
    {{-- <div class="card-footer">
        <div>
            Selected Draw IDs: @json($selected_draw)
        </div>
    </div> --}}
</div>
