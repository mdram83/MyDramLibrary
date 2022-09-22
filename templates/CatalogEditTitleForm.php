<div class="wrapper">

    {file:Catalog/CatalogAddButton}
    {if:status}{file:Catalog/CatalogMessageHandler}{/if}

    <div id="app">

        <div class="appTitle">Edit details... | <a href="" style="color: inherit;">Back to list</a></div>

        {if:title}

        <form method="POST" action="?module=Catalog&action=edit&title={:title.id}">
        <ul class="titleForm">


            <input id="titleId" name="id" value="{:title.id}" hidden>

            <li><label for "title">Title <span class="formRequired">*</span></label>
            <input id="title" name="title" 
                class="title" 
                type="text" 
                minlength="1" 
                maxlength="500" 
                pattern="([\p{L} \d\D\w\W]){1,500}" 
                placeholder="Title..."
                value="{:title.title|encode}" 
                required>
            </li>
            
            <li><label>Author</label>
            <input id="authorFirstname" name="authorFirstname" 
                class="title" 
                style="width: 46%;" 
                list="authorFirstnames" 
                onfocus="ajaxCreateAuthorDatalist(this);" 
                type="text" 
                minlength="1" 
                maxlength="255" 
                placeholder="First name" 
                autocomplete="off" 
                value="{:title.authorFirstname|encode}" >
            <datalist id="authorFirstnames"></datalist>

            <input id="authorLastname" name="authorLastname" 
                class="title" 
                style="width: 46%;" 
                list="authorLastnames" 
                onfocus="ajaxCreateAuthorDatalist(this);" 
                type="text" 
                minlength="1" 
                maxlength="255" 
                placeholder="Last name" 
                autocomplete="off" 
                value="{:title.authorLastname|encode}" >
            <datalist id="authorLastnames"></datalist>
            </li>

            <li><label for "isbn">ISBN</label>
            <input id="isbn" name="isbn" 
                class="title" 
                style="width: 46%;" 
                onchange="changeIsbnButtonStyle('enabled');" 
                oninput="changeIsbnButtonStyle('enabled');"  
                type="text" 
                minlength="10" 
                maxlength="17" 
                placeholder="ISBN"
                value="{:title.isbn|encode}" >
            <button type="button" id="isbnCheck" onclick="ajaxGetDetailsWithISBN();">Get details</button>
            </li>

            <li><label for "publisher">Publisher</label>
            <input id="publisher" name="publisher" 
                class="title" 
                list="publishers" 
                onfocus="ajaxCreatePublisherDatalist();" 
                type="text" 
                minlength="1" 
                maxlength="255" 
                pattern="([\p{L} \d\D\w\W]){1,255}" 
                placeholder="Publisher" 
                autocomplete="off" 
                value="{:title.publisher|encode}" >
            <datalist id="publishers"></datalist>
            </li>

            <li><label>Series / Volume</label>
            <input id="series" name="series" 
                class="title" 
                style="width: 65%;" 
                type="text" 
                minlength="1" 
                maxlength="255" 
                pattern="([\p{L}\d \'\.&,-]){1,100}" 
                placeholder="Series"
                value="{:title.series|encode}" >
            <span> / </span>
            <input id="volume" name="volume" 
                class="title" 
                style="width: 20%;" 
                type="text" 
                minlength="1" 
                maxlength="5" 
                pattern="([0-9]){1,5}" 
                placeholder="Vol"
                value="{:title.volume}" >
            </li>

            <li><label for "pages">Pages</label>
            <input id="pages" name="pages" 
                class="title" 
                type="text" 
                minlength="1" 
                maxlength="5" 
                pattern="([0-9]){1,5}" 
                placeholder="Pages"
                value="{:title.pages}" >
            </li>

            <li><label for "description">Description</label>
            <textarea id="description" name="description" 
                class="title" 
                minlength="1" 
                maxlength="10000" 
                placeholder="Description">{:title.description|encode}</textarea>
            </li>
            
            <li><label for "comment">Comment</label>
            <textarea id="comment" name="comment" 
                class="title" 
                minlength="1" 
                maxlength="10000" 
                placeholder="Comment">{:title.comment|encode}</textarea>
            </li>

            <li><label>Category 
                {ifnot:title.category.9}
                <span id="moreCategories" class="formMore blueFont">
                    (<a class="formMore" onclick="addCategoryField();">add more categories</a>)
                </span>
                {/ifnot}
            </label>
            
            <input id="category00" name="category[]" 
                class="title" 
                list="categories" 
                onfocus="ajaxCreateCategoryDatalist();" 
                type="text" 
                minlength="1" 
                maxlength="100" 
                pattern="([\p{L}\d \.&,-]){1,100}" 
                placeholder="Category" 
                autocomplete="off" 
                value="{:title.category.0|encode}">

            {roll:1to9}

            <input id="category0{|index}" name="category[]" 
                class="title" 
                {ifnot:title.category|index}style="display: none;" {/ifnot}
                list="categories" 
                onfocus="ajaxCreateCategoryDatalist();" 
                type="text" 
                minlength="1" 
                maxlength="100" 
                pattern="([\p{L}\d \.&,-]){1,100}" 
                placeholder="Category" 
                autocomplete="off" 
                value="{:title.category|index|encode}">
                
            {/roll}

            <datalist id="categories"></datalist>
            </li>

            <li><input id="submit" name="submit" class="title" type="submit" value="Save"></li>

        </ul>
        </form>
        {/if}
        
    </div>

</div>

{file:Catalog/AuthorSuggestionScript}