<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسليم الواجبات</title>
    <style>
        :root {
            color: #172033;
            background: #f5f7fb;
            font-family: Tahoma, Arial, sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px max(24px, calc((100vw - 1080px) / 2));
            background: #fff;
            border-bottom: 1px solid #e4e8f0;
        }
        .brand { font-size: 18px; font-weight: 700; }
        .account { display: flex; align-items: center; gap: 16px; color: #657087; font-size: 14px; }
        button, .button {
            border: 0;
            border-radius: 8px;
            padding: 11px 18px;
            background: #2856c7;
            color: white;
            cursor: pointer;
            font: inherit;
            text-decoration: none;
        }
        .logout { background: transparent; color: #47536a; border: 1px solid #d8deea; }
        main { width: min(100% - 32px, 980px); margin: 42px auto; }
        h1 { margin: 0 0 8px; font-size: 28px; }
        .subtitle { margin: 0 0 26px; color: #68748a; }
        .card {
            margin-bottom: 24px;
            padding: 26px;
            border: 1px solid #e3e8f1;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 5px 20px rgb(30 50 90 / 4%);
        }
        h2 { margin: 0 0 22px; font-size: 19px; }
        .fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .field { display: flex; flex-direction: column; gap: 8px; }
        .wide { grid-column: 1 / -1; }
        label { font-size: 14px; font-weight: 700; }
        input, textarea {
            width: 100%;
            border: 1px solid #d9dfeb;
            border-radius: 8px;
            padding: 12px;
            background: #fff;
            color: inherit;
            font: inherit;
        }
        textarea { min-height: 90px; resize: vertical; }
        input:focus, textarea:focus { outline: 2px solid #aec4ff; border-color: #5279d3; }
        .hint { margin: 0; color: #748097; font-size: 12px; }
        .submit { margin-top: 20px; }
        .message { margin-bottom: 20px; padding: 14px 16px; border-radius: 8px; background: #e8f7ee; color: #17643a; }
        .errors { margin-bottom: 20px; padding: 14px 16px; border-radius: 8px; background: #fff0ef; color: #a52b22; }
        .errors ul { margin: 8px 0 0; padding-right: 20px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: right; }
        th, td { padding: 14px 10px; border-bottom: 1px solid #edf0f5; vertical-align: middle; }
        th { color: #778198; font-size: 13px; font-weight: 600; }
        td { font-size: 14px; }
        .status { display: inline-block; border-radius: 20px; padding: 5px 10px; background: #e8f7ee; color: #17643a; white-space: nowrap; }
        .actions { display: flex; align-items: center; gap: 8px; }
        .download { padding: 8px 11px; background: #edf2ff; color: #2856c7; white-space: nowrap; }
        .delete { padding: 7px 10px; background: #fff0ef; color: #a52b22; white-space: nowrap; }
        .empty { padding: 28px 12px; color: #748097; text-align: center; }
        @media (max-width: 620px) {
            header { padding: 16px; }
            main { margin-top: 26px; }
            .card { padding: 19px; }
            .fields { grid-template-columns: 1fr; }
            .wide { grid-column: auto; }
            .account { gap: 8px; font-size: 12px; }
            .logout { padding: 8px 10px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="brand">بوابة تسليم الواجبات</div>
        <div class="account">
            <span>{{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="logout" type="submit">تسجيل الخروج</button>
            </form>
        </div>
    </header>

    <main>
        <h1>واجباتي</h1>
        <p class="subtitle">ارفع واجبك وتابع تسليماتك في مكان واحد.</p>

        @if (session('success'))
            <div class="message" role="status">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors" role="alert">
                تعذّر تسليم الواجب. راجع البيانات التالية:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="card">
            <h2>تسليم واجب جديد</h2>
            <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="fields">
                    <div class="field">
                        <label for="title">اسم الواجب</label>
                        <input id="title" name="title" type="text" maxlength="150" value="{{ old('title') }}" required>
                    </div>
                    <div class="field">
                        <label for="course">اسم المادة</label>
                        <input id="course" name="course" type="text" maxlength="150" value="{{ old('course') }}" required>
                    </div>
                    <div class="field wide">
                        <label for="notes">ملاحظات (اختياري)</label>
                        <textarea id="notes" name="notes" maxlength="2000">{{ old('notes') }}</textarea>
                    </div>
                    <div class="field wide">
                        <label for="file">ملف الواجب</label>
                        <input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip" required>
                        <p class="hint">PDF أو Word أو PowerPoint أو Excel أو ZIP، بحد أقصى 20 ميجابايت.</p>
                    </div>
                </div>
                <button class="submit" type="submit">تسليم الواجب</button>
            </form>
        </section>

        <section class="card">
            <h2>تسليماتي السابقة</h2>
            @if ($submissions->isEmpty())
                <div class="empty">لسه ما سلّمتش أي واجب. استخدم النموذج أعلاه لرفع أول واجب.</div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>الواجب</th>
                                <th>المادة</th>
                                <th>تاريخ التسليم</th>
                                <th>الحالة</th>
                                <th>الملف</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $submission)
                                <tr>
                                    <td>{{ $submission->title }}</td>
                                    <td>{{ $submission->course }}</td>
                                    <td>{{ $submission->created_at->format('Y-m-d H:i') }}</td>
                                    <td><span class="status">تم التسليم</span></td>
                                    <td>{{ $submission->original_filename }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="button download" href="{{ route('submissions.download', $submission) }}">تحميل</a>
                                            <form action="{{ route('submissions.destroy', $submission) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا التسليم؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="delete" type="submit">حذف</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
