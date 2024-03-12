<script setup lang="ts">
import { router, useForm, usePage } from "@inertiajs/vue3";
import moment from "moment";
import { computed, watch } from "vue";

defineProps<{
    upcomingBookings: any[];
    pendingBookings: any[];
    admin?: any;
}>();

const adminForm = useForm({
    emailAddress: "",
    phoneNumber: "",
});

const page = usePage();
const message = computed(() => page.props.flash.message);
const dateForm = useForm({
    date: "",
});

watch(message, (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
        alert(newValue);
    }
});
</script>

<template>
    <main class="relative flex">
        <div
            v-if="!admin"
            class="flex justify-center items-center w-full h-full fixed top-1/2 left-1/2 translate-x-[-50%] translate-y-[-50%] z-10 bg-[rgba(0,0,0,.2)]"
        >
            <div class="border p-4 bg-white">
                <h1 class="text-center">Register email</h1>
                <form
                    method="post"
                    @submit.prevent="
                        router.post('/admin/register', adminForm, {
                            preserveState: true,
                        })
                    "
                >
                    <div>
                        <label for="admin-email">Email</label>
                        <input
                            type="email"
                            id="admin-email"
                            v-model="adminForm.emailAddress"
                        />
                    </div>
                    <div>
                        <label for="admin-number">Phone Number</label>
                        <input
                            type="text"
                            id="admin-number"
                            v-model="adminForm.phoneNumber"
                        />
                    </div>
                    <div>
                        <button
                            class="w-full py-1 px-4 border"
                            type="submit"
                            :disabled="adminForm.processing"
                        >
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{ $page.props.success }}
        <section class="relative w-1/2">
            <div class="bg-white sticky top-0 left-0">
                <div class="sticky top-0 left-0 bg-white flex justify-between">
                    <h1 class="text-lg">Upcoming Bookings</h1>
                    <button
                        class="py-1 px-4 border"
                        @click="
                            router.get(
                                '/admin/upcoming',
                                { date: null },
                                {
                                    onBefore: () => dateForm.reset(),
                                    preserveState: true,
                                }
                            )
                        "
                    >
                        refresh
                    </button>
                </div>
                <form
                    method="post"
                    @submit.prevent="
                        router.post('/admin/disable', dateForm, {
                            preserveState: true,
                        })
                    "
                >
                    <input
                        type="date"
                        id="upcoming-bookings"
                        @change="
                            router.get('/admin/upcoming', dateForm, {
                                preserveState: true,
                            })
                        "
                        v-model="dateForm.date"
                    />
                    <button
                        class="py-1 px-4 border"
                        type="submit"
                        :disabled="dateForm.processing"
                    >
                        Disable date
                    </button>
                </form>
            </div>
            <div class="flex flex-col flex-nowrap gap-4">
                <div v-for="(booking, index) in upcomingBookings" :key="index">
                    <p><strong>book id:</strong> {{ booking.id }}</p>
                    <p><strong>customer id:</strong> {{ booking.user_id }}</p>
                    <p>
                        <strong>vehicle make:</strong>
                        {{ booking.vehicle_make }}
                    </p>
                    <p>
                        <strong>vehicle model:</strong>
                        {{ booking.vehicle_model }}
                    </p>
                    <p>
                        <strong>booked on:</strong>
                        {{ moment(booking.booked_on).format("MM-DD-YYYY") }}
                    </p>
                    <p><strong>started at:</strong> {{ booking.started_at }}</p>
                    <p><strong>ended at:</strong> {{ booking.ended_at }}</p>
                    <p>
                        <strong>admin confirmed:</strong>
                        {{ booking.admin_confirmed }}
                    </p>
                    <p>
                        <strong>customer confirmed:</strong>
                        {{ booking.user_confirmed }}
                    </p>
                </div>
            </div>
        </section>
        <section class="relative w-1/2">
            <div class="sticky top-0 left-0 bg-white flex justify-between">
                <h1 class="text-lg">Pending Bookings</h1>
                <button
                    class="py-1 px-4 border"
                    @click="
                        router.get(
                            '/admin/pending',
                            {},
                            { preserveState: true }
                        )
                    "
                >
                    refresh
                </button>
            </div>
            <div class="flex flex-col flex-nowrap gap-4">
                <div v-for="(booking, index) in pendingBookings" :key="index">
                    <p><strong>book id:</strong> {{ booking.id }}</p>
                    <p><strong>customer id:</strong> {{ booking.user_id }}</p>
                    <p>
                        <strong>vehicle make:</strong>
                        {{ booking.vehicle_make }}
                    </p>
                    <p>
                        <strong>vehicle model:</strong>
                        {{ booking.vehicle_model }}
                    </p>
                    <p>
                        <strong>booked on:</strong>
                        {{ moment(booking.booked_on).format("MM-DD-YYYY") }}
                    </p>
                    <p><strong>started at:</strong> {{ booking.started_at }}</p>
                    <p><strong>ended at:</strong> {{ booking.ended_at }}</p>
                    <p>
                        <strong>admin confirmed:</strong>
                        {{ booking.admin_confirmed }}
                    </p>
                    <p>
                        <strong>customer confirmed:</strong>
                        {{ booking.user_confirmed }}
                    </p>
                    <button
                        v-if="!booking.admin_confirmed"
                        class="w-full py-1 px-4 border"
                        @click="
                            router.post('/admin/approve', {
                                bookingId: booking.id,
                            })
                        "
                    >
                        Approve
                    </button>
                </div>
            </div>
        </section>
    </main>
</template>
