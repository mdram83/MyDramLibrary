<div class="wrapper">

    {file:Catalog/CatalogAddButton}
    {if:status}{file:Catalog/CatalogMessageHandler}{/if}

    <div id="app">

        <div class="listWrapper">
            
            {loop:titles}
            <div class="listElement">
                <div class="listPicture"><img class="listPicture" src="images\general\book_open.png" alt="cover"></div>
                <div class="titleGeneral">
                    <p class="listTitle"><b><a class="listTitle darkblueFont" href="/?module=Catalog&action=edit&title={:id}">{:title|encode}</a></b></p>
                    
                    {if:author}<p class="listAuthor"><i>{:author|encode}</i></p>{/if}
                    
                    <p class="listDetails">
                        {if:series}{:series|encode}&nbsp;{/if}
                        {if:volume}Vol {:volume}{/if}
                    </p>
                    
                    {if:category}
                    <div class="titleCategories">
                        {loop:category}<p class="listCategory">{:category|encode}</p>{/loop}
                    </div>
                    {/if}

                </div>
            </div>
            {/loop}

        </div>
      
    </div>

</div>