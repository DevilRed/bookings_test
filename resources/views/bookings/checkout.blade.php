<x-app-layout>
    <div
        x-data="{
            form: {
                date: null
            }
        }"
        class="space-y-12"
    >
        <div>
            <h2 class="text-xl font-medium mt-3">Here's what you're booking</h2>
            <div class="flex mt-6 space-x-3 bg-slate-100 rounded-lg p-4">
                <img src="{{ $employee->profile_photo_url }}" class="rounded-lg size-14 bg-slate-100">
                <div class="w-full">
                    <div class="flex justify-between">
                        <div class="font-semibold">
                            {{ $service->title }} ({{ $service->duration }} minutes)
                        </div>
                        <div class="text-sm">
                            {{ $service->price }}
                        </div>
                    </div>
                    <div class="text-sm">
                        {{ $employee->name }}
                    </div>
                </div>
            </div>
        </div>
    <div>
        <h2 class="text-xl font-medium mt-3">1. When for?</h2>
        <div
            x-data="{
                picker: null,
                availableDates: {{ json_encode($availableDates) }}
            }"
            x-init="
                this.picker = new easepick.create({
                    element: $refs.date,
                    readonly: true,
                    zIndex: 50,
                    date: '{{ $firstAvailableDate }}',
                    css: [
                        'https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.1/dist/index.css',
                        '/vendor/easepick/easepick.css'
                    ],
                    plugins: [
                        'LockPlugin'
                    ],
                    LockPlugin: {
                        minDate: new Date(),
                        filter(date, picked) {
                            return !Object.keys(availableDates).includes(date.format('YYYY-MM-DD'))
                        }
                    },
                    setup (picker) {
                        picker.on('view', (e) => {
                            const { view, date, target } = e.detail;
                            const dateString = date ? date.format('YYYY-MM-DD'): null;

                            if(view === 'CalendarDay' && dateString in availableDates) {
                                // create span only if doesn't exist
                                const span = target.querySelector('.day-slots') || document.createElement('span');
                                span.className = 'day-slots';
                                // show slots from response, as it already includes slots number
                                span.innerHTML = pluralize('slot', availableDates[dateString], true);

                                target.append(span);
                            }
                        })
                    }
                })
                // trigger event handler
                this.picker.on('select', (e) => {
                    // save e.detail.date from calendar into form variable from a parent element
                    form.date = new easepick.DateTime(e.detail.date).format('YYYY-MM-DD');

                    // dispatch event bound to fetch slots
                    $dispatch('slots-requested');
                });

                // on page load, select a date
                $nextTick(() => {
                    this.picker.trigger('select', { date: '{{ $firstAvailableDate }}' });
                });
            "
        >
            <input x-ref="date" type="text" class="mt-6 bg-slate-100 text-sm border-0 rounded-lg px-6 py-4 w-full" placeholder="choose a date" />
        </div>
        <!-- event handler to request slots: x-on:slots-request.window  -->
        <div
            x-data="{
                slots: [],
                fetchSlots(event) {
                    axios.get(`{{ route('slots', [$employee, $service]) }}?date=${form.date}`)
                        .then((response) => {
                            //console.log(response);
                            this.slots = response.data.times;
                        })
                    ;
                }
            }"

            x-on:slots-requested.window="fetchSlots(event)"
        >
            <h2 class="text-xl font-medium mt-3">2. Choose a time slot</h2>
            <div class="mt-6" x-show="slots.length">
                <div class="grid grid-cols-3 md:grid cols-5 gap-8 mt-6">
                    <template x-for="slot in slots">
                        <div x-text="slot" class="py-3  px-4 text-sm border border-slate-200 rounded-lg text-center hover:bg-gray-50/75 cursor-pointer"></div>
                    </template>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
