<template>
    <div class="bg-color">
        <div id="myCart" class="container my-cart ">
            <div class="row py-4 ">
                <h1 class="mb-4  col-12 text-center">
                    Riepilogo ordine
                </h1>




                <div v-if="formVisible" class="col-md-7 col-12 card p-5 myBorder-primary">
                    <form @submit.prevent="myLog()"  method="post" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label" for="name">Nome*</label>
                            <input class="form-control" type="text" v-model="dataOrder.name" name="name" id="name" maxlength="255" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">Email*</label>
                            <input class="form-control" type="email" v-model="dataOrder.email" name="email" id="email" maxlength="255" minlength="8" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="address">Indirizzo*</label>
                            <input class="form-control" type="text" v-model="dataOrder.address" name="address" id="address" maxlength="255" minlength="8" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="phone">Telefono*</label>
                            <input class="form-control" type="tel" v-model="dataOrder.phone" name="phone" id="phone" max="20" required>
                        </div>

                        <button type="submit" class="btn my_btn">Conferma e accedi al pagamento</button>
                    </form>


                </div>
                <div v-else-if="visible == false && formVisible == false" class="text-center">
                        Transazione avvenuta con successo
                    </div>
                <div v-if="formVisible" class="col-md-4 col-12 offset-md-1">
                    <h1 class="text-center">Il tuo carrello</h1>
                    <div class="my_order  position-relative inline-block bg-white ">
                        <div v-for="plate in arrCartPlate" class="col-12 d-flex my_plate_order " :key="plate.id">
                            <h5 class="col-8 offset-1 d-flex align-items-center">{{plate.name}}</h5>
                            <h5 class="col-3 d-flex align-items-center">{{plate.price}}€  x {{plate.quantity}} </h5>


                        </div>
                        <h3 class=" col-11 offset-1 position-relative bottom-0" >Prezzo totale: {{price_visualizzato}} €</h3>
                    </div>

                </div>
                <div v-if="visible" class="d-flex justify-content-center">
                        <v-braintree
                            :authorization=token_collegamento
                            @success="onSuccess"
                        />
                    </div>
            </div>

        </div>
    </div>
</template>

<script>
export default{
    name: 'CarrelloComp',
    props:{
        arrCartPlate: Array
    },
    data(){
        return {
            "dataOrder": {
                'name': '',
                'email': '',
                'address': '',
                'phone': null,
            },
            price_visualizzato: null,
            orderMessage: '',
            plateIds: [],
            arrQuantity: [],
            token_collegamento: 'null',

            visible: false,
            formVisible: true,
        }



    },

    created(){
        this.generateToken();

        console.log(this.arrCartPlate);

        if (localStorage.getItem('arrCartPlate')) {
            try {
                this.arrCartPlate = JSON.parse(localStorage.getItem('arrCartPlate'));
            }
            catch(e) {
                localStorage.removeItem('arrCartPlate');
            }
        }




        this.arrCartPlate.forEach(plate => {
            let newPrice = plate.price * plate.quantity;
            this.price_visualizzato += newPrice;
        });

        this.arrCartPlate.forEach(plate => {

            this.plateIds.push(plate.id);
            this.arrQuantity.push(plate.quantity);

            console.log(plate);
        });

        console.log(this.plateIds);
    },

    methods: {
        myLog(){
            this.formVisible = false;
            this.visible = true
        },

        onSuccess(payload) {
            let token = payload.nonce;
            axios.post('/api/orders/make/payment', {plate: this.plateIds, token: token, order: this.dataOrder, total: this.price_visualizzato}).then(response => {
                if (response.data.success) {
                    this.visible = false;
                }
                window.localStorage.clear();
            });
        },
        generateToken(){
            axios.get('/api/orders/generate').then(response => {
                if (response.data.success) {
                    this.token_collegamento = response.data.token

                }
            // console.log(this.token_collegamento);
            // console.log(this.visible);
            })
        }
    }
}
</script>


<style lang="scss" scoped>
    .my_btn{
        background-color: #d43a1c;
        color: white;

        &:hover{
            background-color: #ff5735;
            color:white;
        }
    }
    // #myCart{
    //     background-color: #ffe6d8;
    // }

    .my-cart {
        height: calc(100vh - 290px);
        // max-height: calc(100vh - 290px);
    }
    .bg-color {
        background-color: #ffe6d8;
    }
    .my_order{
        border: solid 2px #dee2e6;
        border-radius: 5px;

    }
    .my_plate_order{
        border-bottom: solid 2px #dee2e6;
    }
</style>
