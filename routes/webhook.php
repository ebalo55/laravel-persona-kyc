<?php

use Doinc\PersonaKyc\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("persona-kyc.webhook_prefix"))->post("/persona/hook", [WebhookController::class, "handle"])->name("persona_webhook");
