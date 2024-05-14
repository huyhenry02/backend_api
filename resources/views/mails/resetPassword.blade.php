<style>html,body { padding: 0; margin:0; }</style>
<div style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 auto; padding:0; max-width:600px">
        <tbody>
        <tr>
            <td align="left" valign="center">
                <div style="text-align:left; margin: 0 20px; padding: 40px; background-color:#ffffff; border-radius: 6px">
                    <!--begin:Email content-->
                    <div style="padding-bottom: 30px; font-size: 17px;">
                        <strong>Xin chào!</strong>
                    </div>
                    <div style="padding-bottom: 30px">Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Để tiến hành đặt lại mật khẩu, vui lòng nhấp vào nút bên dưới:</div>
                    <div style="padding-bottom: 40px; text-align:center;">
                        <a href="{{ $data['resetLink'] }}" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009ef7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle" target="_blank">Đặt lại mật khẩu</a>
                    </div>
                    <div style="padding-bottom: 30px">
                        Liên kết đặt lại mật khẩu này sẽ hết hạn sau 60 phút. Nếu bạn không yêu cầu đặt lại mật khẩu thì không cần thực hiện thêm hành động nào.</div>
                    <div style="border-bottom: 1px solid #eeeeee; margin: 15px 0"></div>
                    <div style="padding-bottom: 50px;">
                        <p style="margin-bottom: 10px;">
                            Nút không hoạt động? Hãy thử dán URL này vào trình duyệt của bạn:</p>
                        <a href="{{ $data['resetLink'] }}" rel="noopener" target="_blank" style="text-decoration:none;color: #009ef7">{{ $data['resetLink'] }}</a>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
