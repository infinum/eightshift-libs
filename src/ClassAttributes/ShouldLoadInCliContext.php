<?php

namespace EightshiftLibs\ClassAttributes;

use Attribute;

/**
 * A class attribute definition class, inspected with Reflection when setting up services for DI in Main.
 * ServiceInterface classes annotated with this attribute should be loaded in the CLI context as well,
 * although they don't have to implement ServiceCliInterface themselves.
 */
#[Attribute]
class ShouldLoadInCliContext
{
}
