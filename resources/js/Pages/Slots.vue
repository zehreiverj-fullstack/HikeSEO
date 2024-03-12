<script setup lang="ts">
import moment from "moment";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";

defineProps<{
    slots: string[];
}>();

const booking: any = useForm({
    name: "",
    emailAddress: "",
    phoneNumber: "",
    vehicleMake: "",
    vehicleModel: "",
    bookOn: "",
    slot: [],
});

const handleChange = (e: any) => {
    const date = moment(e.target.value);
    if ([0, 6].includes(date.day())) {
        alert("We're closed on the weekends");
        booking.bookOn = "";
        return;
    }
};

const page = usePage();
//@ts-ignore
const message = computed(() => page.props.flash.message);
const isSuccess = computed(() => page.props.flash.isSuccess);

const handleSubmit = (message: string, isSuccess: boolean) => {
    if (isSuccess) booking.reset();
    alert(message);
};
</script>

<template>
    <Head title="Slots" />
    <main class="flex justify-center items-center">
        <section>
            <form
                class="flex flex-col flex-nowrap gap-4"
                method="post"
                @submit.prevent="
                    router.post('book', booking, {
                        onSuccess: () => {
                            handleSubmit(message, isSuccess);
                        },
                    })
                "
            >
                <div>
                    <div>
                        <label for="name">Name</label>
                        <input
                            id="name"
                            type="text"
                            v-model="booking.name"
                            required
                        />
                    </div>
                    <div>
                        <label for="email-address">Email</label>
                        <input
                            id="email-address"
                            type="email"
                            v-model="booking.emailAddress"
                            required
                        />
                    </div>
                    <div>
                        <label for="phone-number">Phone Number</label>
                        <input
                            id="phone-number"
                            type="text"
                            v-model="booking.phoneNumber"
                            required
                        />
                    </div>
                    <div>
                        <label for="vehicle-make">Vehicle Make</label>
                        <input
                            id="vehicle-make"
                            type="text"
                            v-model="booking.vehicleMake"
                            required
                        />
                    </div>
                    <div>
                        <label for="vehicle-model">Vehicle Model</label>
                        <input
                            id="vehicle-model"
                            type="text"
                            v-model="booking.vehicleModel"
                            required
                        />
                    </div>
                    <div>
                        <label for="book-on">Book On</label>
                        <input
                            id="book-on"
                            type="date"
                            v-model="booking.bookOn"
                            @change="handleChange"
                            required
                        />
                    </div>
                    <div>
                        <label for="slots">Slots</label>
                        <select id="slots" v-model="booking.slot" required>
                            <option
                                v-for="(slot, index) in slots"
                                :key="index"
                                :value="slot"
                            >
                                {{ slot[0] }}-{{ slot[1] }}
                            </option>
                        </select>
                    </div>
                </div>
                <div>
                    <button
                        class="w-full py-1 px-4 border"
                        type="submit"
                        :disabled="booking.processing"
                    >
                        Submit
                    </button>
                </div>
            </form>
        </section>
    </main>
</template>
