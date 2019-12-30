# Syrma Config-Generator

## Goal 
This tool help for developers the easy config generation, 
if the developers use the multi environments.

## Config file reference
```
defaults:

    # Default output path for generator. Absolute path or relative for this file.
    # Available placeholders: {{env}}, {{enviroment}}, {{definition}}.
    outputBasePath:       [cwd]

    # Search list for template searching. Absolute path or relative for this file.
    # Available placeholders: {{env}}, {{enviroment}}, {{definition}}.
    templateSearchPaths:

        # Default:
        - [cwd]/templates

definitions:

    # Prototype
    definitionId:

        # Template for current definition. Absolute path or relative for this file.
        # Available placeholders: {{env}}, {{enviroment}}, {{definition}}.
        template:             ~ # Required

        # Type of the configuration file
        type:                 ~ # One of "plain"; "ini"; "xml"; "yml"; "cron"; "nginx", Required

        # Output base path for generation. Absolute path or relative for this file.
        # If it is empty then it use default.outputBasePath.
        # Available placeholders: {{env}}, {{enviroment}}, {{definition}}.
        outputBasePath:       ~

        # Search list for template searching.Absolute path or relative for this file.
        # If it is empty then it use default.templateSearchPaths.
        # Available placeholders: {{env}}, {{enviroment}}, {{definition}}.
        templateSearchPaths:  []

        # Environment independent parameters for this definition.
        parameters:           []

        # List of extra parameter files definition scope. Absolute path or relative for this file.
        # Available placeholders: {{definition}}.
        parameterFiles:       []

        # List of enviroments
        enviroments:

            # Prototype
            envId:

                # Output file name. Absolute file name or relative for outputBasePath
                # Available placeholders: {{env}}, {{enviroment}}, {{definition}}.
                output:               ~ # Required

                # Environment dependent parameters for this env.
                parameters:           []

                # List of extra parameters for this env. Absolute path or relative for this file.
                # Available placeholders: {{env}}, {{enviroment}}, {{definition}}.
                parameterFiles:       []

```