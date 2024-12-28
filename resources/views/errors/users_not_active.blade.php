<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>المستخدم غير فعال</title>
    <style>
                @font-face {
            src: url("{{ asset('assets/fonts/Tajawal/Tajawal-Regular.ttf') }}");
            font-family: Tajawal;
        }

        *{
            font-family: Tajawal , 'sans-serif';
        }

        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            color: #333;
        }
        h1 {
            font-size: 3rem;
            color: #d9534f;
        }


    </style>
</head>
<body>
    <h1>هذا المستخدم غير فعال</h1>
    <p>الرجاء التواصل مع الدعم الفني لمزيد من المعلومات.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button style="
        background-color: #d9534f;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        border-radius: 5px;"
        class="btn btn-danger btn-sm"
        type="submit">تسجيل الخروج
    </button>
    </form>
</body>
</html>
