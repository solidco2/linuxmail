<?php
function linuxmail(array $to = [], $title = "", $bodyhtml = "", array $attaches = [], $fromname = "", $fromaddr = "noreply@baidu.com", array $moreheaders = []) {
        $splitor = "\r\n";

        $headers = [];
        foreach ($moreheaders as $name=>$value) {
                $headers[$name] = $value;
        }

        $boundary = "----PART_" . uniqid() . "_BOUNDARY";
        $content_boundary = "----PART_" . uniqid() . "_CONTENT_BOUNDARY";

        if ($fromname) {
                $headers["From"] = '=?UTF-8?B?' . base64_encode($fromname) . '?=' . " <$fromaddr>";
        }
        $headers["Content-Type"] = "multipart/mixed;$splitor    boundary=\"$boundary\"";
        $headers["MIME-Version"] = "1.0";
        $headers["X-ABC"] = "D";

        $headerlines = [];

        foreach ($headers as $name=>$value) {
                $headerlines []= "$name: $value";
        }


        $subject = "=?UTF-8?B?".base64_encode($title)."?=";

        $bodyhtml = quoted_printable_encode($bodyhtml);
        $body = "Content-Type: multipart/alternative;$splitor    boundary=\"$content_boundary\"$splitor$splitor--$content_boundary${splitor}Content-Type: text/html;$splitor    charset=utf-8${splitor}Content-Transfer-Encoding: quoted-printable$splitor$splitor$bodyhtml";

        $contents []= "$body$splitor--$content_boundary--$splitor";

        foreach ($attaches as $atta) {
                $fn = "=?UTF-8?B?" . base64_encode($atta['filename']) . "?=";
                $sec = "";
                $sec .= "Content-Type: $atta[mime]; charset=utf-8; name=\"$fn\"" . $splitor;
                $sec .= "Content-Transfer-Encoding: base64" . $splitor;
                $sec .= "Content-Disposition: attachment; filename=\"$fn\"" . $splitor;
                $sec .= "$splitor";
                $sec .= chunk_split(base64_encode($atta['content']));
                $contents []= $sec;
        }

        $content = "";
        foreach ($contents as $section) {
                $content .= "--$boundary$splitor$section$splitor";
        }
        $content .= "--$boundary--$splitor$splitor";

        if ($to) {
                mail(
                        implode(', ', $to),
                        $subject,
                        $content,
                        implode("$splitor", $headerlines) . $splitor
                );
        }
}
function linuxmail_attach($content, $mime = "text/plain", $filename = "") {
        $res = [
                "content" => $content,
                "mime" => $mime,
                "filename" => $filename,
        ];
        return $res;
}

$csv = "
        1, 2, 3
        4, 5, 6
";

linuxmail(
        [ "solidco2@qq.com" ],
        "测试邮件：中文哒",
        "<table>
        <td>
                <th>
                        hello
                </th>
                <td title=1234> 呵呵 </td>
        </td>
</table>",
        [
                linuxmail_attach("$csv", "text/csv", "呵呵111.csv"),
                linuxmail_attach("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa", "text/csv", "hehe.csv"),
        ],
        "无回复"
);
