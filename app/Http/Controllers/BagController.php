<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bag;

class BagController extends Controller
{
    public function updateBagData(Request $request)
    {
        $request->validate([
            'battery' => 'required|integer|min:0|max:100', 
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $bag = $request->user()->bag;

        if (!$bag) {
            return response()->json(['message' => 'No bag linked to user'], 404);
        }

        $bag->update([
            'battery' => $request->battery,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['message' => 'Bag data updated successfully', 'bag' => $bag]);
    }

    public function searchByBagId(Request $request)
    {
        $request->validate([
            'bag_id' => 'required|string',
        ]);

        $bag = Bag::where('bag_id', $request->bag_id)->first();

        if (!$bag) {
            return response()->json(['error' => 'Bag not found'], 404);
        }

        return response()->json([
            'bag_id' => $bag->bag_id,
            'latitude' => $bag->latitude,
            'longitude' => $bag->longitude,
            'status' => $bag->status,
        ]);
    }
}
