See database diagram here: https://dbdiagram.io/d/audit-congress-65b58579ac844320aedb92be

Goal: 
    * Represent relevent parts of api.congress.gov in SQL DB
    * Make data easily queryable/analizable
        - does not imply simple table layout per se
    * Abstract api.congress.gov data away from primary keys
        - Always use auto genetered ID's as primary keys
        - Include states/chambers/parties/etc.. in this structure

