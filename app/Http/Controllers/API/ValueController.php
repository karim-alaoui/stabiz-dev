<?php

namespace App\Http\Controllers\API;

use App\Actions\CacheClear;
use App\Http\Controllers\BaseApiController;
use App\Http\Resources\AreaResource;
use App\Http\Resources\Category4ArticleResource;
use App\Http\Resources\EduBgResource;
use App\Http\Resources\IncomeRangeResource;
use App\Http\Resources\IndustryCatResource;
use App\Http\Resources\IndustryResource;
use App\Http\Resources\LangLevelResource;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\MgmtExpResource;
use App\Http\Resources\OccupationCatResource;
use App\Http\Resources\OccupationResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\PositionResource;
use App\Http\Resources\PrefectureResource;
use App\Http\Resources\PresentPostResource;
use App\Http\Resources\WorkingStatusResource;
use App\Models\Area;
use App\Models\Category4Article;
use App\Models\EducationBackground;
use App\Models\IncomeRange;
use App\Models\Industry;
use App\Models\IndustryCat;
use App\Models\LangLevel;
use App\Models\Language;
use App\Models\MgmtExp;
use App\Models\Occupation;
use App\Models\OccupationCategory;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Position;
use App\Models\Prefecture;
use App\Models\PresentPost;
use App\Models\WorkingStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @group Values
 *
 * Returns common values like city,incomes etc
 * You will get the name/label/level in either Japanese or English.
 * Provide lang header to get it in that lang.
 * Sometimes, you will get English value that's because translation is not done yet.
 */
class ValueController extends BaseApiController
{
    /**
     * Clear cache
     *
     * Clear all the cache of the application. Can only be done by the super admin
     * @return JsonResponse|Response
     */
    public function clearCache(): Response|JsonResponse
    {
//        Gate::authorize('');
        try {
            CacheClear::execute();
            return $this->noContent();
        } catch (Exception) {
            return $this->errorMsg(__('Could not clear cache'));
        }
    }

    /**
     * Build different cache key based on language
     * @param Request $request
     * @param string $key
     * @return string
     */
    private function cacheKey(Request $request, string $key): string
    {
        $lang = $request->get('lang', 'en');
        return sprintf('%s_%s', $key, $lang);
    }

    /**
     * Income
     *
     * <aside>
     * The unit will be translate as per the lang header.
     * </aside>
     * @unauthenticated
     * @responseFile storage/responses/income.json
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws Exception
     */
    public function income(Request $request): AnonymousResourceCollection
    {
        return cache()->rememberForever($this->cacheKey($request, 'income'), function () {
            return IncomeRangeResource::collection(IncomeRange::query()->orderByRaw('lower_limit nulls first')->get());
        });
    }

    /**
     * Prefectures
     *
     * @unauthenticated
     * @queryParam area_id will return all prefecture of that area Example: 1
     * @responseFile storage/responses/prefecture.json
     * @throws Exception
     */
    public function prefecture(Request $request): AnonymousResourceCollection
    {
        $cacheKey = $this->cacheKey($request, 'prefecture');
        $prefecture = Prefecture::query();
        $areaId = $request->area_id;
        if ($areaId) {
            $cacheKey = $this->cacheKey($request, "prefecture:$areaId");
            $prefecture = $prefecture->where('area_id', $areaId)
                ->orderBy('in_area_sort_order');
        } else {
            $prefecture = $prefecture->orderBy('sort_order');
        }
        return cache()->rememberForever($cacheKey, fn() => PrefectureResource::collection($prefecture->get()));
    }

    /**
     * Areas
     *
     * @unauthenticated
     * @responseFile storage/responses/areas.json
     * @throws Exception
     */
    public function area(Request $request): AnonymousResourceCollection
    {
        return cache()->rememberForever($this->cacheKey($request, 'area'), function () {
            return AreaResource::collection(Area::query()->orderByRaw('sort_order nulls last')->get());
        });
    }

    /**
     * Education background
     *
     * @unauthenticated
     * <aside>
     * ** Name will be translate as per the `lang` header
     * </aside>
     * @responseFile storage/responses/edubg.json
     * @throws Exception
     */
    public function eduBg(Request $request): AnonymousResourceCollection
    {
        $cacheKey = $this->cacheKey($request, 'education_background');
        return cache()->rememberForever($cacheKey, fn() => EduBgResource::collection(EducationBackground::all()));
    }

