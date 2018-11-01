<template>
	<div>
		<span class="help is-info"  v-if="isLoading">Loading...</span>
		<table class="table" v-else>
			<thead>
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
			<template v-for="group in groups">
				<tr v-bind:key="group.id">
					<td>{{ group.id }}</td>
					<td>{{ group.name }}</td>
					<td>
						<form @submit.prevent="removeGroup(group)">
							<button class="button is-primary" v-bind:class="{ 'is-loading' : isLoading }">Remove</button>
						</form>
					</td>
				</tr>
			</template>
			</tbody>
		</table>
		<group-form @completed="addGroup"></group-form>
	</div>
</template>

<script>
	import axios from 'axios'
	import Vue from 'vue'
	import GroupForm from './GroupForm.vue'

	export default {
		components: {
			GroupForm
		},
		data() {
			return {
				groups: {},
				isLoading: true
			}
		},
		async created () {
			try {
				const response = await axios.get('/groups');
				this.groups = response.data;
				this.isLoading = false;
			} catch(e) {
				// handle authentication error here
			}
		},
		methods: {
			addGroup(group) {
				this.groups.push(group)
			},
			removeGroup(group) {

			}
		}
	}
</script>