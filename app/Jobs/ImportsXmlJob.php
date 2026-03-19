<?php

namespace App\Jobs;

use App\Models\{Hotel, Customer, Room, Rate, Reservation, RoomReservation, DailyPrice};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use SimpleXMLElement;

class ImportsXmlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $hotels_xml,
        protected string $rooms_xml,
        protected string $rates_xml,
        protected string $reservations_xml
    ) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        // 1. HOTELS
        $xml = new SimpleXMLElement($this->hotels_xml);
        foreach ($xml->hotel as $h) {
            Hotel::updateOrCreate(
                ['id' => (int) $h['id']],
                ['name' => (string) $h->name]
            );
        }

        // 2. ROOMS
        $xml = new SimpleXMLElement($this->rooms_xml);
        foreach ($xml->room as $r) {
            Room::updateOrCreate(
                ['id' => (int) $r['id']],
                [
                    'hotel_id'        => (int) $r['hotel_id'],
                    'name'            => (string) $r,
                    'inventory_count' => (int) $r['inventory_count'],
                ]
            );
        }

        // 3. RATES
        $xml = new SimpleXMLElement($this->rates_xml);
        foreach ($xml->rate as $r) {
            Rate::updateOrCreate(
                ['id' => (int) $r['id']],
                [
                    'hotel_id' => (int) $r['hotel_id'],
                    'name'     => (string) $r,
                    'active'   => (bool) $r['active'],
                    'price'    => (float) $r['price'],
                ]
            );
        }

        // 4. RESERVATIONS + CUSTOMERS + ROOM_RESERVATIONS + DAILY_PRICES
        $xml = new SimpleXMLElement($this->reservations_xml);

        foreach ($xml->reservation as $res) {

            $first_name = trim((string) $res->customer->first_name);
            $last_name  = trim((string) $res->customer->last_name);

            $customer = Customer::firstOrCreate(
                [
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                ]
            );

            // Reservation
            $reservation = Reservation::updateOrCreate(
                ['id' => (int) $res->id],
                [
                    'customer_id' => $customer->id,
                    'date'        => (string) $res->date,
                    'time'        => (string) $res->time,
                    'hotel_id'    => (int) $res->hotel_id,
                ]
            );

            // Room Reservation
            $roomXml = $res->room;

            $guestCounts = [];
            if (isset($roomXml->guest_counts->guest_count)) {
                $guestCountElements = $roomXml->guest_counts->guest_count;
                if (!is_array($guestCountElements)) {
                    $guestCountElements = [$guestCountElements];
                }

                foreach ($guestCountElements as $gc) {
                    $guestCounts[] = [
                        'count' => (int) $gc['count'],
                        'type'  => (string) ($gc['type'] ?? 'adult'),
                    ];
                }
            }

            $roomReservation = RoomReservation::updateOrCreate(
                ['id' => (int) $roomXml->roomreservation_id],
                [
                    'reservation_id'  => $reservation->id,
                    'room_id'         => (int) $roomXml->id,
                    'arrival_date'    => (string) $roomXml->arrival_date,
                    'departure_date'  => (string) $roomXml->departure_date,
                    'currencycode'    => (string) $roomXml->currencycode,
                    'meal_plan'       => (string) $roomXml->meal_plan,
                    'guest_counts'    => $guestCounts,
                    'totalprice'      => (float) $roomXml->totalprice,
                ]
            );

            // Daily Prices
            foreach ($roomXml->price as $priceElement) {
                DailyPrice::updateOrCreate(
                    [
                        'room_reservation_id' => $roomReservation->id,
                        'date'                => (string) $priceElement['date'],
                    ],
                    [
                        'rate_id' => (int) $priceElement['rate_id'],
                        'price'   => (float) $priceElement,
                    ]
                );
            }
        }
    }
}
