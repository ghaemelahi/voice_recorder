<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <form action="{{route('save_voice')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="voiec" class="">Record Voice</label>
                                <div class="col-md-4 mt-3"><button type="button" class="btn btn-sm btn-outline-info ml-2" id="start-recording">Start
                                    <i class="mr-2 fa fa-microphone "></i></button></div>
                                <div class="col-md-4"><audio id="audio-preview" controls></audio></div>
                                <div class="col-md-4"><button type="button" class="btn btn-sm btn-outline-danger mr-2"
                                    id="stop-recording">Stop<i class="mr-2 fa fa-stop"></i></button></div>
                                <input type="hidden" name="audio_path" id="audio-path">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-4">Save Voice</button>
                </form>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="container mt-3">
                <h2>Voice Table</h2>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Count</th>
                            <th>Voice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($voices as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <audio controls>
                                        <source src="{{ asset($item->voice) }}">
                                    </audio>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>


    <script src="{{ asset('js/RecordRTC.js') }}"></script>
    <script>
        var recorder;
        var audio = document.getElementById('audio-preview');
        var startButton = document.getElementById('start-recording');
        var stopButton = document.getElementById('stop-recording');

        startButton.onclick = function() {
            startButton.disabled = true;
            stopButton.disabled = false;

            navigator.mediaDevices.getUserMedia({
                audio: true
            }).then(function(stream) {
                recorder = RecordRTC(stream, {
                    type: 'audio'
                });
                recorder.startRecording();
            });
        };

        stopButton.onclick = function() {
            stopButton.disabled = true;
            startButton.disabled = false;

            recorder.stopRecording(function() {
                var blob = recorder.getBlob();
                audio.src = URL.createObjectURL(blob);
                var formData = new FormData();
                formData.append('audio', blob, 'audio.wav');

                // دریافت توکن CSRF از متا تگ
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('/save_temp', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                }).then(response => response.json()).then(result => {
                    if (result.path) {
                        document.getElementById('audio-path').value = result.path;
                    } else {
                        console.error('Error:', result.error);
                    }
                }).catch(error => {
                    console.error('Error:', error);
                });
            });
        };
    </script>
</body>

</html>
