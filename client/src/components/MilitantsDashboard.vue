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
			<template v-for="militant in militants">
				<tr v-bind:key="militant.id">
					<td>{{ militant.id }}</td>
					<td>{{ militant.name }}</td>
					<td>
						<form @submit.prevent="removeMilitant(militant)">
							<button class="button is-primary" v-bind:class="{ 'is-loading' : isLoading }">Remove</button>
						</form>
					</td>
				</tr>
			</template>
			</tbody>
		</table>
		<militant-form @completed="addMilitant"></militant-form>
	</div>
</template>

<script>
	import axios from 'axios'
	import Vue from 'vue'
	import MilitantForm from './MilitantForm.vue'

	export default {
		components: {
			MilitantForm
		},
		data() {
			return {
				militants: {},
				isLoading: true
			}
		},
		async created () {
			try {
				const response = await axios.get('/militants');
				this.militants = response.data;
				this.isLoading = false;
			} catch(e) {
				// handle authentication error here
			}
		},
		methods: {
			addMilitant(militant) {
				this.militants.push(militant)
			},
			removeMilitant(militant) {

			}
		}
	}
</script>