<?php

namespace Doinc\PersonaKyc\Tests\Assets;

use Doinc\PersonaKyc\Enums\IPersonaTemplates;

enum PersonaTemplates: string implements IPersonaTemplates {
    public function val(): string
    {
        return $this->value;
    }

	case GOVERNMENT_ID = "itmpl_DVxFbisLWGNoAgLjvGTEgDVA";
	case GOVERNMENT_ID_AND_SELFIE = "example_GOVERNMENT_ID_AND_SELFIE_template_id";
}
