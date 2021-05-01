<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title')</title>
  <style>
    @font-face {
      font-family: 'arialRegular';
      src: url("/fonts/arial/ARIAL_REGULAR.ttf") format('truetype');
      font-weight: normal;
      font-style: normal;
    }

    @font-face {
      font-family: 'arialBold';
      src: url("/fonts/arial/ARIAL_BOLD.ttf") format('truetype');
      font-weight: normal;
      font-style: bold;
    }

    @font-face {
      font-family: 'arialItalic';
      src: url("/fonts/arial/ARIAL_ITALIC.ttf") format('truetype');
      font-weight: normal;
      font-style: italic;
    }

    body {
      font-family: "arialRegular", "arialBold", "arialItalic";
      font-size: 10pt;
    }

    #tabel-ct-scan {
      border: 1px solid black;
      border-collapse: collapse;
      text-align: center;
    }

    table#tabel-ct-scan th {
      background-color: darkseagreen;
    }

    #watermark {
      position: fixed;
      bottom: 10cm;
      left: 3cm;
      width: 472px;
      height: 553px;
      opacity: 0.2;
      z-index: -1000;
    }

  </style>
</head>

<body marginwidth="0" marginheight="0">
  <div id="watermark">
    <img src="{{ $logo_watermark }}" height="100%" width="100%" />
  </div>
  <div style="margin-bottom: 20px; margin-top: 20px;">
    <img src="{{ $kop_surat }}" width="100%" alt="" srcset="">
  </div>
  @yield('content')
  @if ($validator)
  <table width="97%" style="margin-top: 12%">
    <tbody>
      <tr>
        <td width="30%"></td>
        <td width="25%"></td>
        <td>Bandung, {{ date('d M Y') }}</td>
      </tr>
      <tr>
        <td width="30%"></td>
        <td width="25%"></td>
        <td>PENANGGUNG JAWAB LAB COVID-19</td>
      </tr>
      <tr>
        <td colspan="3" style="padding-top: 65px;"></td>
      </tr>
      <tr>
        <td width="30%"></td>
        <td width="25%"></td>
        <td>
          <span style="text-decoration: underline">{{ $validator ? $validator->nama : ' -' }}</span>
        </td>
      </tr>
      <tr>
        <td width="30%"></td>
        <td width="25%"></td>
        <td>
          NIP. {{ $validator ? $validator->nip : ' -' }}
        </td>
      </tr>
    </tbody>
  </table>
  @endif
</body>

</html>
