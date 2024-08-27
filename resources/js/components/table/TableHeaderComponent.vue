<template>
    <div class="card-header">
        <h4>{{ tableHeader.heading }}</h4>
        <div class="card-header-action">
            <a v-if="tableHeader.link" :href="tableHeader.link" data-toggle="modal" :data-target="tableHeader.target" class="btn btn-primary">
                Add New
            </a>
        </div>
    </div>
</template>

<script>
    export default {
         name: 'TableHeader',
         props : ['tableHeader']
    }
</script>
