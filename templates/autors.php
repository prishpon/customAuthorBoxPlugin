<div id="sv-autors" class="wrap">
    <h1>Sv autors</h1>


<!--table2-->

<table class="widefat striped" id="aut-table">
    <thead>
    <tr>
        <td> Autor Name</td>
        <td> Autor Bio</td>
        <td> Autor Social</td>
        <td> Autor Photo</td>
        <td> Actions</td>
    </tr>
    </thead>
    <tbody>


    <tr v-for="(d, index) in data" :key="index">
        <td>
            <div v-if="edited!=index">
                {{d.name}}
            </div>
            <input v-if="edited==index" v-model="d.name" type="text" placeholder="Name">
        </td>
        <td>
            <div v-if="edited!=index">
                {{d.bio}}
            </div>
            <textarea v-if="edited==index" v-model="d.bio" placeholder="Author bio"></textarea>
        </td>
        <td>
            <ul>
                <li v-for ="(social, key) in d.social" class="link-actions">
                    <a v-if="!socialEditKeys.includes(key) || edited!=index" :href="social.link">{{social.name}}</a>
                    <span v-if="socialEditKeys.includes(key) && edited==index">
                        <input v-model="social.name" type="text" placeholder="Link name">
                        <input v-model="social.link" type="text" placeholder="URL">
                    </span>
                    <div v-if="edited==index" class="edit-links">
                    <span v-if="!socialEditKeys.includes(key)" @click="editSingleSocialItem(key)" class="dashicons dashicons-edit">  </span>
                    <span v-if="socialEditKeys.includes(key)"  @click="saveSingleSocialItem(key)" class="dashicons dashicons-saved">  </span>
                    </div>
                </li>
            </ul>
            <span  v-if="edited==index"  @click="addSocial()" class="add-social dashicons dashicons-plus"></span>
            </td>
        <td>
            <img src="" alt="">
        </td>
        <td>
            <button v-if="edited!=index" type="button" @click="editAutor(index)" :disabled="edited != null" class="button button-secondary">
                Edit Autor
             </button>
            <button v-if="edited==index" type="button" @click="saveEditedAutor()" class="button button-primary">
                Save changes
            </button>
        </td>
    </tr>

    </tbody>
</table>

    <div class="add-autor">
        <button type="button" @click="showModelBtn()" class="button button-secondary">
            Add sv Autor
        </button>
    </div>

    <div :class="[{ show: showModel },'fade']" >
    <div class="modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Adding new author</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" @click="closeModal()">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                        <div>
                            <label>Author name</label>
                            <input v-model="modalData.name" type="text" name="autName" placeholder="Name">
                        </div>

                        <div>
                            <label>Author bio</label>
                            <textarea  v-model="modalData.bio" placeholder="Author bio"></textarea>
                        </div>

                        <div>
                            <label>Social links</label>
                           <ul>
                               <li v-for="social in modalData.social" class="link-actions">
                                   <input v-model="social.name" type="text" placeholder="Link name">
                                   <input v-model="social.link" type="text" placeholder="URL">
                               </li>
                               <li class="add-social dashicons dashicons-plus" @click="addModalSocial()"></li>
                           </ul>
                        </div>
                      <label>Author photo</label>
<!--                        <div><input v-model="modalData.photo" type="file" name="autPhoto"></div>-->


                </div>
                <div class="modal-footer">
                    <button type="button" class="button button-primary" @click="saveAutor()">Save new author</button>
                    <p v-if="modalError">{{ modalError }}</p>
                </div>
            </div>
        </div>
       </div>
    </div>
</div>
