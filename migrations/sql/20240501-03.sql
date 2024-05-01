UPDATE _users SET project_name =
        CASE
            WHEN project_slug = 'hasmints' THEN 'HasMints'
            WHEN project_slug = 'astronaughties' THEN 'AstroNaughties'
            WHEN project_slug = 'pixelastros' THEN 'PixelAstros'
            WHEN project_slug = 'ripplepunks' THEN 'RipplePunks'
            ELSE NULL
            END;