// delay loading
$(function () {
    // title
    $('[data-toggle="tooltip"]').tooltip();

    // message alert box
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "timeOut": 2000,
    };
});

// time display
function showTime() {
    let d = new Date();
    let h = addZero(d.getHours());
    let m = addZero(d.getMinutes());
    let s = addZero(d.getSeconds());
    $('#time_now').text(h + ':' + m + ':' + s);

    setTimeout('showTime()', 1000);
}

// add zero
function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }

    return i;
}

showTime();

var ckeditorConfig = {
    filebrowserImageBrowseUrl: '/admin/filemanager?type=Images',
    filebrowserImageUploadUrl: '/admin/filemanager/upload?type=Images',
    filebrowserBrowseUrl: '/admin/filemanager?type=Files',
    filebrowserUploadUrl: '/admin/filemanager/upload?type=Files',
    height: '500px'
};

var countyMap = [
    '台北市','新北市','桃園市','台中市','台南市','嘉義市','高雄市','新竹縣','苗栗縣',
    '彰化縣','南投縣','雲林縣','嘉義縣','屏東縣','宜蘭縣','花蓮縣','台東縣',
    '澎湖縣','金門縣','連江縣','基隆市','新竹市','新竹縣'
];

(function($) {
    $.fn.filemanager = function(type, options, lightbox = false) {
        type = type || 'file';

        this.on('click', function(e) {
            var route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
            var target_input = $('#' + $(this).data('input'));
            var target_preview = $('#' + $(this).data('preview'));
            window.open(route_prefix + '?type=' + type, 'FileManager', 'width=900,height=600');
            window.SetUrl = function (items) {
            var file_path = items.map(function (item) {
                return item.url;
            }).join(',');

            // set the value of the desired input to image url
            target_input.val('').val(file_path).trigger('change');

            // clear previous preview
            target_preview.html('');

            // set or change the preview image src
            items.forEach(function (item) {
                let tag;
                if (lightbox) {
                    let a = $('<a>').attr({
                        'data-toggle': 'lightbox',
                        'href': item.thumb_url
                    });

                    tag = a.append(
                        $('<img>').css('height', '10rem').attr('src', item.thumb_url)
                    );
                } else {
                    tag = $('<img>').css('height', '10rem').attr('src', item.thumb_url);
                }

                target_preview.append(tag);
            });

            // trigger change event
            target_preview.trigger('change');
            };
            return false;
        });
    }
})(jQuery);

/* ------------------------------------------------------------------
 * 列表排序（統一規則：數字小的在前）
 * 用法：列表頁的 new Vue({ mixins: [window.sortMixin || {}], ... })，
 * 列表頁需要有 items 陣列與 sortItems() 方法（儲存排序）。
 * 操作：按住 ☰ 把手拖曳整列、按 ↑ ↓ 移一格、或直接改數字（改完自動儲存）。
 *
 * 分組排序：列表若分「分類」（例如資源檔案依分類），在該頁的 methods 裡定義
 *   sortGroupKey: function(item) { return item.resource_category_id; }
 * 就只能在「同一個分類內」排序：拖曳不能跨分類、↑↓ 會跳過其他分類的列，
 * 重新分配數字時也只動同一分類的項目。沒定義就是整個列表一組。
 *
 * 重新分配數字：沿用該組原本的排序數字（不影響其他分組／其他頁），
 * 若數字有重複（例如全是 1），改用「該組最小的數字起算、依序 +1」。
 * ------------------------------------------------------------------ */
