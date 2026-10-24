<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    /**
     * @api {get} /api/cities Összes város listázása
     * @apiName GetCities
     * @apiGroup City
     * @apiVersion 1.0.0
     * @apiDescription Lekéri az összes várost a hozzájuk kapcsolódó megye adataival együtt.
     *
     * @apiSuccess {Object[]} cities Városok listája.
     * @apiSuccess {Number} cities.id A város egyedi azonosítója.
     * @apiSuccess {String} cities.name A város neve.
     * @apiSuccess {Number} cities.county_id A kapcsolódó megye azonosítója.
     * @apiSuccess {Object} cities.county A kapcsolódó megye objektum adatai.
     * @apiSuccess {Number} cities.county.id A megye azonosítója.
     * @apiSuccess {String} cities.county.name A megye neve.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "cities": [
     *         {
     *           "id": 1,
     *           "name": "Vác",
     *           "county_id": 12,
     *           "county": {
     *             "id": 12,
     *             "name": "Pest"
     *           }
     *         }
     *       ]
     *     }
     */
    public function index()
    {
        $cities = City::with('county')->get();
        return response()->json([
            'cities' => $cities,
        ]);
    }

    /**
     * @api {post} /api/cities Új város létrehozása
     * @apiName StoreCity
     * @apiGroup City
     * @apiVersion 1.0.0
     * @apiPermission Bearer Token
     * @apiDescription Új város rögzítése a rendszerben. Hitelesítés szükséges.
     *
     * @apiBody {String} name A város neve (max 255 karakter).
     * @apiBody {Number} county_id A kapcsolódó megye létező azonosítója (ID).
     *
     * @apiSuccess {Object} city A létrehozott város objektum.
     * @apiSuccess {Number} city.id A város egyedi azonosítója.
     * @apiSuccess {String} city.name A város neve.
     * @apiSuccess {Number} city.county_id A kapcsolódó megye azonosítója.
     *
     * @apiError (Hiba 401) Unauthorized Hiányzó vagy érvénytelen Bearer Token.
     * @apiError (Hiba 422) UnprocessableEntity Validációs hiba (hiányzó adatok vagy nem létező county_id).
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "city": {
     *         "id": 2,
     *         "name": "Szentendre",
     *         "county_id": 12
     *       }
     *     }
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|exists:counties,id',
        ]);

        $city = City::create($request->all());

        return response()->json([
            'city' => $city,
        ]);
    }

    /**
     * @api {put} /api/cities/:id Létező város módosítása
     * @apiName UpdateCity
     * @apiGroup City
     * @apiVersion 1.0.0
     * @apiPermission Bearer Token
     * @apiDescription Létező város adatainak frissítése ID alapján. Hitelesítés szükséges.
     *
     * @apiParam {Number} id A város egyedi azonosítója az URL-ben.
     * @apiBody {String} [name] A város új neve.
     *
     * @apiSuccess {Object} city A frissített város objektum.
     *
     * @apiError (Hiba 401) Unauthorized Hiányzó vagy érvénytelen Bearer Token.
     * @apiError (Hiba 404) NotFound A megadott azonosítóval nem található város.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "city": {
     *         "id": 1,
     *         "name": "Vác Újváros",
     *         "county_id": 12
     *       }
     *     }
     */
    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);
        $city->update($request->all());

        return response()->json([
            'city' => $city,
        ]);
    }

    /**
     * @api {delete} /api/cities/:id Város törlése
     * @apiName DestroyCity
     * @apiGroup City
     * @apiVersion 1.0.0
     * @apiPermission Bearer Token
     * @apiDescription Város törlése a rendszerből ID alapján. Hitelesítés szükséges.
     *
     * @apiParam {Number} id A törlendő város egyedi azonosítója.
     *
     * @apiSuccess {String} message Sikeres törlést igazoló üzenet.
     *
     * @apiError (Hiba 401) Unauthorized Hiányzó vagy érvénytelen Bearer Token.
     * @apiError (Hiba 404) NotFound A megadott azonosítóval nem található város.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "message": "City deleted"
     *     }
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json([
            'message' => 'City deleted',
        ]);
    }

    /**
     * @api {get} /api/city Városok keresése
     * @apiName SearchCity
     * @apiGroup City
     * @apiVersion 1.0.0
     * @apiDescription Városok keresése név részlet vagy teljes név alapján.
     *
     * @apiQuery {String} name Keresendő városnév vagy névtöredék.
     *
     * @apiSuccess {Object[]} cities A találati listában szereplő városok.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "cities": [
     *         {
     *           "id": 1,
     *           "name": "Vác",
     *           "county_id": 12
     *         }
     *       ]
     *     }
     */
    public function search(Request $request) {
        $cities = City::where('name', 'LIKE', '%' . $request->name . '%')->get();
        return response()->json([
            'cities' => $cities,
        ]);
    }

    /**
     * @api {get} /api/cities_filtered Városok szűrt listázása
     * @apiName FilterCities
     * @apiGroup City
     * @apiVersion 1.0.0
     * @apiDescription Városok szűrése pontos megye név alapján.
     *
     * @apiQuery {String} name A megye pontos neve (pl. Pest).
     *
     * @apiSuccess {Object[]} cities A megadott megyéhez tartozó városok listája.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "cities": [
     *         {
     *           "id": 1,
     *           "name": "Vác",
     *           "county_id": 12
     *         }
     *       ]
     *     }
     */
    public function filter(Request $request) {
        $cities = City::whereHas('county', function ($query) use ($request) {
            $query->where('name', $request->name);
        })->get();
        return response()->json([
            'cities' => $cities,
        ]);
    }
}
