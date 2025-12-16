<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\OrderRepository;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;

class PrinterController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function printReceipt($order_id)
    {
        $ip = '192.168.18.200'; // Your printer IP
        $port = 9100;

        $order = $this->orderRepository->find($order_id);
        if (!$order) {
            return response()->json(['status' => false, 'error' => 'Order not found']);
        }

        $cart = $order->items;

        try {
            $connector = new NetworkPrintConnector($ip, $port);
            $printer = new Printer($connector);

            /* === LOGO (Optional Safe Version) === */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $logoPath = public_path('logoimage.png');
            // if (file_exists($logoPath)) {
            //     $logo = EscposImage::load($logoPath, false);
            //     $printer->bitImage($logo);
            //     $printer->feed();
            // }

            /* === HEADER === */
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("LOOP\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text('Order#: ' . $order->order_uid . "\n");
            $printer->text(str_repeat('-', 40) . "\n");

            /* === ORDER INFO === */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Invoice #: ' . $order->order_uid . "\n");
            $printer->text("Customer : Self Customer\n");
            $printer->text('Date     : ' . $order->created_at->format('d M Y  h:i A') . "\n");
            $printer->text('Server   : ' . ($order->user->name ?? 'N/A') . "\n");
            $printer->text('Type     : ' . strtoupper($order->order_type ?? 'Take Away') . "\n");
            $printer->text(str_repeat('-', 40) . "\n");

            /* === ITEM TABLE === */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-4s %-18s %6s %8s\n", 'Qty', 'Item', 'Rate', 'Amount'));
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', 40) . "\n");

            $subtotal = 0;
            foreach ($cart as $item) {
                $name = strtoupper($item->productVariant->product->name ?? 'N/A');
                $qty = $item->quantity ?? 1;
                $rate = number_format($item->price, 0);
                $amount = number_format($item->price * $qty, 0);
                $subtotal += ($item->price * $qty);

                $printer->text(sprintf("%-4s %-18s %6s %8s\n", $qty, substr($name, 0, 18), $rate, $amount));

                // Addons
                $addonDetail = getIngredientDetails($item->addon_id, true, $item->productVariant->sizes->id ?? null);
                if (!empty($addonDetail)) {
                    foreach ($addonDetail as $addon) {
                        $printer->text('     + ' . strtoupper($addon['name']) . "\n");
                    }
                }

                // Notes
                if (!empty($item->notes)) {
                    $printer->text('     📝 ' . $item->notes . "\n");
                }
            }

            $printer->text(str_repeat('-', 40) . "\n");

            /* === TOTALS === */
            $gst = $order->tax ?? ($subtotal * 0.15);
            $discount = $order->discount_amount ?? 0;
            $net = $order->final_amount ?? ($subtotal + $gst - $discount);

            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text(sprintf("%-25s %13.2f\n", 'SubTotal:', $subtotal));
            $printer->text(sprintf("%-25s %13.2f\n", 'GST (15%):', $gst));
            $printer->text(sprintf("%-25s %13.2f\n", 'Discount:', $discount));
            $printer->text(str_repeat('-', 40) . "\n");
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-25s %13.2f\n", 'Net Bill:', $net));
            $printer->setEmphasis(false);
            $printer->feed();

            /* === PAYMENT INFO === */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Cash Received : ' . number_format($net, 0) . "\n");
            $printer->text('Payment Mode  : ' . ucfirst($order->payment_mode ?? 'Cash') . "\n");
            $printer->feed();

            /* === FOOTER === */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("!!! FOR THE LOVE OF FOOD !!!\n");
            $printer->setEmphasis(false);
            $printer->text("Powered by: LOOP Technologies\n");
            $printer->text("+92 300 1234567 | www.loop.pk\n");
            $printer->feed(2);
            $printer->cut();
            $printer->close();

            return response()->json(['status' => true, 'message' => 'Receipt printed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
