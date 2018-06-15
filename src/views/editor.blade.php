<script src="{{asset('vendor/kindeditor/kindeditor-all-min.js')}}"></script>
<script src="{{asset('vendor/kindeditor/lang/zh-CN.js')}}"></script>
<script>
    KindEditor.ready(function (K) {
        window.editor = K.create('#'+'{{$editor}}', {
            uploadJson: "{{asset('kindeditor/upload')}}",
            //fileManagerJson: '',
            allowFileManager: false,
            formatUploadUrl: false
        });
    });
</script>