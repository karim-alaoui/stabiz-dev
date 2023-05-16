<?php

namespace App\Http\Controllers\API;

use App\Actions\GetTemplates;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CreateTemplateReq;
use App\Http\Requests\GetTemplatereq;
use App\Http\Requests\TemplateUpdateReq;
use App\Http\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use JetBrains\PhpStorm\Pure;

/**
 * @group Email template
 */
class MailTemplateController extends BaseApiController
{
    public function __construct()
    {
        $this->authorizeResource(EmailTemplate::class);
    }

    /**
     * Get email templates
     *
     * @param GetTemplatereq $request
     * @return AnonymousResourceCollection
     */
    public function index(GetTemplatereq $request): AnonymousResourceCollection
    {
        return EmailTemplateResource::collection(GetTemplates::execute($request->all()));
    }

    /**
     * Create mail template
     *
     * @param CreateTemplateReq $request
     * @return JsonResponse
     */
    public function store(CreateTemplateReq $request): JsonResponse
    {
        return (new EmailTemplateResource(EmailTemplate::create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get an email template
     *
     * @urlParam emailTemplate required email template id. No-example
     * @param EmailTemplate $emailTemplate
     * @return EmailTemplateResource
     */
    #[Pure] public function show(EmailTemplate $emailTemplate): EmailTemplateResource
    {
        return new EmailTemplateResource($emailTemplate);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TemplateUpdateReq $request
     * @param EmailTemplate $emailTemplate
     * @return EmailTemplateResource
     */
    public function update(TemplateUpdateReq $request, EmailTemplate $emailTemplate): EmailTemplateResource
    {
        $data = $request->validated();
        if (count($data)) $emailTemplate->update($data);
        return new EmailTemplateResource($emailTemplate);
    }

    /**
     * Delete email template
     *
     * @responseFile status=204 storage/responses/delete.json
     * @param EmailTemplate $emailTemplate
     * @return Response
     */
    public function destroy(EmailTemplate $emailTemplate): Response
    {
        $emailTemplate->delete();
        return $this->noContent();
    }
}
