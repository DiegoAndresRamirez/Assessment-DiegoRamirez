<script setup>
import DashboardDoctor from '@/Pages/Doctor/Dashboard.vue';
import DashboardPatient from '@/Pages/Patient/Dashboard.vue';

const props = defineProps({
    auth: Object,
    title: String,
    description: String,
    schedule: Object
});
</script>

<template>
    <component 
        :is="auth.user.roles.includes('doctor') ? DashboardDoctor : DashboardPatient"
        :auth="auth"
        :schedule="schedule"
        :title="title"
        :description="description">
    </component>
</template>
