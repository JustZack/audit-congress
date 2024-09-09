# Design Revisions

This file is meant to help me keep track of each time the project has changed direction.

__December 2022 --> January 2023 - The first idea:__

Learned that api.congress.gov exists. initial project only had a react frontend which leverged api calls directly to api.congress.gov.

Goal here was proof of concept, project paused due to lack of time.

__January 2024 --> May 2024 - Build out a real backend & Database:__

Decide to restart the project & quickly realize I need to *somehow* cache every bill, vote, and legislator in a database. This also means I preferably need to download this information in bulk from time to time. Note there are roughly ~250K bills between the "modern" congresses (93-preset/118). Each bill may have hundreds of rows to insert into the database aswell. In short, its alot of information to manage.

the TL;DR is in this stage a robust API, backend, and DB was built out to house ALL bill information I could get. Read below for the how.

* Naive idea for bulk data was via the congress API, but this felt slow & hacky. Note congress API allows 5K requests/hour/API key (easy to procure many). Each bill was 10 - 15 requests on the low endend, assuming 1 page per bill part (actions, titles, texts, sponsors, ect). thats at best 500 FULL bills per hour.
* After some time, I found that ProPublical hosts a [dataset](https://www.propublica.org/datastore/dataset/congressional-data-bulk-legislation-bills) that updated every 6 hours daily. It had the same (and sometimes less) data as api.congress.gov. I opted to design by bulk bill data pulls off this dataset, which took up to 30 minutes to pull down & parse the entire data set via python. Updates to the most recent congress took a few minutes.
  * I used the [congress-legislators](https://github.com/unitedstates/congress-legislators) repository to fetch member data in bulk, which has proven useful.
  * If you click the dataset above however, you'll see it no longer exists however as it was depricated in June of 2024. This was a major setback in design.
* The [congress](https://github.com/unitedstates/congress) repository (by the same people as legislators) exists that fetches all bill information via govtrack & some other sources. Other large websites use this repository for their purposes as it works well. Thing is its very very *very* slow in my testing & I feel it could be done better. Its a 12 year old repositroy.

__June 2024 --> August 2024 - Evaluate my strategy & Goals__

Losing the bulk dataset was disheartening & put me nearly back and square one. Some time off the project while I consider what the next steps are & ensure my effort is not misplaced in the mean time.

__September 2024 --> ? - Try out ideas & Settle on next steps__

By now I had time to think about alot of ideas and get excited about the result of this project. Heres some of my ideas:

* Building a congress.gov web scraper is something I've seen mentioned on other projects but never done. Via a few services It seemed very feasable:
  * Via proxy scraping services like [Scraper API](scraperapi.com/), IP's can be reclyed, and full javascript can be rendered pages, and returned back. This would solve my own IP being blocked by repeated scraping.
  * Via AWS Lambda & RDS, I could offload the scraping process to a parrallel setup that could be scheduled to run via many queued lambda runs.
  * Via AWS API Gateway, I could setup a public API which gives access to the reuslts of these scrapes. My own application could then simply be a user of this API.
  * This idea would live in the cloud, and likely not even use a server for anything. Lambda would do as much as was monetarily feasable.
  * The pitfalls:
    * To scrape congress.gov, you MUST load javascript, which takes way longer to load via scraping services. This would be expensive on lambda & the proxy API scraping services alike.
    * AWS would not be cheap when even at this scale. RDS, Lambda, API gateway, aswell as the Scraper API might cost me hundreds per month when setup like this.
* Building a scaper for govtrack (Just like the repo mentioned above) was considered. Though the principal of downloading many files and then reading them still takes a long time unless you have a great SSD. At this point I've realized bulk data might not be the way.
* Final idea - fetch & update only what you need, when you need it & Cycle many congress API keys and keep track of usage to stay under 5K hourly limits.
  * Initially preform a minimal fetch of bills all via api.congress.gov to gather basic information about every bill while still allowing for broad searches. Initial pulls comes in pages of 250, which should fall under the limit of 5K requests per hour.
    * After the initial fetch, asyncrnously check the updated bill listing from time to time in the backend until you reach a known bill which has not updated (via the updateDate field).
    * In this way, we always know of every bill, but never perform expensive fetches.
  * Do a full fetch of legislators via congress or other API's all at once and push to the database. (already done)
  * When you click a bill, the backend will fetch/update all of its information in the background if needed.
    * New API Route: bill/congress/type/number/cache-status - Return the status of cached information & trigger a fetch if needed.
  * When you click a member, the backend will fetch/update all of its bill information in the background if needed.
    * New API Route: member/number/cache-status - Return the status of cached information & trigger a fetch if needed.



Whats data is left to gather at the time of writing?

* Complete fetching and parsing votes on bills - already started sometime ago.
  * These connect members AND bills together.
  * Need a solution to fetch the XML vote files en masse & update continuously.
  * Likely will use a scraper service here to avoid blocking my IP (for the 5th time)
