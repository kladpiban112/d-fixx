<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// add Lib mPDF to projectr
require_once '../config.php';
require_once '../vendor/autoload.php';
require_once '../core/functions.php';

ob_start();
$datenow = date('Y-m-d');
$dtnow = date('Y-m-d H:i:s');

$orderid = filter_input(INPUT_GET, 'orderid', FILTER_SANITIZE_STRING);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="print-style.css">

    <style>

    </style>
    <title>ใบเสนอราคา/QUOTATION</title>
    <style>
    body {
        font-family: 'angsana';
        font-size: 16pt;
    }

    p {
        margin: 0pt;
    }

    table.items {
        border: 0.1mm solid #000000;
    }

    td {
        vertical-align: top;
    }

    .items td {
        border-left: 0.1mm solid #000000;
        border-right: 0.1mm solid #000000;
    }

    table thead td {
        background-color: #EEEEEE;
        text-align: center;
        border: 0.1mm solid #000000;
        font-variant: small-caps;
    }

    td.blanktotal {
        background-color: #EEEEEE;
        border: 0.1mm solid #000000;
        background-color: #FFFFFF;
        border: 0mm none #000000;
        border-top: 0.1mm solid #000000;
        border-right: 0.1mm solid #000000;
    }

    td.totals {
        text-align: right;
        border-bottom: 0.1mm solid #000000;
    }

    td.cost {
        text-align: "."center;
    }
    </style>

</head>

<body>
    <?php
    $sql = 'SELECT a.*,b.*,r.*,od.order_date,DATE_ADD(od.order_date, INTERVAL od.endorder_day DAY) valid_date,od.invoice quotation
            FROM '.DB_PREFIX."order_main a 
            left join order_main_detail od on a.order_id = od.order_id
            left join users b on a.add_users = b.user_id
            left join register r on r.id = b.register_id
            WHERE a.order_id = '$orderid'
            ";
//echo $sql;
$result = $conn->prepare($sql);
$result->execute();

$rs = $result->fetch(PDO::FETCH_ASSOC);
//echo $rs_count = count($conn->prepare ($sql));

$username = $rs['name'];
$adddate = $rs['add_date'];
$valid_date = $rs['valid_date'];
$orderid = $rs['order_id'];
$qo = $rs['quotation'];

$addr = isset($rs['company_addr_no']) ? ' เลขที่.'.$rs['company_addr_no'] : '';
$addr .= isset($rs['company_addr_road']) ? ' ถ.'.$rs['company_addr_road'] : '';
$addr .= isset($rs['company_addr_moo']) ? ' ม.'.$rs['company_addr_moo'] : '';
$addr .= isset($rs['company_addr_tambon']) ? ' ต.'.$rs['company_addr_tambon'] : '';
$addr .= isset($rs['company_addr_ampur']) ? ' อ.'.$rs['company_addr_ampur'] : '';
$addr .= isset($rs['company_addr_province']) ? ' จ.'.$rs['company_addr_province'] : '';

?>
    <!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
<td width="30%" style="color:#0000BB; ">
<span style="font-weight: bold; font-size: 20pt;"> ใบเสนอราคา <br/> QUOTATION </span>
</td>
<td width="20%" style="color:#0000BB; "><span style="font-weight: bold; font-size: 16pt;"> (ต้นฉบับ / Original) </span>
</td>
<td width="50%" style="color:#0000BB; ">&nbsp;
</td>

</tr>
</table>


