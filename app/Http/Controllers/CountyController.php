<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\County;

class CountyController extends Controller
{
    /**
     * @api {get} /api/counties Összes megye listázása
     * @apiName GetCounties
     * @apiGroup County
     * @apiVersion 1.0.0
     * @apiDescription Lekéri az összes megyét a hozzájuk kapcsolódó városok adataival együtt.
     *
     * @apiSuccess {Object[]} counties Megyék listája.
     * @apiSuccess {Number} counties.id A megye egyedi azonosítója.
     * @apiSuccess {String} counties.name A megye neve.
     * @apiSuccess {Object[]} counties.cities A megyéhez tartozó városok listája.
     * @apiSuccess {Number} counties.cities.id A város azonosítója.
     * @apiSuccess {String} counties.cities.name A város neve.
     * @apiSuccess {Number} counties.cities.county_id A város megye azonosítója.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "counties": [
     *         {
     *           "id": 12,
     *           "name": "Pest",
     *           "cities": [
     *             {
     *               "id": 1,
     *               "name": "Vác",
     *               "county_id": 12
     *             }
     *           ]
     *         }
     *       ]
     *     }
     */
    public function index()
    {
        $counties = County::with('cities')->get();
        return response()->json([
            'counties' => $counties,
        ]);
    }

    /**
     * @api {post} /api/counties Új megye létrehozása
     * @apiName StoreCounty
     * @apiGroup County
     * @apiVersion 1.0.0
     * @apiPermission Bearer Token
     * @apiDescription Új megye rögzítése a rendszerben. Hitelesítés szükséges.
     *
     * @apiBody {String} name A megye neve (max 255 karakter).
     *
     * @apiSuccess {Object} county A létrehozott megye objektum.
     * @apiSuccess {Number} county.id A megye egyedi azonosítója.
     * @apiSuccess {String} county.name A megye neve.
     *
     * @apiError (Hiba 401) Unauthorized Hiányzó vagy érvénytelen Bearer Token.
     * @apiError (Hiba 422) UnprocessableEntity Validációs hiba (hiányzó adatok).
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "county": {
     *         "id": 12,
     *         "name": "Pest"
     *       }
     *     }
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $county = County::create($request->all());

        return response()->json([
            'county' => $county,
        ]);
    }

    /**
     * @api {put} /api/counties/:id Létező megye módosítása
     * @apiName UpdateCounty
     * @apiGroup County
     * @apiVersion 1.0.0
     * @apiPermission Bearer Token
     * @apiDescription Létező megye adatainak frissítése ID alapján. Hitelesítés szükséges.
     *
     * @apiParam {Number} id A megye egyedi azonosítója az URL-ben.
     * @apiBody {String} [name] A megye új neve.
     *
     * @apiSuccess {Object} county A frissített megye objektum.
     *
     * @apiError (Hiba 401) Unauthorized Hiányzó vagy érvénytelen Bearer Token.
     * @apiError (Hiba 404) NotFound A megadott azonosítóval nem található megye.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "county": {
     *         "id": 12,
     *         "name": "Pest Új név"
     *       }
     *     }
     */
    public function update(Request $request, $id)
    {
        $county = County::findOrFail($id);
        $county->update($request->all());

        return response()->json([
            'county' => $county,
        ]);
    }

    /**
     * @api {delete} /api/counties/:id Megye törlése
     * @apiName DestroyCounty
     * @apiGroup County
     * @apiVersion 1.0.0
     * @apiPermission Bearer Token
     * @apiDescription Megye törlése a rendszerből ID alapján. Hitelesítés szükséges.
     *
     * @apiParam {Number} id A törlendő megye egyedi azonosítója.
     *
     * @apiSuccess {String} message Sikeres törlést igazoló üzenet.
     *
     * @apiError (Hiba 401) Unauthorized Hiányzó vagy érvénytelen Bearer Token.
     * @apiError (Hiba 404) NotFound A megadott azonosítóval nem található megye.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "message": "County deleted"
     *     }
     */
    public function destroy($id)
    {
        $county = County::findOrFail($id);
        $county->delete();

        return response()->json([
            'message' => 'County deleted',
        ]);
    }
    

    /**
     * @api {get} /api/county Megyék keresése
     * @apiName SearchCounty
     * @apiGroup County
     * @apiVersion 1.0.0
     * @apiDescription Megyék keresése név részlet vagy teljes név alapján.
     *
     * @apiQuery {String} name Keresendő megyenév vagy névtöredék.
     *
     * @apiSuccess {Object[]} counties A találati listában szereplő megyék.
     *
     * @apiSuccessExample {json} Sikeres válasz:
     *     HTTP/1.1 200 OK
     *     {
     *       "counties": [
     *         {
     *           "id": 12,
     *           "name": "Pest"
     *         }
     *       ]
     *     }
     */
    public function search(Request $request) {
        $counties = County::where('name', 'LIKE', '%' . $request->name . '%')->get();
        return response()->json([
            'counties' => $counties,
        ]);
    }
}
