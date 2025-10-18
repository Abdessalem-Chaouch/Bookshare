<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>New Book Fetch Request</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="margin:0; padding:0; background:#f4f6f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color:#24303b;">

  <!-- Outer wrapper -->
  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f4f6f8; padding:24px 0;">
    <tr>
      <td align="center">

        <!-- Card -->
        <table role="presentation" cellpadding="0" cellspacing="0" width="640" style="max-width:640px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 6px 18px rgba(17,24,39,0.08);">
          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(90deg,#0d6efd,#6f42c1); padding:20px 24px; color:#ffffff;">
              <h1 style="margin:0; font-size:20px; line-height:1.15; font-weight:600;">New Book Fetch Request</h1>
              <p style="margin:6px 0 0; font-size:13px; opacity:0.95;">A customer submitted a request for your attention.</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:20px 24px;">
              <p style="margin:0 0 12px; font-size:15px; color:#111827;">
                Hi{{ isset($bookFetch->store) && $bookFetch->store->store_name ? ' ' . $bookFetch->store->store_name : '' }},<br>
                A user asked your store to look for a book — details below.
              </p>

              <!-- Details box -->
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #eef2f6; border-radius:6px; background:#fbfcfe;">
                <tr>
                  <td style="padding:14px 16px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                        <td style="vertical-align:top; padding-bottom:8px; width:28%; font-size:13px; color:#6b7280;">Requester</td>
                        <td style="vertical-align:top; padding-bottom:8px; font-weight:600; color:#0f1724;">{{ $bookFetch->email }}</td>
                      </tr>

                      <tr>
                        <td style="vertical-align:top; padding-bottom:8px; font-size:13px; color:#6b7280;">Book Title</td>
                        <td style="vertical-align:top; padding-bottom:8px; color:#0f1724;">{{ $bookFetch->title ?? 'Not specified' }}</td>
                      </tr>

                      <tr>
                        <td style="vertical-align:top; padding-bottom:8px; font-size:13px; color:#6b7280;">Author</td>
                        <td style="vertical-align:top; padding-bottom:8px; color:#0f1724;">{{ $bookFetch->author ?? 'Not specified' }}</td>
                      </tr>

                      <tr>
                        <td style="vertical-align:top; padding-bottom:8px; font-size:13px; color:#6b7280;">ISBN</td>
                        <td style="vertical-align:top; padding-bottom:8px; color:#0f1724;">{{ $bookFetch->isbn ?? 'Not specified' }}</td>
                      </tr>

                      <tr>
                        <td style="vertical-align:top; font-size:13px; color:#6b7280;">Specific edition?</td>
                        <td style="vertical-align:top; color:#0f1724;">{{ $bookFetch->specific_edition ? 'Yes' : 'No' }}</td>
                      </tr>

                      @if(isset($bookFetch->created_at))
                        <tr>
                          <td style="vertical-align:top; padding-top:8px; font-size:13px; color:#6b7280;">Requested</td>
                          <td style="vertical-align:top; padding-top:8px; color:#0f1724;">{{ $bookFetch->created_at->format('d M Y H:i') }}</td>
                        </tr>
                      @endif
                    </table>
                  </td>
                </tr>
              </table>

              <!-- CTA -->
              <div style="margin-top:18px; text-align:left;">
                <a href="{{ route('stores.show', $bookFetch->store_id) }}" style="display:inline-block; padding:10px 16px; background:#0d6efd; color:#ffffff; text-decoration:none; border-radius:6px; font-weight:600; font-size:14px;">
                  View Store & Respond
                </a>

                <!-- small note -->
                <p style="margin:12px 0 0; font-size:13px; color:#6b7280;">
                  Reply to the requester at <strong>{{ $bookFetch->email }}</strong> when you have an update.
                </p>
              </div>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f8fafc; padding:14px 24px; font-size:12px; color:#6b7280;">
              <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                  <strong>{{ config('app.name') }}</strong><br>
                  <span>We connect readers & stores</span>
                </div>
                <div style="text-align:right;">
                  <span style="display:block; font-size:11px; color:#9ca3af;">&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                </div>
              </div>
            </td>
          </tr>
        </table>
        <!-- /Card -->

      </td>
    </tr>
  </table>

</body>
</html>