    /**
     * Working status
     *
     * @unauthenticated
     * Same like edu background for name
     * @responseFile storage/responses/workingstatus.json
     * @throws Exception
     */
    public function workingStatus(Request $request): AnonymousResourceCollection
    {
        return cache()->rememberForever($this->cacheKey($request, 'working_status'), fn() => WorkingStatusResource::collection(WorkingStatus::all()));
    }

    /**
     * Industry
     *
     * @unauthenticated
     * The name will only be in English for now
     * @responseFile storage/responses/industry.json
     * @throws Exception
     */
    public function industry(Request $request): AnonymousResourceCollection
    {
        return cache()->rememberForever($this->cacheKey($request, 'industries'), fn() => IndustryResource::collection(Industry::all()));
    }

    /**
     * Industry category
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function industryCat(Request $request): mixed
    {
        return cache()->rememberForever($this->cacheKey($request, 'industry_categories'), function () {
            return IndustryCatResource::collection(IndustryCat::all());
        });
    }

    /**
     * Lang level
     *
     * @unauthenticated
     * @responseFile storage/responses/lang.json
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws Exception
     */
    public function langLevel(Request $request): AnonymousResourceCollection
    {
        return cache()->rememberForever($this->cacheKey($request, 'industry_categories'), fn() => LangLevelResource::collection(LangLevel::all()));
    }


    /**
     * Occupation
     *
     * @unauthenticated
     * @responseFile storage/responses/occupation.json
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws Exception
     */
    public function occupation(Request $request): AnonymousResourceCollection
    {
        $occupations = Occupation::query()->with('category')->get();
        return cache()->rememberForever($this->cacheKey($request, 'occupations'), fn() => OccupationResource::collection($occupations));
    }

    /**
     * Present post
     *
     * @unauthenticated
     * @responseFile storage/responses/presentpost.json
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws Exception
     */
    public function presentPost(Request $request): AnonymousResourceCollection
    {
        return cache()->rememberForever($this->cacheKey($request, 'present_posts'), fn() => PresentPostResource::collection(PresentPost::all()));
    }

    /**
     * Language
     *
     * @unauthenticated
     * @responseFile storage/responses/languages.json
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws Exception
     */
    public function language(Request $request): AnonymousResourceCollection
    {
        return cache()->rememberForever($this->cacheKey($request, 'langs'), fn() => LanguageResource::collection(Language::all()));
    }

    /**
     * Positions
     *
     * Company positions like CEO, CTO
     * @unauthenticated
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function position(Request $request): mixed
    {
        return cache()->rememberForever($this->cacheKey($request, 'positions'), fn() => PositionResource::collection(Position::all()));
    }

    /**
     * Occupation category
     *
     * Each position belongs to a position category
     * @unauthenticated
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function occupationCat(Request $request): mixed
    {
        return cache()->rememberForever($this->cacheKey($request, 'position_categories'), fn() => OccupationCatResource::collection(OccupationCategory::all()));
    }

    /**
     * Packages
     *
     * @unauthenticated
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function packages(Request $request): mixed
    {
        $packages = Package::with(['plans', 'monthlyPlan'])->get();
        return cache()->rememberForever($this->cacheKey($request, 'packages'), fn() => PackageResource::collection($packages));
    }

    /**
     * Plans
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function plans(Request $request): mixed
    {
        $plans = Plan::with('package')->get();
        return cache()->rememberForever($this->cacheKey($request, 'plans'), fn() => PlanResource::collection($plans));
    }

    /**
     * Category for article
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function category4articles(Request $request): mixed
    {
        $cacheKey = $this->cacheKey($request, 'category_for_articles');
        return cache()->rememberForever($cacheKey, fn() => Category4ArticleResource::collection(Category4Article::all()));
    }

    /**
     * Management exps
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function managementExps(Request $request): mixed
    {
        $cacheKey = $this->cacheKey($request, 'mgmt_exps');
        return cache()->rememberForever($cacheKey, fn() => MgmtExpResource::collection(MgmtExp::all()));
    }
}
