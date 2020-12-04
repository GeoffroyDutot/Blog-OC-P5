$('.validate-comment').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id-comment');
    $.ajax({
        type: 'PUT',
        url: "/admin/commentaires/"+id+"/valider"
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
        type: 'PUT',
        url: "/admin/commentaires/"+id+"/refuser"
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
        type: 'PUT',
        url: "/admin/articles/"+id+"/archiver"
    }).done(function (data) {
        if (data.success) {
            location.reload();
        }
    })
});

$('.unarchive-post').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id-post');
    $.ajax({
        type: 'PUT',
        url: "/admin/articles/"+id+"/desarchiver"
    }).done(function (data) {
        if (data.success) {
            location.reload();
        }
    })
});

$('.delete-post').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id-post');
    $.ajax({
        type: 'DELETE',
        url: "/admin/articles/supprimer/"+id
    }).done(function (data) {
        if (data.success) {
            location.reload();
        }
    })
});

$('.deactivate-user').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id-user');
    $.ajax({
        type: 'PUT',
        url: "/admin/utilisateurs/"+id+"/desactiver"
    }).done(function (data) {
        if (data.success) {
            location.reload();
        }
    })
});

$('.reactivate-user').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id-user');
    $.ajax({
        type: 'PUT',
        url: "/admin/utilisateurs/"+id+"/reactiver"
    }).done(function (data) {
        if (data.success) {
            location.reload();
        }
    })
});