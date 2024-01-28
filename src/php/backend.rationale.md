GOAL:
    - Take in API requests from frontend
    - Load request arguments into into Congress OBJECTS
    - OBJECTS query the database to load data
    - OBJECTS decide if data the database to load data
    - OBJECTS make API calls to load data
    - OBJECTS update the database with newest API information
    - API waits for OBJECTS to complete operations
        * OBJECTS may make multi threaded requests
    - API ask OBJECT for Json response

NOTES ABOUT CONGRESS API:
    - Many ways to get same data:
        * All bills have sponsors/cosponsors
        * All members have sponsored/cosponsored bills



Primary models are: 
    Bill (and all items), 
    SimpleBill (with minimal parts of Bill),
    Amendment,
    Cosponsor,
    Member

Models:
    * Bill:
        Actions:
            Array(Action)
        Amendments:
            Array(Amendment)
        Comittees:
            Array(Comittee)
        Cosponsors:
            Array(Cosponsor)                
        RelatedBills:
            Array(SimpleBill)
        Subjects:
        Summaries:
            Summary
        TextVersions:
            Array(TextVersion)
        Titles:
            Array(Title)
    
    * SimpleBill: <-- Minimal version of a Bill

    * Amendment:
        Actions:
            Array(Action)
        Cosponsors:
            Array(Cosponsor)
        Amendments:
            Array(Amendment)
        TextVersions:
            Array(TextVersion)

    * Cosponsor:
        Member:

    * Member:
        SponsoredLegislation:
        CosponsoredLegislation:



