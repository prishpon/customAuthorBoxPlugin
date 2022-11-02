import { createApp } from 'vue/dist/vue.esm-bundler.js'

import './index.scss';

const ajaxurl = window.ajaxurl;

if(document.getElementById("sv-autors")) {
    createApp({
        data() {
            return {
                nonce: '',
                data: [],
                socialEditKeys: [],
                showModel:false,
                edited:null,
                modalData:{},
                modalError: ''
            }
        }, //data
        methods: {
            showModelBtn(){
                this.showModel = true;
                //document.getElementsByTagName('body')[0].style.overflow = "hidden";
            },
            editAutor(i){
                this.edited = i ;
            },
            saveAutor(){
                fetch(ajaxurl+'?action=sv_aut_add_user',{
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({"data": this.modalData, "nonce": this.nonce}),
                }).then(response => response.json())
                    .then(data => {
                        console.log('response', data);
                        if(data.response == 'OK'){
                            this.modalData.ID = data.ID
                            this.data.push(this.modalData);
                            this.modalData = {};
                            this.closeModal();
                        }else{
                            this.modalError = data.response
                            console.warn(data.response)
                        }
                    });
            },
            saveEditedAutor(){
                fetch(ajaxurl+'?action=sv_aut_edit_user',{
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({"data": this.data[this.edited], "nonce": this.nonce}),
                }).then(response => response.json())
                    .then(data => {
                        console.log('response', data);
                        if(data.response == 'OK'){
                            //
                        }else{
                            console.warn(data.response)
                        }
                    });

                this.edited = null;
                this.socialEditKeys= [];
            },
            closeModal(){
                this.modalData = {};
                this.showModel = false;
                document.getElementsByTagName('body')[0].style.removeProperty("overflow");
            },
            addSocial(){
                if(this.data[this.edited].social == null){

                    this.data[this.edited].social = [{
                        name: '',
                        link: '',
                    }]

                }else{
                    this.data[this.edited].social.push({
                        name: '',
                        link: '',
                    })
                }

                this.socialEditKeys.push(this.data[this.edited].social.length -1 )
            },
            addModalSocial(){
                if(this.modalData.social == null){
                    this.modalData.social = [{
                        name: '',
                        link: '',
                    }]

                }else{
                    this.modalData.social.push({
                        name: '',
                        link: '',
                    })
                }
            },
            editSingleSocialItem(key){
                this.socialEditKeys.push(key);
            },
            saveSingleSocialItem(key){
                this.socialEditKeys = this.socialEditKeys.filter(item => item != key);
            },
            getUsers(){
                fetch(ajaxurl+'?action=sv_aut_get_users')
                    .then(response => response.json())
                    .then(data => {
                        this.data = data.users
                        this.nonce = data.nonce
                    });
            }
        },  //methods
        computed: {

        }, //computed
        mounted() {

        //    this.data = [{
        //        name:"test",
        //        bio:"bio test",
        //        social: [{
        //            name: 'facebook',
        //            link: 'https://',
        //        },
        //            {
        //                name: 'linked',
        //                link: 'https://',
        //            }],
        //        photo:""
        // }];


            this.getUsers()
        }// mounted

    }).mount('#sv-autors')
}