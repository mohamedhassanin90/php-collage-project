<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول | بوابة الواجبات</title>
    <style>
        :root {
            color: #172033;
            background: #f5f7fb;
            font-family: Tahoma, Arial, sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
        }
        * { box-sizing: border-box; }
        body { display: flex; min-height: 100vh; flex-direction: column; margin: 0; }
        header, footer { padding: 18px 24px; background: #fff; border-bottom: 1px solid #e4e8f0; }
        header { font-size: 18px; font-weight: 700; }
        footer { margin-top: auto; border-top: 1px solid #e4e8f0; border-bottom: 0; color: #778198; text-align: center; font-size: 12px; }
        main { display: grid; flex: 1; place-items: center; padding: 30px 16px; }
        .card { width: min(100%, 420px); padding: 30px; border: 1px solid #e3e8f1; border-radius: 14px; background: #fff; box-shadow: 0 5px 20px rgb(30 50 90 / 4%); }
        h1 { margin: 0 0 8px; font-size: 24px; text-align: center; }
        .subtitle { margin: 0 0 26px; color: #68748a; font-size: 14px; line-height: 1.7; text-align: center; }
        .field { display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; }
        label { font-size: 14px; font-weight: 700; }
        input { width: 100%; padding: 12px; border: 1px solid #d9dfeb; border-radius: 8px; color: inherit; font: inherit; }
        input:focus { outline: 2px solid #aec4ff; border-color: #5279d3; }
        .remember { display: flex; align-items: center; gap: 8px; margin: 4px 0 20px; color: #59657a; font-size: 13px; }
        .remember input { width: auto; }
        button { width: 100%; border: 0; border-radius: 8px; padding: 13px; background: #2856c7; color: #fff; cursor: pointer; font: inherit; font-weight: 700; }
        .errors { margin-bottom: 18px; padding: 12px 14px; border-radius: 8px; background: #fff0ef; color: #a52b22; font-size: 13px; }
        .register { display: block; margin-top: 20px; color: #2856c7; font-size: 14px; text-align: center; text-decoration: none; }
    </style>
</head>
<body>
    <header>بوابة تسليم الواجبات</header>

    <main>
        <section class="card" aria-labelledby="login-title">
            <h1 id="login-title">تسجيل الدخول</h1>
            <p class="subtitle">سجّل دخولك لتسليم واجباتك ومتابعتها.</p>

            @if ($errors->any())
                <div class="errors" role="alert">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="email">البريد الإلكتروني</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                </div>
                <div class="field">
                    <label for="password">كلمة المرور</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" required>
                </div>
                <label class="remember">
                    <input type="checkbox" name="remember" value="1">
                    تذكرني
                </label>
                <button type="submit">دخول</button>
            </form>
            <a class="register" href="{{ route('register') }}">مستخدم جديد؟ أنشئ حسابك</a>
        </section>
    </main>

    <footer>بوابة تسليم الواجبات</footer>
</body>
</html>
