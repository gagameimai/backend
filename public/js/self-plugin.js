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
