<div id="vgi-settings" class="wrap">
        <h1 class="wp-heading-inline">SV AUTHORS SETTINGS</h1>
        <?= $msg; ?>
<style>
    .d-flex{
        display: flex;
        align-items: center;
    }
    .d-flex > div{
        margin-right:20px;
    }
    td.sv-custom-author{
        width:15%;
    }
</style>
    <form method="post" action="?post_type=sv_authors&page=sv_authors_settings&update">
        <?php wp_nonce_field(SV_AUT_URL, 'sv-authors-options'); ?>
        <table class="form-table">
            <thead>
                <tr>
                    <th><label for="show_heading_text">Set the translation for author box labels.</label></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class = "sv-custom-author">
                        <div class="d-flex">
                            <div>
                               <label for="fname">Updated on:</label>
                            </div>
                            <div>
                               <input type="text" name="updatedOn" value="<?= $options['updatedOn']; ?>">
                            </div>
                        </div>
                    </td>
                    <td class = "sv-custom-author">
                        <div class="d-flex">
                            <div>
                                <label for="fname">Published on:</label>
                            </div>
                            <div>
                                <input type="text" name="publishedOn" value="<?= $options['publishedOn']; ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class = "sv-custom-author">
                        <div class="d-flex">
                            <div>
                                <label for="fname">About the author </label>
                            </div>
                            <div>
                                <input type="textarea" name="aboutAuthour" value="<?= $options['aboutAuthour']; ?>">
                            </div>
                        </div>
                    </td>
                    <td class = "sv-custom-author">
                        <div class="d-flex">
                            <div>
                                <label for="fname">See other posts by</label>
                            </div>
                            <div>
                                <input type="textarea" name="otherPosts" value="<?= $options['otherPosts']; ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class = "sv-custom-author">
                        <div class="d-flex">
                            <div>
                                <label for="fname">Follow on</label>
                            </div>
                            <div>
                                <input type="text" name="followOn" value="<?= $options['followOn']; ?>">
                            </div>
                        </div>
                    </td>
                </tr>


            </tbody>

        </table>

        <input type="submit" name="submit" class="button-primary" value="Update Translations">
    </form>
</div>