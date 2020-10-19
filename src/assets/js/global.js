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