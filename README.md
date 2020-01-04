# Syrma Config-Generator

## Goal 
This tool help for developers the easy config generation, 
if the developers use the multi environments.

## Config file reference
```

# List of other configuration files.
imports:

    # Prototype
    -

        # The other configuration file.Absolute path or relative for this file.
        resource:             ~

defaults:

    # Default output path for generator. Absolute path or relative for this file.
    # Available placeholders in value: {{env}}, {{environment}}, {{definition}}.
    outputBasePath:       /srv/szicsu/syrma/config-generator

    # List of parameters for all definition envs.
    parameters:           []

    # List of extra parameter files for all definition scopes. Absolute path or relative for this file.
    # Available placeholders in value: {{env}}, {{environment}}, {{definition}}.
    parameterFiles:       []

definitions:

    # Prototype
    definitionId:

        # Template for current definition. Absolute path or relative for this file.
        # Available placeholders in value: {{env}}, {{environment}}, {{definition}}.
        template:             ~ # Required

        # Type of the configuration file
        type:                 ~ # One of "plain"; "ini"; "xml"; "yml"; "cron"; "nginx", Required

        # Output base path for generation. Absolute path or relative for this file.
        # If it is empty then it use default.outputBasePath.
        # Available placeholders in value: {{env}}, {{environment}}, {{definition}}.
        outputBasePath:       ~

        # Output file name. Absolute file name or relative for outputBasePath
        # Available placeholders in value: {{env}}, {{environment}}, {{definition}}.
        output:               ~

        # Environment independent parameters for this definition.
        parameters:           []

        # List of extra parameter files definition scope. Absolute path or relative for this file.
        # Available placeholders in value: {{env}}, {{environment}}, {{definition}}.
        parameterFiles:       []

        # List of enviroments
        environments:

            # Prototype
            envId:

                # Output file name. Absolute file name or relative for outputBasePath
                # If it is empty then it use definition.output
                # Available placeholders in value: {{env}}, {{environment}}, {{definition}}.
                output:               ~

                # Environment dependent parameters for this env.
                # The $env, $environment and $definition variables automatic add this config
                parameters:           []

                # List of extra parameters for this env. Absolute path or relative for this file.
                # Available placeholders in value: {{env}}, {{environment}}, {{definition}}.
                parameterFiles:       []

```