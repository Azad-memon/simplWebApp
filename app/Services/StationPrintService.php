<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Station;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class StationPrintService
{
    /**
     * Generate printable sticker for each station based on order items.
     */
    public function printOrderItems($order)
    {
        // Only print for active or preparing orders
        if (! in_array($order->status, ['active', 'processing'])) {
            return;
        }

        // Get unique categories
            $categoryIds = $order->items
            ->map(function ($item) {
                return optional($item->productVariant->product)->cat_id;
            })
            ->filter()       // remove nulls
            ->unique();      // unique categories only
$stationsArray = [];

foreach ($categoryIds as $categoryId) {

    // Get stations linked to this category
    $stations = Station::whereHas('categories', function ($q) use ($categoryId) {
        $q->where('categories.id', $categoryId);
    })->get();

    // Filter items for this category
    $itemsToPrint = $order->items
        ->load(['productVariant.sizes', 'productVariant.product'])
        ->filter(function ($i) use ($categoryId) {
            return optional($i->productVariant->product)->cat_id == $categoryId;
        })
        ->map(function ($item) {
            $variant = $item->productVariant;
            $sizeId = $variant->sizes->id ?? null;
            ;

            // Convert addon_json to array
            $addonArray = json_decode($item->addon_json ?? '[]', true) ?? [];

            // Fetch addon details using helper
            $addonDetail = getIngredientDetails($item->addon_id, true, $sizeId);

            // Match prices
            foreach ($addonDetail as &$a) {
                $match = collect($addonArray)->first(function ($x) use ($a) {
                    return intval($x['ing_id']) == intval($a['id']);
                });
                $a['price'] = $match['price'] ?? 0;
            }

            $item->addon_details = $addonDetail;
            $item->cat_name = optional($item->productVariant->product->category)->name ?? '';
            return $item;
        });
// print_r($itemsToPrint);
    // Loop through stations for this category
    foreach ($stations as $station) {
        $stationsArray[] = [
            'category_id' => $categoryId,
            'station' => $station,
            'items' => $itemsToPrint,
            'order' => $order,
            'ip' => $station->ip,
        ];
    }
}
return $stationsArray;
        // foreach ($categoryIds as $categoryId) {

        //     // Get stations linked to this category
        //     $stations = Station::whereHas('categories', function ($q) use ($categoryId) {
        //         $q->where('categories.id', $categoryId);
        //     })->get();

        //     // Filter items for this category
        //    $itemsToPrint = $order->items
        //     ->load(['productVariant.sizes', 'productVariant.product'])
        //     ->filter(function ($i) use ($categoryId) {
        //         return optional($i->productVariant->product)->cat_id == $categoryId;
        //     });

//             foreach ($stations as $station) {
//                 // Print only once per station per category
//                // dump($itemsToPrint);
//                $itemsToPrint = $itemsToPrint->map(function ($item) {
//                 $variant = $item->productVariant;
//                 $sizeId = $variant->sizes->id ?? null;

//                 // Convert addon_json to array
//                 $addonArray = json_decode($item->addon_json ?? '[]', true) ?? [];

//                 // Fetch addon details using helper
//                 $addonDetail = getIngredientDetails($item->addon_id, true, $sizeId);

//                 // Match prices (if needed)
//                 foreach ($addonDetail as &$a) {
//                     $match = collect($addonArray)->first(function ($x) use ($a) {
//                         return intval($x['ing_id']) == intval($a['id']);
//                     });

//                     $a['price'] = $match['price'] ?? 0;
//                 }

//                 // ATTACH ADDONS TO ITEM OBJECT
//                 $item->addon_details = $addonDetail;  // <-- This is what Core PHP will receive

//                 return $item;
//             });
//                // dump( $itemsToPrint);
//              $response = Http::get('http://localhost/PrinEscpos/KotBill.php', [
//                 'station' => json_encode($station)
//                 ,'order' => json_encode( $order),
//                 'items' => json_encode( $itemsToPrint),
//                 "ip" => $station->ip
//             ]);
//                 // $this->printKitchenReceipt($station, $station->ip, 9100, $order, $itemsToPrint);
// print_r( "<pre>".$response->body());

//                 // $this->printKitchenReceiptTest($station, $station->ip, 9100, $order, $itemsToPrint);
//             }
// $stationsData = $stations->map(function ($station) use ($order, $items) {

//     $itemsToPrint = $items->map(function ($item) {
//         $variant = $item->productVariant;
//         $sizeId = $variant->sizes->id ?? null;

//         // Convert addon_json to array
//         $addonArray = json_decode($item->addon_json ?? '[]', true) ?? [];

//         // Fetch addon details using helper
//         $addonDetail = getIngredientDetails($item->addon_id, true, $sizeId);

//         // Match prices
//         foreach ($addonDetail as &$a) {
//             $match = collect($addonArray)->first(function ($x) use ($a) {
//                 return intval($x['ing_id']) == intval($a['id']);
//             });

//             $a['price'] = $match['price'] ?? 0;
//         }

//         $item->addon_details = $addonDetail;

//         return $item;
//     });

//     return [
//         'station' => $station,
//         'items'   => $itemsToPrint,
//         'order'   => $order,
//         'ip'      => $station->ip
//     ];
// });
// return $stationsData;


//         }

    }

    public function printKitchenReceiptTest($station, $ip, $port, $order, $itemsToPrint)
    {
        $receiptText = '';

        // HEADER
        $stationName = strtoupper($station->s_name ?? 'UNKNOWN STATION');
        $receiptText .= str_repeat('=', 40)."\n";
        $receiptText .= "STATION: {$stationName}\n";
        $receiptText .= str_repeat('=', 40)."\n\n";
        $receiptText .= "KITCHEN ORDER\n";
        $receiptText .= str_repeat('=', 40)."\n";

        // BASIC INFO
        $receiptText .= 'Order#: '.$order->order_uid."\n";
        $receiptText .= 'Date   : '.$order->created_at->format('d M Y  h:i A')."\n";
        $receiptText .= 'Cashier: '.($order->staff->first_name ?? '').' '.($order->staff->last_name ?? 'N/A')."\n";
        $receiptText .= 'Type   : '.strtoupper($order->order_type_label ?? 'Take Away')."\n";
        $receiptText .= str_repeat('-', 40)."\n";

        // ITEMS
        $receiptText .= sprintf("%-4s %-24s %-8s\n", 'Qty', 'Item', 'Size');
        $receiptText .= str_repeat('-', 40)."\n";

        foreach ($itemsToPrint as $item) {
            $name = strtoupper($item->productVariant->product->name ?? 'N/A');
            $qty = $item->quantity ?? 1;
            $size = strtoupper($item->productVariant->sizes->name ?? '-');
            $receiptText .= sprintf("%-4s %-24s %-8s\n", $qty, substr($name, 0, 24), $size);

            // Addons
            $addonDetail = getIngredientDetails($item->addon_id, true, $item->productVariant->sizes->id ?? null);
            if (! empty($addonDetail)) {
                foreach ($addonDetail as $addon) {
                    $receiptText .= '     + '.strtoupper($addon['name'])."\n";
                }
            }

            // Notes
            if (! empty($item->notes)) {
                $receiptText .= '     📝 '.$item->notes."\n";
            }

            $receiptText .= str_repeat('-', 40)."\n";
        }

        // FOOTER
        $receiptText .= "*** KITCHEN COPY ***\n";
        $receiptText .= "\n\n\n"; // paper feed

        // SEND TO NODE.JS PRINT AGENT
        try {
            $orderData = [
                'printerIP' => $ip,
                'printerPort' => $port,
                'header' => 'KITCHEN ORDER',
                'station' => $station->s_name,
                'order' => [
                    'id' => $order->order_uid,
                    'date' => $order->created_at->format('d M Y h:i A'),
                    'cashier' => $order->staff->first_name.' '.$order->staff->last_name,
                    'type' => $order->order_type_label,
                ],
                'items' => $itemsToPrint->map(function ($item) {
                    return [
                        'name' => $item->productVariant->product->name ?? 'N/A',
                        'qty' => $item->quantity ?? 1,
                        'size' => $item->productVariant->sizes->name ?? '-',
                        'notes' => $item->notes,
                    ];
                })->toArray(),
            ];
            $response = Http::post('http://localhost:4000/print-kitchen', $orderData);

            // $response = Http::post('http://localhost:4000/print-kitchen', [
            //     'printerIP' => $ip,
            //     'printerPort' => $port,
            //     'receiptText' => $receiptText,
            // ]);

            return $response->json();
        } catch (\Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    public static function getPublicIp()
    {
        try {
            // Using an external service to get public IP
            $response = Http::timeout(3)->get('https://api.ipify.org?format=json');
            if ($response->successful()) {
                return $response->json()['ip'] ?? null;
            }
        } catch (\Exception $e) {
            // Log error if needed
            \Log::error('Failed to get public IP: '.$e->getMessage());
        }

        return null;
    }

    public function printKitchenReceipt($station, $ip, $port, $order, $itemsToPrint)
    {

        // dd($ip);
        // dd($station->s_name);
        if (! $order || $itemsToPrint->isEmpty()) {
            return response()->json(['status' => false, 'error' => 'No items to print for this station.']);
        }

        try {
            $connector = new NetworkPrintConnector($ip, $port);
            $printer = new Printer($connector);

        /* === LOGO === */
    $logoPath = public_path('NEEEE.png'); // change to your logo
    $tmpLogo = sys_get_temp_dir() . '/loop_logo_bw.png';
    $imageData = @file_get_contents($logoPath);
    if ($imageData !== false) {
        $src = @imagecreatefromstring($imageData);
        if ($src) {
            $maxWidth = 280;
            $ratio = imagesx($src) / imagesy($src);
            $newHeight = intval($maxWidth / $ratio);
            $resized = imagecreatetruecolor($maxWidth, $newHeight);

            imagecopyresampled($resized, $src, 0, 0, 0, 0, $maxWidth, $newHeight, imagesx($src), imagesy($src));
            imagefilter($resized, IMG_FILTER_GRAYSCALE);
            imagefilter($resized, IMG_FILTER_CONTRAST, 25);
            imagefilter($resized, IMG_FILTER_BRIGHTNESS, 25);

            imagepng($resized, $tmpLogo);
            imagedestroy($src);
            imagedestroy($resized);

            $logo = EscposImage::load($tmpLogo, false);
            if ($logo) {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->bitImage($logo);
                $printer->feed();
            }
        }
    }

    /* === HEADER === */
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->setTextSize(2, 2);

    $stationName = strtoupper($station->s_name ?? 'UNKNOWN STATION');
    $printer->text("STATION: {$stationName}\n");
    $printer->text(str_repeat('=', 40) . "\n\n");

        $queueNumber = $order->queue_number ?? '';

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setReverseColors(true);
        $printer->text(" $queueNumber \n");
        $printer->setReverseColors(false);
       $printer->text(str_repeat('=', 40) . "\n");



    $printer->text("KITCHEN ORDER\n");
    $printer->setTextSize(1, 1);
    $printer->setEmphasis(false);
    $printer->text(str_repeat('=', 40) . "\n");

    /* === BASIC INFO === */
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text('Order#: ' . $order->order_uid . "\n");
    $printer->text('Date   : ' . $order->created_at->format('d M Y  h:i A') . "\n");
    $printer->text('Cashier : ' . (($order->staff->first_name ?? '') . ' ' . ($order->staff->last_name ?? 'N/A')) . "\n");
    $printer->text('Type   : ' . strtoupper($order->order_type_label ?? 'Take Away') . "\n");

    /* === ORDER NOTE === */
    if (!empty($order->note)) {
        $printer->setEmphasis(true);
        $printer->text("📝 Order Note:\n");
        $printer->setEmphasis(false);
        $printer->text(wordwrap($order->note, 38, "\n") . "\n");
    }

    $printer->text(str_repeat('-', 40) . "\n");

    /* === ITEMS === */
    $printer->setEmphasis(true);
    $printer->text(sprintf("%-4s %-24s %-8s\n", 'Qty', 'Item', 'Size'));
    $printer->setEmphasis(false);
    $printer->text(str_repeat('-', 40) . "\n");

    foreach ($itemsToPrint as $item) {
        $name = strtoupper($item->productVariant->product->name ?? 'N/A');
        $qty = $item->quantity ?? 1;
        $size = strtoupper($item->productVariant->sizes->name ?? '-');

        $printer->text(sprintf("%-4s %-24s %-8s\n", $qty, substr($name, 0, 24), $size));

        /* === ADDONS === */
        $sizeid = $item->productVariant->sizes->id ?? null;
        $addonDetail = getIngredientDetails($item->addon_id, true, $sizeid);
        $addonArray = json_decode($item->addon_json ?? '[]', true) ?? [];
        if (!empty($addonDetail)) {
            foreach ($addonDetail as $addon) {
                $match = collect($addonArray)->first(function($a) use ($addon) {
                    return intval($a['ing_id']) === intval($addon['id']);
                });
                $price = $match['price'] ?? 0;

                $printer->text('     + ' . strtoupper($addon['name']) ."\n");
            }
        }

        /* === ITEM NOTES === */
        if (!empty($item->notes)) {
           $printer->text('  * ' . wordwrap($item->notes, 38, "\n  ") . "\n");
        }

        $printer->text(str_repeat('-', 40) . "\n");
    }

    /* === FOOTER === */
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->text("*** KITCHEN COPY ***\n");
    $printer->setEmphasis(false);
    $printer->feed(2);
    $printer->cut();
    $printer->close();
            echo "✅ Print sent successfully to {$ip}\n";
        } catch (\Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }

    }

    public function printReceipt($order, $ip)
    {

        $port = 9100;
        //         $subtotal = 0;
        //     $addonSubtotal_all = 0;

        //       foreach ($order->items as $item) {
        //         $name = strtoupper($item->productVariant->product->name ?? 'N/A');
        //         $qty = $item->quantity ?? 1;
        //         $rate = number_format($item->price, 0);
        //         $amount = number_format($item->price * $qty, 0);
        //        // $subtotal += $item->price * $qty;

        //         // Addons
        //         $sizeid = $item->productVariant->sizes->id ?? null;
        //         $addonDetail =  getIngredientDetails($item->addon_id, true ,$sizeid);
        //         $addonArray = json_decode($item->addon_id, true) ?? [];
        //         dump( $addonArray);
        //         $addonSubtotal = 0;
        // foreach ($addonDetail as $addon) {
        //             $price = 0;

        //             // safer matching
        //            $match = collect($addonArray)->firstWhere(
        //                                                         'ing_id',
        //                                                         $addon['id'],
        //                                                     );
        //             dump($match );
        //              $price = $match['price'] ?? 0;
        //             // calculate subtotal
        //            // $addonSubtotal += $price * $qty;

        //             // print addon
        //            // $printer->text('       + '.wordwrap($addon['name'], 22).' Rs '.number_format($price, 2)."\n");
        //         }

        //     }
        // exit;

        try {
            //\Log::info('Printing order: '.$order->order_uid);
            $connector = new NetworkPrintConnector($ip, $port);
            $printer = new Printer($connector);

            /* === LOGO SECTION === */
            // === LOGO ===
            $logoPath = public_path('NEEEE.png');
            $tmpLogo = sys_get_temp_dir().'/loop_logo_bw.png';

            $imageData = @file_get_contents($logoPath);
            if ($imageData === false) {
                throw new Exception("Failed to load logo from $logoPath");
            }

            $src = @imagecreatefromstring($imageData);
            if (! $src) {
                throw new Exception('Invalid logo image file.');
            }

            // Resize logo for 80mm receipt (~280px width)
            $maxWidth = 280;
            $ratio = imagesx($src) / imagesy($src);
            $newHeight = intval($maxWidth / $ratio);
            $resized = imagecreatetruecolor($maxWidth, $newHeight);

            imagecopyresampled($resized, $src, 0, 0, 0, 0, $maxWidth, $newHeight, imagesx($src), imagesy($src));
            imagefilter($resized, IMG_FILTER_GRAYSCALE);
            imagefilter($resized, IMG_FILTER_CONTRAST, 25);
            imagefilter($resized, IMG_FILTER_BRIGHTNESS, 25);

            imagepng($resized, $tmpLogo);
            imagedestroy($src);
            imagedestroy($resized);

            $logo = @EscposImage::load($tmpLogo, false);
            if ($logo) {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->bitImage($logo);
            }

            // === RECEIPT TITLE ===
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("ORDER RECEIPT\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text(str_repeat('=', 40)."\n");

            // === BUSINESS INFO ===
            $printer->setTextSize(1, 1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("LOOP RESTAURANT\n");
            $printer->text($order->branch->address."\n");
            $printer->text('Ph: '.$order->branch->phone."\n");
            $printer->text(str_repeat('-', 40)."\n");

            // === SALE RECEIPT HEADER ===
            $queueNumber = $order->queue_number ?? 0;

            $printer->setJustification(Printer::JUSTIFY_CENTER);

            // 🔥 Double size + bold for strong emphasis
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer->setEmphasis(true);

            // Build dynamic width based on queue number length
            $len = strlen($queueNumber);
            $width = $len + 6; // padding + borders

            $top    = "┌" . str_repeat("─", $width) . "┐\n";
            $middle = "│   {$queueNumber}   │\n";
            $bottom = "└" . str_repeat("─", $width) . "┘\n";

            // Print box
            $printer->text($top);
            $printer->text($middle);
            $printer->text($bottom);

            // Reset styling
            $printer->selectPrintMode();
            $printer->setEmphasis(false);
            $printer->setJustification();

            // 2️⃣ SALE RECEIPT header below queue number
            $printer->setReverseColors(true);      // Background reverse
            $printer->setEmphasis(true);           // Bold
            $printer->text(str_pad("SALE RECEIPT", 48, ' ', STR_PAD_BOTH)."\n");
            $printer->setReverseColors(false);
            $printer->setEmphasis(false);


                // Divider line
                $printer->text(str_repeat('=', 48) . "\n");

            // === ORDER DETAILS ===
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Order#: '.($order->order_uid ?? 'N/A')."\n");
            $printer->text('Date: '.$order->created_at->format('d M Y h:i A')."\n");
            $printer->text('Cashier: '.(($order->staff->first_name ?? '').' '.($order->staff->last_name ?? ''))."\n");
            $printer->text('Type: '.strtoupper($order->order_type_label ?? 'Take Away')."\n");
            if (! empty($order->customer_name)) {
                $printer->text('Customer: '.substr($order->customer_name, 0, 25)."\n");
            }
            $printer->text(str_repeat('-', 40)."\n");

            // === ITEMS + ADDONS + REMOVED ===
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-4s %-22s %6s %8s\n", 'Qty', 'Item', 'Rate', 'Total'));
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', 40)."\n");

            $subtotal = 0;
            $addonSubtotal_all = 0;

            foreach ($order->items as $item) {

            $category = $item->productVariant->product->category->name ?? 'N/A';
            $product  = $item->productVariant->product->name ?? 'N/A';

            $circle = "(" . $category . ")";
            $name = strtoupper($product) . " - " . strtoupper($circle);

            $qty = $item->quantity ?? 1;
            $rate = number_format($item->price, 0);
            $amount = number_format($item->price * $qty, 0);
            $subtotal += $item->price * $qty;

            // Main item line
            $printer->text(
                sprintf("%-4s %-22s %6s %8s\n",
                    $qty,
                    substr($name, 0, 22),
                    $rate,
                    $amount
                )
            );

            // 🔥 Item Note (FULL, NO CUT)
            if (!empty($item->notes)) {
                $printer->text("      " . wordwrap(strtoupper($item->notes), 32, "\n      ", true) . "\n");
            }

            // Addons
            $sizeid = $item->productVariant->sizes->id ?? null;
            $addonDetail = getIngredientDetails($item->addon_id, true, $sizeid);
            $addonArray = json_decode($item->addon_id, true) ?? [];
            $addonSubtotal = 0;

            foreach ($addonDetail as $addon) {

                $match = collect($addonArray)->firstWhere('ing_id', $addon['id']);
                $price = $match['price'] ?? 0;

                $addonSubtotal += $price * $qty;

                // addon print
                $printer->text('       + '.wordwrap($addon['name'], 22).' Rs '.number_format($price, 2)."\n");
            }

            $addonSubtotal_all += $addonSubtotal;

            $printer->text(str_repeat('-', 40)."\n");
        }


            // === TOTAL SECTION ===
            $totalBeforeTax = $subtotal + $addonSubtotal_all;
            $gst = $order->tax ?? ($totalBeforeTax * 0.15);
            $discount =  number_format($order->discount ?? 0, 2) ?? 0;
            $net = $order->final_amount ?? ($totalBeforeTax + $gst - $discount);


                // 🔥 Add Order Note BEFORE totals
                if (!empty($order->order_note)) {
                    $printer->text("\nORDER NOTE:\n");
                    $printer->text(wordwrap(strtoupper($order->order_note), 40, "\n") . "\n");
                }

            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->setReverseColors(true);
            $printer->setEmphasis(true);
            $printer->text(" SUMMARY \n");
            $printer->setReverseColors(false);
            $printer->setEmphasis(false);

            $printer->text(sprintf("%-25s %13.2f\n", 'Items Subtotal:', $subtotal));
            $printer->text(sprintf("%-25s %13.2f\n", 'Addons Total:', $addonSubtotal_all));
            $printer->text(sprintf("%-25s %13.2f\n", 'GST ('.($order->tax_percent ?? 0).'%):', $gst));
            $printer->text(sprintf("%-25s %13.2f\n", 'Discount:', $discount));

            $printer->setEmphasis(true);
            $printer->text(sprintf("%-25s %13.2f\n", 'Net Total:', $net));
            $printer->setEmphasis(false);

            // 🔥 Change / Return Amount (NEW)
            $changeReturn = $order->change_return ?? 0;
            $printer->text(sprintf("%-25s %13.2f\n", 'Change Return:', $changeReturn));

            $printer->feed();
            // === PAYMENT INFO ===
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text('Payment Mode: '.ucfirst($order->payment->payment_method??'N/A')."\n");
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', 40)."\n");

            // === SUMMARY SECTION ===
            $totalItems = count($order->items);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for dining with us!\n");
            $printer->text(str_repeat('=', 40)."\n");

            // === FOOTER ===
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("!!! FOR THE LOVE OF FOOD !!!\n");
            $printer->text("Powered by DoodlenDash\n");
            $printer->text("https://doodlendash.com\n");
            $printer->feed(2);

            $printer->cut();
            $printer->close();

            return ['status' => true, 'message' => "Receipt printed to {$ip}:{$port}"];

        } catch (\Exception $e) {
            Log::error("Printer connection failed ({$ip}): ".$e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
        public function printReceiptLocal($order, $ip)
    {

        $port = 9100;


        try {
            //\Log::info('Printing order: '.$order->order_uid);
            $connector = new NetworkPrintConnector($ip, $port);
            $printer = new Printer($connector);

            /* === LOGO SECTION === */
            // === LOGO ===
            $logoPath = public_path('NEEEE.png');
            $tmpLogo = sys_get_temp_dir().'/loop_logo_bw.png';

            $imageData = @file_get_contents($logoPath);
            if ($imageData === false) {
                throw new Exception("Failed to load logo from $logoPath");
            }

            $src = @imagecreatefromstring($imageData);
            if (! $src) {
                throw new Exception('Invalid logo image file.');
            }

            // Resize logo for 80mm receipt (~280px width)
            $maxWidth = 280;
            $ratio = imagesx($src) / imagesy($src);
            $newHeight = intval($maxWidth / $ratio);
            $resized = imagecreatetruecolor($maxWidth, $newHeight);

            imagecopyresampled($resized, $src, 0, 0, 0, 0, $maxWidth, $newHeight, imagesx($src), imagesy($src));
            imagefilter($resized, IMG_FILTER_GRAYSCALE);
            imagefilter($resized, IMG_FILTER_CONTRAST, 25);
            imagefilter($resized, IMG_FILTER_BRIGHTNESS, 25);

            imagepng($resized, $tmpLogo);
            imagedestroy($src);
            imagedestroy($resized);

            $logo = @EscposImage::load($tmpLogo, false);
            if ($logo) {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->bitImage($logo);
            }

            // === RECEIPT TITLE ===
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("ORDER RECEIPT\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text(str_repeat('=', 40)."\n");

            // === BUSINESS INFO ===
            $printer->setTextSize(1, 1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("LOOP RESTAURANT\n");
            $printer->text($order->branch->address."\n");
            $printer->text('Ph: '.$order->branch->phone."\n");
            $printer->text(str_repeat('-', 40)."\n");

            // === SALE RECEIPT HEADER ===
            $queueNumber = $order->queue_number ?? 0;

            $printer->setJustification(Printer::JUSTIFY_CENTER);

            // 🔥 Double size + bold for strong emphasis
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer->setEmphasis(true);

            // Build dynamic width based on queue number length
            $len = strlen($queueNumber);
            $width = $len + 6; // padding + borders

            $top    = "┌" . str_repeat("─", $width) . "┐\n";
            $middle = "│   {$queueNumber}   │\n";
            $bottom = "└" . str_repeat("─", $width) . "┘\n";

            // Print box
            $printer->text($top);
            $printer->text($middle);
            $printer->text($bottom);

            // Reset styling
            $printer->selectPrintMode();
            $printer->setEmphasis(false);
            $printer->setJustification();

            // 2️⃣ SALE RECEIPT header below queue number
            $printer->setReverseColors(true);      // Background reverse
            $printer->setEmphasis(true);           // Bold
            $printer->text(str_pad("SALE RECEIPT", 48, ' ', STR_PAD_BOTH)."\n");
            $printer->setReverseColors(false);
            $printer->setEmphasis(false);


                // Divider line
                $printer->text(str_repeat('=', 48) . "\n");

            // === ORDER DETAILS ===
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Order#: '.($order->order_uid ?? 'N/A')."\n");
            $printer->text('Date: '.$order->created_at->format('d M Y h:i A')."\n");
            $printer->text('Cashier: '.(($order->staff->first_name ?? '').' '.($order->staff->last_name ?? ''))."\n");
            $printer->text('Type: '.strtoupper($order->order_type_label ?? 'Take Away')."\n");
            if (! empty($order->customer_name)) {
                $printer->text('Customer: '.substr($order->customer_name, 0, 25)."\n");
            }
            $printer->text(str_repeat('-', 40)."\n");

            // === ITEMS + ADDONS + REMOVED ===
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-4s %-22s %6s %8s\n", 'Qty', 'Item', 'Rate', 'Total'));
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', 40)."\n");

            $subtotal = 0;
            $addonSubtotal_all = 0;

            foreach ($order->items as $item) {

            $category = $item->productVariant->product->category->name ?? 'N/A';
            $product  = $item->productVariant->product->name ?? 'N/A';

            $circle = "(" . $category . ")";
            $name = strtoupper($product) . " - " . strtoupper($circle);

            $qty = $item->quantity ?? 1;
            $rate = number_format($item->price, 0);
            $amount = number_format($item->price * $qty, 0);
            $subtotal += $item->price * $qty;

            // Main item line
            $printer->text(
                sprintf("%-4s %-22s %6s %8s\n",
                    $qty,
                    substr($name, 0, 22),
                    $rate,
                    $amount
                )
            );

            // 🔥 Item Note (FULL, NO CUT)
            if (!empty($item->notes)) {
                $printer->text("      " . wordwrap(strtoupper($item->notes), 32, "\n      ", true) . "\n");
            }

            // Addons
            $sizeid = $item->productVariant->sizes->id ?? null;
            $addonDetail = getIngredientDetails($item->addon_id, true, $sizeid);
            $addonArray = json_decode($item->addon_id, true) ?? [];
            $addonSubtotal = 0;

            foreach ($addonDetail as $addon) {

                $match = collect($addonArray)->firstWhere('ing_id', $addon['id']);
                $price = $match['price'] ?? 0;

                $addonSubtotal += $price * $qty;

                // addon print
                $printer->text('       + '.wordwrap($addon['name'], 22).' Rs '.number_format($price, 2)."\n");
            }

            $addonSubtotal_all += $addonSubtotal;

            $printer->text(str_repeat('-', 40)."\n");
        }


            // === TOTAL SECTION ===
            $totalBeforeTax = $subtotal + $addonSubtotal_all;
            $gst = $order->tax ?? ($totalBeforeTax * 0.15);
            $discount =  number_format($order->discount ?? 0, 2) ?? 0;
            $net = $order->final_amount ?? ($totalBeforeTax + $gst - $discount);


                // 🔥 Add Order Note BEFORE totals
                if (!empty($order->order_note)) {
                    $printer->text("\nORDER NOTE:\n");
                    $printer->text(wordwrap(strtoupper($order->order_note), 40, "\n") . "\n");
                }

            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->setReverseColors(true);
            $printer->setEmphasis(true);
            $printer->text(" SUMMARY \n");
            $printer->setReverseColors(false);
            $printer->setEmphasis(false);

            $printer->text(sprintf("%-25s %13.2f\n", 'Items Subtotal:', $subtotal));
            $printer->text(sprintf("%-25s %13.2f\n", 'Addons Total:', $addonSubtotal_all));
            $printer->text(sprintf("%-25s %13.2f\n", 'GST ('.($order->tax_percent ?? 0).'%):', $gst));
            $printer->text(sprintf("%-25s %13.2f\n", 'Discount:', $discount));

            $printer->setEmphasis(true);
            $printer->text(sprintf("%-25s %13.2f\n", 'Net Total:', $net));
            $printer->setEmphasis(false);

            // 🔥 Change / Return Amount (NEW)
            $changeReturn = $order->change_return ?? 0;
            $printer->text(sprintf("%-25s %13.2f\n", 'Change Return:', $changeReturn));

            $printer->feed();
            // === PAYMENT INFO ===
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text('Payment Mode: '.ucfirst($order->payment->payment_method??'N/A')."\n");
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', 40)."\n");

            // === SUMMARY SECTION ===
            $totalItems = count($order->items);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for dining with us!\n");
            $printer->text(str_repeat('=', 40)."\n");

            // === FOOTER ===
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("!!! FOR THE LOVE OF FOOD !!!\n");
            $printer->text("Powered by DoodlenDash\n");
            $printer->text("https://doodlendash.com\n");
            $printer->feed(2);

            $printer->cut();
            $printer->close();

            return ['status' => true, 'message' => "Receipt printed to {$ip}:{$port}"];

        } catch (\Exception $e) {
            Log::error("Printer connection failed ({$ip}): ".$e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function StationPrint($order)
    {




     $stickersHtml = '';
     $globalIndex = 1; // THIS is your correct series counter
     $quantity = $order->items->sum('quantity') ?? 1; // Default 1 if not set
       // $i=1;
        foreach ($order->items as $item) {


             for ($i = 0; $i < $item->quantity; $i++) {
            $stickersHtml .= view('stickers.dynamic', [
                'order' => $order,
                'item' => $item,
                "quantity" => $quantity,
                "i" =>   $globalIndex,
            ])->render();
            // Add page break between stickers
            // $stickersHtml .= '<div style="page-break-after: alw  ays;"></div>';
              $globalIndex++; // Increase for global sticker number
            }
            $i++;

        }


        return response($stickersHtml);
        // try {
        //     // Connect to a local printer file (example: /dev/usb/lp0 or temp file)
        //     // //$connector = new FilePrintConnector("/dev/usb/lp0"); // Linux example
        //      $connector = new WindowsPrintConnector("BC_LP1300");

        //     $printer = new Printer($connector);

        //     $connector = $this->getPrinterConnector();
        //     $printer = new Printer($connector);
        //     // dd($printer);

        //     // Optional: Add logo if available
        //     $logoPath = public_path('logoimage.png');
        //     // dd( $logoPath);
        //     if (file_exists($logoPath)) {
        //         $logo = EscposImage::load($logoPath, false);
        //         // dd( $logo);
        //         $printer->setJustification(Printer::JUSTIFY_CENTER);
        //         $printer->bitImage($logo);
        //     }
        //     //  dd(  $printer );

        //     $printer->setJustification(Printer::JUSTIFY_CENTER);
        //     // $printer->text("----------------------------------------\n");
        //     // $printer->text("         LOOP STICKER RECEIPT\n");
        //     // $printer->text("----------------------------------------\n\n");

        //     $printer->setJustification(Printer::JUSTIFY_LEFT);
        //     $printer->text('Order#: '.($order->order_uid ?? $order->id)."\n\n");

        //     foreach ($order->items as $item) {
        //         $printer->text(($item->productVariant->product->name ?? 'N/A')."\n");
        //         $printer->text('Size: '.($item->productVariant->sizes->name ?? '-')."\n");
        //         $printer->text('Note: '.($item->notes ?? '—')."\n");
        //         $printer->text("----------------------------------------\n");

        //         // 👇 Print sticker copies based on quantity
        //         $quantity = $item->quantity ?? 1;
        //         for ($i = 1; $i < $quantity; $i++) {
        //             $printer->feed(2);
        //             $printer->cut();
        //             $printer->bitImage($logo ?? null);
        //             $printer->setJustification(Printer::JUSTIFY_CENTER);
        //             $printer->text("         LOOP STICKER RECEIPT\n");
        //             $printer->setJustification(Printer::JUSTIFY_LEFT);
        //             $printer->text(($item->productVariant->product->name ?? 'N/A')."\n");
        //             $printer->text('Size: '.($item->productVariant->sizes->name ?? '-')."\n");
        //             $printer->text('Note: '.($item->notes ?? '—')."\n");
        //             $printer->text("----------------------------------------\n");
        //         }
        //     }

        //     $printer->feed(3);
        //     $printer->cut();
        //     $printer->close();
        //     dd($printer);
        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        //     \Log::error('Sticker Print Error: '.$e->getMessage());
        // }

    }

    public function pingPrinter($ip, $timeout = 2)
    {
        // OS-specific ping command
        $os = strtoupper(substr(PHP_OS, 0, 3));

        if ($os === 'WIN') {
            $ping = exec('ping -n 1 -w '.($timeout * 1000).' '.escapeshellarg($ip), $output, $status);
        } else {
            $ping = exec('ping -c 1 -W '.$timeout.' '.escapeshellarg($ip), $output, $status);
        }
       // dd($ping);

        return $status === 0;
    }

    public function handlePrinterJob($order, $ip)
    {
        try {
            // Step 1: Ping printer
            if (! $this->pingPrinter($ip)) {
                return ['status' => false, 'error' => "Printer at {$ip} not reachable."];
            }

            // Step 2: Proceed to print
            $this->printReceipt($order, $ip);
            $this->printOrderItems($order);

            return ['status' => true, 'message' => 'Successfully printed'];
        } catch (\Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    private function generateStickerText($order)
    {
        $text = "----------------------------------------\n";
        $text .= "           LOOP STICKER RECEIPT\n";
        $text .= "----------------------------------------\n";
        $text .= 'Order#: '.($order->order_uid ?? $order->id)."\n\n";

        foreach ($order->items as $item) {
            $text .= 'Product: '.($item->productVariant->product->name ?? 'N/A')."\n";
            $text .= 'Size: '.($item->productVariant->sizes->name ?? '-')."\n";
            $text .= 'Note: '.($item->notes ?? '—')."\n";
            $text .= "----------------------------------------\n";
        }

        $text .= "\n\n\n"; // Some spacing before cut

        return $text;
    }

    private function getPrinterConnector()
    {
        // Detect OS automatically
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // 🟢 Windows: use printer name as installed in Control Panel
            // Check: Control Panel → Devices & Printers → Printer Properties → "Share name"
            $printerName = 'BC_LP1300'; // <-- replace with your exact printer name

            return new WindowsPrintConnector($printerName);
        } else {
            // 🟢 Linux / macOS: use device file or shared name
            // Common USB thermal printer path
            $devicePath = '/dev/usb/lp0';

            return new FilePrintConnector($devicePath);
        }
    }
}
