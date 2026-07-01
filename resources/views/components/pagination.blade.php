<ul class="pagination pagination-lm m-1">
    <li v-if="pagination.current_page > 1">
        <a @click="getItems(pagination.current_page - 1)" aria-label="Previous">
            <span class="page-link" aria-hidden="true"><</span>
        </a>
    </li>
    <li v-for="page in pagination.total" v-if="page >= pagination.start" class="page-item" v-bind:class="page == pagination.current_page ? 'active' : ''">
        <a class="page-link" href="javascript:void(0)" @click="getItems(page)">@{{ page }}</a>
    </li>
    <li v-if="pagination.current_page != pagination.total && pagination.total != 0">
        <a @click="getItems(pagination.current_page + 1)" aria-label="Previous">
            <span class="page-link" aria-hidden="true">></span>
        </a>
    </li>
</ul>
