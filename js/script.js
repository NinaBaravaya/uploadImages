$(document).ready(function () {
    var max = 5;//устанавливаем max кол-во полей
    var min = 1;//устанавливаем min кол-во полей
    $("#del").attr("disabled", true);
    $(document).on("click", "#add", function (event) {
        var total = $("input[name='galleryimg[]']").length;
        if (total < max) {
            $("#btnimg").append('<div><input type="file" name="galleryimg[]" /></div>');
            $("#nameimg").append('<div><input type="text" name="name[]" /></div>');

            if (max == total + 1) {//можем добавить не более 5 полей
                $("#add").attr("disabled", true);
            }
            $("#del").removeAttr("disabled");
        }
    });
    $("#del").click(function () {
        var total = $("input[name='galleryimg[]']").length;
        if (total > min) {
            $("#btnimg div:last-child").remove();
            $("#name div:last-child").remove();

            if (min == total - 1) {//не позволяем удалять последнее поле
                $("#del").attr("disabled", true);
            }
            $("#add").removeAttr("disabled");
        }
    });
});


$(document).on("click", "#save_img", function (event) {
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: new FormData($('form')[0]),
        cache: false,
        contentType: false,
        processData: false,
        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                return myXhr;
            }
        },
        success: function (d) {
            var html = $.parseHTML(d);
            var div_content = html[9];
            $(".content").html($(div_content).html());
        }
    });
});