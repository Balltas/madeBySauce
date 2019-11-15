<template>
    <div>
        <h2>Curreny exchanger</h2>
        <form id="exhangeForm">
            <div class="form-group row">
                <div class="col-12 col-md-4">
                    <label for="from">From</label>
                    <select class="form-control" name="from" v-model="exchanger.from">
                        <!-- <option value="E3E">check for not existing</option> -->
                        <option v-for="currency in currencies" v-bind:value="currency.code">{{ currency.name }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="to">To</label>
                    <select class="form-control" name="to" v-model="exchanger.to">
                        <option v-for="currency in currencies" v-bind:value="currency.code">{{ currency.name }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="amount">Amount</label>
                    <input class="form-control" placeholder="amount" v-model="exchanger.amount" type="number" name="amount" step="0.01" />
                </div>
            </div>
            <div class="form-group row">
                <button class="btn btn-light" v-on:click="submitForm">Submit</button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        name: "CurrencyExchangeComponent.vue",
        data() {
            return {
                exchanger: {
                    form: '',
                    to: '',
                    amount: ''
                }
            }
        },
        props: ['currencies'],
        methods: {
            submitForm: function(event) {
                event.preventDefault();
                axios.get('api/currency', {
                    params: {
                        from: this.exchanger.from,
                        to: this.exchanger.to,
                        amount: this.exchanger.amount
                    }
                }).then(res => {
                    console.log(res);
                }).catch(err => {
                    console.log(err.response);
                });
            }
        },

    }
</script>

<style scoped>

</style>