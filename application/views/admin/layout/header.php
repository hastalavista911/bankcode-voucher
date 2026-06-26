<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? esc($title) . ' — BankCode' : 'BankCode Admin' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root { --sidebar-bg: #1e293b; --sidebar-width: 240px; }
    body  { background: #f1f5f9; font-size: .9rem; }

    #sidebar {
      position: fixed; top: 0; left: 0; bottom: 0;
      width: var(--sidebar-width);
      background: var(--sidebar-bg);
      z-index: 100;
      overflow-y: auto;
    }
    #sidebar .brand {
      color: #fff; font-weight: 700; font-size: 1.1rem;
      padding: 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,.1);
    }
    #sidebar .nav-link {
      color: #94a3b8; padding: .55rem 1rem;
      border-radius: .375rem; margin: .1rem .5rem;
    }
    #sidebar .nav-link:hover, #sidebar .nav-link.active {
      color: #fff; background: rgba(255,255,255,.1);
    }
    #sidebar .nav-link i { margin-right: .5rem; }

    #main { margin-left: var(--sidebar-width); min-height: 100vh; }
    .topbar {
      background: #fff; padding: .75rem 1.5rem;
      border-bottom: 1px solid #e2e8f0;
      display: flex; align-items: center; justify-content: space-between;
    }
    .content { padding: 1.5rem; }
  </style>
</head>
<body>
