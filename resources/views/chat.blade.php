<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chat với AI</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/components/goals.css', 'resources/js/app.js', 'resources/js/component/goals.js'])
</head>

<body>
    <h2>Chat với AI</h2>
    <div id="chatBox"></div>
    <div>
        <input type="text" id="aim" placeholder="Nhập mục tiêu">
    </div>
    <div>
        <input type="text" id="promise" placeholder="Nhập mong muốn đạt được">
    </div>
    <div>
        <input type="text" id="currentProcess" placeholder="Nhập tiến độ hiện tại">
    </div>
    <div>
        <input type="text" id="duration" placeholder="Nhập thời gian thực hiện">
    </div>
    <button onclick="sendMessage()">Gửi</button>

    <div id="suggestion"></div>

    <script>
        async function sendMessage() {
            const aim = document.getElementById('aim').value;
            const promise = document.getElementById('promise').value;
            const currentProcess = document.getElementById('currentProcess').value;
            const duration = document.getElementById('duration').value;

            let promt = `Bạn hãy là một trợ lý quản lý thời gian. Hiên tại tôi có mục tiêu ${aim} và mong muốn ${promise}. Tình trạng của tôi hiện
            là ${currentProcess}. Tôi mong muốn hoàn thành trong ${duration}.`

            const res = await fetch('/analytics', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    'message': promt
                })
            });

            const data = await res.json();
            console.log(data);


            document.getElementById('suggestion').innerHTML += `${data.message}`;
        }
    </script>
</body>

</html>



{{-- <form action="{{ route('postMessage') }}" method="POST">
    @csrf
    <input type="text" name="message">
    <button>Hỏi</button>
</form> --}}
