$('.validate-comment').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id-comment');
    $.ajax({
        type: 'POST',
        url: "/admin/commentaires/valider",
        data: { 'id' : id }
    }).done(function (data) {
        if (data.success) {
            location.reload();
        }
    })
});

$('.unvalidate-comment').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id-comment');
    $.ajax({
        type: 'POST',
        url: "/admin/commentaires/refuser",
        data: { 'id' : id }
    }).done(function (data) {
        if (data.success) {
            location.reload();
        }
    })
});

    $('.archive-post').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id-post');
    $.ajax({
        type: 'POST',
        url: "/admin/articles/archiver",
        data: { 'id' : id }
    }).done(function (data) {
        if (data.success) {
            location.reload();
        }
    })
});