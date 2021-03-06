<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Member;
use App\Models\Code;
use App\Models\Kursi;
use App\Models\Reservasi;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $table = Event::all();
        return view('admin.Event.PageEvent')->with(['event' => $table]);
    }
    public function addEvent()
    {
        return view('admin.Event.addEvent');
    }
    public function postEvent(Request $r)
    {
        $event = new Event;

        $event->nama = $r->request->get('nama');
        $event->deskripsi = $r->request->get('deskripsi');
        $event->tanggal = $r->request->get('tanggal');
        $event->kuota = $r->request->get('kuota');
        $event->expired = $r->request->get('expired');
        $event->jam_mulai = $r->request->get('jam_mulai');
        $event->jam_selesai = $r->request->get('jam_selesai');
        $event->hari = $r->request->get('hari');

        $event->save();
        $table = Event::all();
        return view('admin.Event.PageEvent')->with(['event' => $table]);
    }

    public function deleteEvent($id)
    {
        Event::destroy($id);
        $table = Event::all();
        return view('admin.Event.PageEvent')->with(['event' => $table]);
    }

    public function editEvent($id)
    {
        $event = Event::findOrFail($id);
        return view('admin.Event.editEvent')->with(['event' => $event]);
    }

    public function updateEvent(Request $r)
    {
        $id = $r->request->get('id');
        $event = Event::findOrFail($id);
        $event->nama = $r->request->get('nama');
        $event->deskripsi = $r->request->get('deskripsi');
        $event->tanggal = $r->request->get('tanggal');
        $event->kuota = $r->request->get('kuota');
        $event->expired = $r->request->get('expired');
        $event->jam_mulai = $r->request->get('jam_mulai');
        $event->jam_selesai = $r->request->get('jam_selesai');
        $event->hari = $r->request->get('hari');

        $event->save();
        $table = Event::all();
        return view('admin.Event.PageEvent')->with(['event' => $table]);
    }

    public function detailEvent($id)
    {
        $detailEvent = Event::findOrFail($id);
        // dump(auth()->user()->role);
        // die();
        $detailMember = '';
        $age = 0;
        if (auth()->check() && auth()->user()->role == 'member') {
            $auth = auth()->id();
            $detailMember = Member::where("id_user", "=", $auth)->first();
            $now = new DateTime('now');
            $current_age = date_create($detailMember->tgl_lahir);
            $tempAge = date_diff($now, $current_age);
            $age = $tempAge->y;
            // dump($age->y);
            // dump($now);
            // dump($current_age);
            // die();
        }

        $booked     = Reservasi::with(['kursi'])->where('id_even', $id)->get();
        $tempBooked = [];
        foreach ($booked as $value) {
            array_push($tempBooked, $value->kursi->id);
        }
        $codeKursi  = Kursi::with(['code'])->whereNotIn('id', $tempBooked)->orderBy('code_id', 'ASC')->orderBy('no_kursi', 'ASC')->get();
        // dump($codeKursi->toArray());
        // die();
        // dump($detailMember);
        // die();
        $data = ['event' => $detailEvent, 'member' => $detailMember, 'code' => $codeKursi, 'age' => $age];
        return view('detail')->with($data);
    }

    public function EventReport()
    {
        $EventReport = Event::orderBy('id', 'DESC')->get();
        return view('admin.Event.EventReport')->with(['data' => $EventReport]);
    }

    public function EventReportDetail($id)
    {
        $EventDetail = Event::with(['reservasi.chair.code'])->where('id', $id)->firstOrFail();
        // return $EventDetail->toArray();
        return view('admin.Event.EventReportDetail')->with(['data' => $EventDetail]);
    }

    public function absensi(Request $r)
    {
        try {
            $id     = $r->request->get('id');
            $reservasi  = Reservasi::find($id);
            $reservasi->absen   = true;
            $reservasi->save();

            return response()->json([
                'msg' => 'success'
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'msg' => $th
            ], 500);
        }
    }
}