</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
Page {PAGENO} of {nb}
</div>
<div style="font-size: 9pt; text-align: right; padding-top: 2mm; ">
จัดเตรียมโดย / Prepared by Goverlution.
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->


    <!-- <div style="text-align: right">Date: 13th November 2008</div> -->
    <table width="100%" cellpadding="0">

        <tr>
            <td width="22%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>ลูกค้า/</strong>Customer
            </td>

            <td width="48%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <?php echo $rs['company_major']; ?> <small><?php echo $rs['company_name']; ?></small>
            </td>

            <td width="22%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>เลขที่/</strong>No
            </td>

            <td width="18%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <?php echo 'QO-'.$qo; ?>
            </td>
        </tr>
        <tr>
            <td width="22%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>ที่อยู่/</strong>Address
            </td>

            <td width="48%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <?php echo $addr; ?>
            </td>

            <td width="22%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>วันที่ / </strong>Issue
            </td>

            <td width="18%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <?php echo date_sub_thai($adddate); ?>
            </td>
        </tr>
    </table>
    <table width="100%" cellpadding="0" style="border-bottom: 0.1mm solid #000000;">
        <tr>
            <td width="22%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>เลขผู้เสียภาษี/</strong>ATax ID
            </td>

            <td width="28%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                xxxxx
            </td>
            <td width="22%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                E:
            </td>

            <td width="22%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>ใช้ได้ถึง / </strong>Valid.
            </td>

            <td width="18%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <?php echo date_sub_thai($valid_date); ?>
            </td>
        </tr>
        <tr>
            <td width="22%" style="border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>ผู้ติดต่อ / </strong>Attention :
            </td>

            <td width="28%" style="border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                xxxxx
            </td>
            <td width="22%" style="border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                T: <?php echo $rs['contact_telephone']; ?>
            </td>
            <td width="22%" style="border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>อ้างอิง / </strong>Ref.
            </td>

            <td width="18%" style="border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                xxxx
            </td>
        </tr>
    </table>
    <table width="100%" cellpadding="0" style="border-bottom: 0mm solid #000000;">
        <tr>
            <td width="10%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>ผู้ออก</strong>
            </td>
            <td width="50%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>EZKAE Company Limited </strong>
            </td>

            <td width="20%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>เลขผู้เสียภาษี / </strong>Tax ID
            </td>
            <td width="25%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                -
            </td>
        </tr>
        <tr>
            <td width="10%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                issuer
            </td>
            <td width="50%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <span style="font-size: 14pt;">65/206 อาคาร/หมู่บ้าน ชำนาญเพ็ญชาติ ชั้น 24 <br />
                    ถนนพระราม 9 แขวงห้วยขวาง เขตห้วยขวาง<br />
                    กรุงเทพมหานคร 10320<br /></span>
            </td>

            <td width="20%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>Tel : </strong>026-430-744<br>
                <strong>W : -</strong>
            </td>
            <td width="25%"
                style="border:0px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; text-align:left;">
                <strong>Email : -</strong>
            </td>
        </tr>

    </table>

    <table width="100%"
        style="border:1px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; font-size: 14pt;">
        <thead>
            <tr>
                <td width="7%">
                    <strong>ลำดับที่ </strong><br> No.
                </td>
                <td width="15%">
                    <strong>รหัส </strong><br> ID No.
                </td>
                <td width="38%">
                    <strong>ชื่อสินค้า </strong><br> Product Name.
                </td>
                <td width="10%">
                    <strong>จำนวน </strong><br>Quantity
                </td>
                <td width="15%">
                    <strong>ราคาต่อหน่วย </strong><br>UNIT PRICE
                </td>
                <td width="7%">
                    <strong>ภาษี </strong><br>VAT
                </td>
                <td width="18%">
                    <strong>มูลค่าก่อนภาษี </strong><br>Pre-Tax Amount
                </td>
            </tr>
        </thead>
        <tbody>

            <!-- foreach ($order->lineItems as $line) or some such thing here -->
            <?php

                                                                               echo $sql = "SELECT o.order_id,o.order_session,od.oid AS od_id,od.quantity,od.productprice,od.productid proid,od.productname,omd.price_total,omd.discount,omd.grand_total,omd.vat,omd.total 
                                                                               FROM order_detail od 
                                                                               LEFT JOIN order_main o ON od.order_id = o.order_id
                                                                               LEFT JOIN order_main_detail omd ON od.order_id = omd.order_id
                                                                               WHERE o.order_id = '$orderid' AND o.flag != '99' AND od.flag = '1'
                                                                               ";
    $result = $conn->prepare($sql);
    $rs_count = count($conn->prepare($sql));
    $result->execute();
    $content = '';
    if ($rs_count > 0) {
        $i = 1;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $totalprice = $row['productprice'] * $row['quantity'];
            $grandtotal = $row['grand_total'];
            $discounttotal = $row['discount'];
            $vat = $row['vat'];
            $total = $row['total'];
            $pricetotal += $totalprice; ?>
            <tr style="border:1px #000;">
                <td>
                    <?php echo $i; ?></td>
                <td>
                    <?php echo getProductID($row['proid']); ?></td>
                <td>
                    <?php echo $row['productname']; ?></td>
                <td class="cost">
                    <?php echo $row['quantity']; ?></td>
                <td class="cost">
                    <?php echo $row['productprice']; ?></td>
                <td class="cost">
                    <?php echo '-'; ?></td>
                <td class="totals">
                    <?php echo number_format($totalprice, 2); ?></td>


            </tr>
            <?php
            ++$i;
        }
    }
        ?>
        </tbody>
    </table>
    <table width="100%"
        style="border:1px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; font-size: 12pt;">
        <tr>
            <td width="47%">&nbsp;</td>
            <td width="35%"
                style="border-bottom:0.1px solid #000000; border-collapse:collapse; cellpadding:0px; cellspacing:0px; font-size: 12pt;">
                ราคาสุทธิสินค้ายกเว้นภาษี/ VAT-Exempted Amount</td>
            <td class="totals" width="18%">
                <?php echo number_format($pricetotal, 2); ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="totals">ส่วนลด / Discount</td>
            <td class="totals">
                <?php echo number_format($discounttotal, 2); ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="totals">ภาษีมูลค่าเพิ่ม / Vat</td>
            <td class="totals">
                <?php echo number_format($vat, 2); ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="totals">จํานวนเงินรวมทั้งสิ้น / Grand Total</td>
            <td class="totals"><strong>
                    <?php echo number_format($grandtotal, 2); ?></strong></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2" class="cost"> (<strong><?php echo bahtText($grandtotal); ?></strong>)</td>
        </tr>
    </table>
    <table width="100%" cellpadding="5">
        <tr>
            <td width="50%" style="border: 0mm solid #888888; ">
                <span style="font-size: 12pt; font-weight: bold; color: #555555;">การชำระเงิน:</span><br />
                <span style="font-weight: bold; font-size: 12pt;">
                    <table width="100%" cellpadding="5"
                        style="font-size: 12pt; color: #555555; border-bottom: 0.1mm solid #000000;">
                        <tr>
                            <td>ธนาคาร</td>
                            <td>ชื่อบัญชี</td>
                            <td>เลขที่บัญชี</td>
                        </tr>
                    </table>
                </span>
            </td>
            <td width="25%" style="border: 0mm solid #888888; ">
                <center>
                    <span style="font-size: 12pt; font-weight: bold; color: #555555;">อนุมัติโดย/Approved by</span>
                    <br /><br /><br /><br /><span
                        style="font-size: 12pt; font-weight: bold; color: #555555;">............................................
                        <br />วันที่ / Date........................</span>
                </center>
            </td>

            <td width="25%" style="border: 0mm solid #888888;">
                <center>
                    <span style="font-size: 12pt; font-weight: bold; color: #555555;">ยอมรับใบเสนอราคา/Accepted
                        by</span>
                    <br /><br /><br /><br /><span
                        style="font-size: 12pt; font-weight: bold; color: #555555;">............................................
                        <br />วันที่ / Date........................</span>
                </center>
            </td>
        </tr>
    </table>



</body>

</html>

<?php

$html = ob_get_contents();
ob_end_clean();

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'angsana',
    'default_font_size' => 14,
    'tempDir' => __DIR__.'/tmp',
    'margin_left' => 20,
    'margin_right' => 15,
    'margin_top' => 30, //48
    'margin_bottom' => 25,
    'margin_header' => 10,
    'margin_footer' => 10,
     ]);
     $mpdf->SetProtection(['print']);
     $mpdf->SetTitle('Goverlution. - Quotation');
     $mpdf->SetAuthor('Goverlution.');
     $mpdf->SetWatermarkText('Quotation');
     $mpdf->showWatermarkText = true;
     $mpdf->watermark_font = 'DejaVuSansCondensed';
     $mpdf->watermarkTextAlpha = 0.1;

$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
$mpdf->Output();

?>