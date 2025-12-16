<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Printer Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        button {
            background: #3498db;
            border: none;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }
        button:disabled {
            background: #95a5a6;
        }
        #status {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Network Printer Test (192.168.18.200)</h2>
    <button id="printBtn">Run Test Print</button>
    <div id="status"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#printBtn').on('click', function() {
            let btn = $(this);
            btn.prop('disabled', true).text('Printing...');
            $('#status').text('');

            $.ajax({
                url: "{{ route('printer.test') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#status').css('color', 'green').text(response.message);
                    } else {
                        $('#status').css('color', 'red').text(response.message);
                    }
                },
                error: function(xhr) {
                    $('#status').css('color', 'red').text('Error: ' + xhr.statusText);
                },
                complete: function() {
                    btn.prop('disabled', false).text('Run Test Print');
                }
            });
        });
    </script>
</body>
</html>
