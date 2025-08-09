@if (\Session::has('success'))
<script type="text/javascript">

    addEventListener("load", (event) => {
        var timerInterval;
        Swal.fire({
            title: "@lang('admin.success_message')",
            html: '',
            timer: 2000,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: function () {
                Swal.showLoading()
                timerInterval = setInterval(function () {
                    var content = Swal.getHtmlContainer()
                    if (content) {
                        var b = content.querySelector('b')
                        if (b) {
                            b.textContent = Swal.getTimerLeft()
                        }
                    }
                }, 100)
            },
            onClose: function () {
                clearInterval(timerInterval)
            }
        }).then(function (result) {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
                redirect('{{route("dashboard.categories.index")}}')
            }
        })
    });
</script>
@else

@endif
