<!DOCTYPE html>
<html>
<head>
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Hello, {{ $data['user_id'] }}</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Email</th>
                <th>Registration Status</th>
                <th>Vacancy</th>
                <th>Post</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $data['user_id'] }}</td>
                <td>{{ $data['email'] }}</td>
                <td>{{ $data['registration_status'] }}</td>
                <td>{{ $data['vacancy'] ? $data['vacancy']->name : 'N/A' }}</td>
                <td>{{ $data['post'] ? $data['post']->name : 'N/A' }}</td>
            </tr>
        </tbody>
    </table>
    <p>Thank you for submitting your form at {{ config('app.name') }}. Please login to app and make payment!</p>
</body>
</html>
