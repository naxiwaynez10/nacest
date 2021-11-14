function submitForm(id){
    $(id).submit(function(e) {
        e.preventDefault(); // prevent actual form submit
        var form = $(this);
        var url = form.attr('action'); //get submit url [replace url here if desired]
        $.ajax({
             type: "POST",
             url: url,
             data: form.serialize(), // serializes form input
             success: function(data){
                 console.log(data);
                //  var response = JSON.parse(data);
                 console.log(data.message);
                //  swal(data.message);
             }
        });
    });
}