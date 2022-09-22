<div class="wrapper">

    {if:status}{file:Catalog/CatalogMessageHandler}{/if}

    <div id="app">

        <div class="appTitle">Enter details... | <a href="" style="color: inherit;">Back to list</a></div>

        <form method="POST" action="?module=Catalog&action=add">
        <ul class="titleForm">

            <li><label for "title">Title <span class="formRequired">*</span></label>
            <input id="title" name="title"          
                class="title" 
                type="text" 
                minlength="1" 
                maxlength="500" 
                pattern="([\p{L} \d\D\w\W]){1,500}" 
                placeholder="Title" 
                value="{:formValues.title|encode}" 
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
                value="{:formValues.authorFirstname|encode}">
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
                value="{:formValues.authorLastname|encode}">
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
                value="{:formValues.isbn|encode}">
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
                value="{:formValues.publisher|encode}">
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
                value="{:formValues.series|encode}">
            <span> / </span>
            <input id="volume" name="volume" 
                class="title" 
                style="width: 20%;" 
                type="text" 
                minlength="1" 
                maxlength="5" 
                pattern="([0-9]){1,5}" 
                placeholder="Vol" 
                value="{:formValues.volume}">
            </li>
            
            <li><label for "pages">Pages</label>
            <input id="pages" name="pages" 
                class="title" 
                type="text" 
                minlength="1" 
                maxlength="5" 
                pattern="([0-9]){1,5}" 
                placeholder="Pages" 
                value="{:formValues.pages}">
            </li>

            <li><label>
                Category&nbsp;<span id="moreCategories" class="formMore blueFont">(<a class="formMore" onclick="addCategoryField();">add more categories</a>)</span>
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
                value="{:formValues.category.0|encode}">

            {roll:1to9}

            <input id="category0{|index}" name="category[]" 
                class="title" 
                {ifnot:formValues.category|index}style="display: none;" {/ifnot}
                list="categories" 
                onfocus="ajaxCreateCategoryDatalist();" 
                type="text" 
                minlength="1" 
                maxlength="100" 
                pattern="([\p{L}\d \.&,-]){1,100}" 
                placeholder="Category" 
                autocomplete="off" 
                value="{:formValues.category|index|encode}">

            {/roll}

            <datalist id="categories"></datalist>
            </li>            
            
            <li><input id="submit" name="submit" class="title" type="submit" value="Add"></li>
            
        </ul>
        </form>
        
    </div>

</div>

{file:Catalog/AuthorSuggestionScript}