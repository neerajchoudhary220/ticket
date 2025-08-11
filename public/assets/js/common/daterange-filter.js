        $(document).ready(function() {
            const dateRangePickerSpan = $('.date-range-picker span');
            let picker;

            $(function() {
                var start = moment();
                var end = moment();

                function cb(start, end, label) {
                    const rangeLabels = ['Today', 'Yesterday', 'Last 7 Days', 'Last 30 Days', 'This Month',
                        'Last Month'
                    ];

                    if (rangeLabels.includes(label)) {
                        dateRangePickerSpan.html(label);
                    } else {
                        dateRangePickerSpan.html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                            'MMMM D, YYYY'));
                    }

                    $('#date-range-picker-form input[name=start_date]').val(start.format('YYYY-MM-DD'));
                    $('#date-range-picker-form input[name=end_date]').val(end.format('YYYY-MM-DD'));
                    $('#date-range-picker-form input[name=day]').val(rangeLabels.includes(label) ? label :
                        '');
                }

                picker = $('.date-range-picker').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment()
                            .subtract(1, 'month').endOf('month')
                        ]
                    }
                }, cb);

                cb(start, end, 'Today');

                // Reset button
                $('.reset-btn').on('click', function() {
                    let today = moment();

                    // Reset picker to Today
                    picker.data('daterangepicker').setStartDate(today);
                    picker.data('daterangepicker').setEndDate(today);
                    cb(today, today, 'Today');

                    // Remove query parameters from URL without reloading
                    const baseUrl = window.location.origin + window.location.pathname;
                    window.history.replaceState({}, '', baseUrl);
                    window.location.reload();
                });
            });
        });