window.sortMixin = {
    data: function() {
        return { dragFrom: -1, dragOverIdx: -1, handleDown: false };
    },
    methods: {
        sortGroupOf: function(item) {
            return typeof this.sortGroupKey === 'function' ? this.sortGroupKey(item) : 0;
        },
        applyOrder: function(group) {
            var self = this;
            var desc = this.sortDesc === true;
            var searching = !!(this.search && this.search.is_search);
            if (!searching) {
                // 一般情況（整份清單都在畫面上）：拖曳或按 ↑↓ 之後，整頁重新編號 1、2、3…（第 2 頁從 501 起），
                // 置頂那一組排前面所以拿到小號碼，一般組接著編。這樣資料庫不會再出現 0,0,0,1,1,1 這種重複號碼。
                var per = parseInt(this.sortPerPage, 10) || 500;
                var offset = ((parseInt(this.page, 10) || 1) - 1) * per;
                var n = this.items.length;
                this.items.forEach(function(it, i) { it.sort = desc ? offset + n - i : offset + i + 1; });
                this.sortItems();
                return;
            }
            // 搜尋中（畫面上只有一部分資料）：只動同一組、沿用原本那幾個號碼重新分配，不碰沒顯示出來的資料
            var members = this.items.filter(function(it) { return self.sortGroupOf(it) === group; });
            var vals = members.map(function(i) { return parseInt(i.sort, 10) || 0; }).sort(function(a, b) { return desc ? b - a : a - b; });
            var distinct = vals.every(function(v, i) { return i === 0 || (desc ? v < vals[i - 1] : v > vals[i - 1]); });
            var base = vals.length ? Math.max(1, Math.min.apply(null, vals)) : 1;
            var n2 = members.length;
            members.forEach(function(it, i) { it.sort = distinct ? vals[i] : (desc ? base + n2 - 1 - i : base + i); });
            this.sortItems();
        },
        moveItem: function(num, dir) {
            var g = this.sortGroupOf(this.items[num]);
            var to = num + dir;
            while (to >= 0 && to < this.items.length && this.sortGroupOf(this.items[to]) !== g) to += dir;
            if (to < 0 || to >= this.items.length) return;
            var it = this.items.splice(num, 1)[0];
            this.items.splice(to, 0, it);
            this.applyOrder(g);
        },
        dragStart: function(num, e) {
            if (!this.handleDown) { e.preventDefault(); return; }
            this.dragFrom = num;
            if (e.dataTransfer) {
                e.dataTransfer.effectAllowed = 'move';
                try { e.dataTransfer.setData('text/plain', String(num)); } catch (x) {}
            }
        },
        dragEnter: function(num) {
            if (this.dragFrom < 0) return;
            this.dragOverIdx = (this.sortGroupOf(this.items[num]) === this.sortGroupOf(this.items[this.dragFrom])) ? num : -1;
        },
        dropRow: function(num) {
            var from = this.dragFrom;
            this.dragEnd();
            if (from < 0 || from === num) return;
            var g = this.sortGroupOf(this.items[from]);
            if (this.sortGroupOf(this.items[num]) !== g) {
                if (window.toastr) toastr.warning('只能在同一個分類內調整順序');
                return;
            }
            var it = this.items.splice(from, 1)[0];
            this.items.splice(num, 0, it);
            this.applyOrder(g);
        },
        dragEnd: function() {
            this.dragFrom = -1;
            this.dragOverIdx = -1;
            this.handleDown = false;
        }
    }
};
(function() {
    var st = document.createElement('style');
    st.textContent = '.sort-handle{cursor:grab;color:#98a2ad;font-size:18px;margin-right:6px;user-select:none}' +
        '.sort-arrow{display:inline-block;width:24px;text-align:center;border:1px solid #ced4da;border-radius:3px;margin-right:3px;color:#495057;text-decoration:none}' +
        '.sort-arrow:hover{background:#e9ecef;text-decoration:none}' +
        '.sort-num{display:inline-block;width:70px;margin-top:4px}' +
        'tr.sort-over td{border-top:3px solid #007bff}' +
        'tr.sort-over-down td{border-bottom:3px solid #007bff}' +
        /* iPhone 風格開關（啟用／停用）：整顆可點，綠色＝啟用 */
        '.sw-toggle{position:relative;display:inline-block;width:46px;height:28px;margin:0 8px 0 0;vertical-align:middle;cursor:pointer}' +
        '.sw-toggle input{opacity:0;width:0;height:0;position:absolute}' +
        '.sw-slider{position:absolute;inset:0;background:#d1d5db;border-radius:28px;transition:background .2s}' +
        '.sw-slider:before{content:"";position:absolute;left:2px;top:2px;width:24px;height:24px;background:#fff;border-radius:50%;box-shadow:0 1px 3px rgba(0,0,0,.35);transition:transform .2s}' +
        '.sw-toggle input:checked + .sw-slider{background:#34c759}' +
        '.sw-toggle input:checked + .sw-slider:before{transform:translateX(18px)}' +
        'td:has(> .sw-toggle){white-space:nowrap;min-width:110px}' +
        '.sw-text{font-size:13px;vertical-align:middle;white-space:nowrap}.sw-text.on{color:#28a745}.sw-text.off{color:#98a2ad}';
    document.head.appendChild(st);
})();
