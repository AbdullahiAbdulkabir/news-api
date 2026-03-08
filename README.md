## News aggregator

This repo contains the implementation of a news aggregator.

## Backend Installation & Requirement
Clone the repository and install dependencies 
- Composer:
- Apache or Nginx (server to run the application):
- PHP (requires 8.2 upwards):

```bash
# cd to the file
cd news-api
```
```bash
# Install PHP dependencies
composer install
```

## Environment Configuration

Copy the example environment file and set up the required configurations:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Run migration:
```bash
php artisan migrate
```

Run database seeder with a user seeded
```bash
php artisan db:seed
```


To run app 
```bash
#use sail
./vendor/bin/sail up
#or
php artisan serve
```
the app should be available in [http://localhost:8000/](http://127.0.0.1:8000/)

Fetch news from a specific a source
NOTE: If no source is specified, it runs for the configured sources in the NewsSourceServiceProvider
```bash
php artisan fetch:news  --source=NewsAPI
#or "New York Times" or "The Guardian"

```

## Deployment

### Deploying to Production

For production deployment, set up your web server:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan schedule:work
```

## Run Tests

```bash
php artisan test
```
## To fix PHP code style

```bash
./vendor/bin/pint
./vendor/bin/rector
```

## Architecture & Design Patterns
- I implemented a command that allows pulling articles from different sources. 

This is scheduled every 30 mins in `routes/console.php`
```php
Schedule::command('fetch:news')->everyThirtyMinutes();
```

- In this command I implemented the NewsSources, it implements the singleton approach that allows one instance throughout the entire lifecycle. Inside the NewsSourceServiceProvider. Promotes dependency injection
it implements the singleton

```php
$this->app->singleton(NewsSources::class, function () {
            $source = new NewsSources;
            $source->addSource(new NewsApiSource);
            if (! app()->environment('testing')) {
                $source->addSource(new GuardianSource);
                $source->addSource(new NewYorkTimeSource);
            }
            return $source;
        });
```

- Each source implements an abstract class NewsAbstract::class and implements an interface to fetch articles from various sources.
- I implemented Data Transfer Objects (DTOs), ArticleDTO, AuthorDTO, CategoryDTO to allow data transformation. It extends spatie laravel data. Allows for unification of data promoting Single responsibility Principle (SRP) and encapsulation 
- 
### Sample Request
To retrieve all articles, the endpoint is 
- **Get All Articles**: `GET /api/articles`
```http
  GET /api/articles?filter[title]=live&filter[authors.name]=Jessie&page[number]=1
  ```

-  **Query Parameters**:
   -  `filter[title]`: Filter articles by title.
   - `filter[authors.name]`: Filter articles by author's name.
   - `filter[categories.name]`: Filter articles by category's name.
   - `filter[source]`: Filter articles by source.
   - `filter[published_at]`: Filter articles by published_at.
### Sample response
![Response](sample-response.jpeg "Response")
```json
{
  "data": [
    {
      "id": 1,
      "title": "Intel report warns large-scale war ‘unlikely’ to oust Iran’s regime - The Washington Post",
      "description": "A classified U.S. report doubts that Iran’s opposition would take power following either a short or extended U.S. military campaign.",
      "content": "A classified report by the National Intelligence Council found that even a large-scale assault on Iran launched by the United States would be unlikely to oust the Islamic republics entrenched militar… [+169 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://www.washingtonpost.com/wp-apps/imrs.php?src=https://arc-anglerfish-washpost-prod-washpost.s3.amazonaws.com/public/YH2RNZOYDY3G776PTNDD5MLK74_size-normalized.jpg&w=1440",
      "external_url": "https://www.washingtonpost.com/national-security/2026/03/07/iran-intelligence-report-unlikely-oust-regime/",
      "published_at": "2026-03-07T14:42:54+00:00",
      "authors": [
        {
          "name": "John Hudson"
        },
        {
          "name": "Warren P. Strobel"
        }
      ],
      "categories": []
    },
    {
      "id": 2,
      "title": "Digital reconstruction reveals the face of ‘Little Foot,’ a nearly 4 million-year-old human ancestor - CNN",
      "description": "Little Foot, a 3.67 million-year-old human ancestor, is getting a digital facial reconstruction after her skull was crushed in a cave.",
      "content": "Scientists can now come face to face with an early human ancestor nicknamed Little Foot who lived 3.67 million years ago, thanks to digital reconstruction technology.\r\nRenowned paleoanthropologist Ro… [+7155 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://media.cnn.com/api/v1/images/stellar/prod/c-gettyimages-2209822258.jpg?c=16x9&q=w_800,c_fill",
      "external_url": "https://www.cnn.com/2026/03/07/science/little-foot-fossil-face-reconstruction",
      "published_at": "2026-03-07T12:30:33+00:00",
      "authors": [
        {
          "name": "Ashley Strickland"
        }
      ],
      "categories": []
    },
    {
      "id": 3,
      "title": "Gas prices: Prices continue to surge with double-digit increases in NC - WRAL",
      "description": "WRAL is tracking the change to gas prices daily to help central North Carolina drivers better balance their household budgets.",
      "content": "As the United States and Israel bombard in an attempt to neutralize that nation's military power, the rise in the price of oil and gas is one of the most noticeable immediate impacts for North Caroli… [+1074 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://images.wral.com/asset/news/local/2026/03/06/22332401/3292160-Gas_prices2-DMID1-6a8a8t5q4-6000x4000.jpg?w=1200&h=630&height=630",
      "external_url": "https://www.wral.com/consumer/5onyourside/gas-prices-today-iran-war-march-2026/",
      "published_at": "2026-03-07T12:20:36+00:00",
      "authors": [
        {
          "name": "WRAL 5 On Your Side"
        }
      ],
      "categories": []
    },
    {
      "id": 4,
      "title": "Under threat, Iraqi Kurds resist pressure to join Iran war - Axios",
      "description": "\"The Kurds must not be the tip of the spear in this conflict,\" an Iraqi Kurdish official told Axios.",
      "content": "<ul><li>\"The Kurds must not be the tip of the spear in this conflict,\" a senior Kurdistan Regional Government (KRG) official told Axios.</li></ul>Zoom in: Iraq's Kurdish government prides itself on t… [+4168 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://images.axios.com/N6RJU-4_oFYHHpdRvMED0hKEXoY=/0x0:4200x2363/1366x768/2026/03/07/1772846544581.jpeg",
      "external_url": "https://www.axios.com/2026/03/07/iran-kurds-iraq-israel-trump-cia-mossad",
      "published_at": "2026-03-07T11:51:04+00:00",
      "authors": [
        {
          "name": "Barak Ravid"
        },
        {
          "name": "Marc Caputo"
        }
      ],
      "categories": []
    },
    {
      "id": 5,
      "title": "4 dead, mulitple injured after reported tornado hits southern Michigan - Detroit Free Press",
      "description": "A tornado was reported Friday afternoon as a severe storm moved across parts of southwest Michigan, leaving 4 dead and 12 injured, officials say.",
      "content": "A tornado was reported on the afternoon of Friday, March 6, as a severe storm moved across parts of southern Michigan, leaving at least 4 people dead and multiple injured, according to officials.\r\nTh… [+3114 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://www.freep.com/gcdn/authoring/authoring-images/2026/03/07/PPOH/89029408007-menard-2.JPG?crop=3599,2025,x0,y187&width=3200&height=1801&format=pjpg&auto=webp",
      "external_url": "https://www.freep.com/story/news/local/michigan/2026/03/06/tornado-warning-michigan/89024660007/",
      "published_at": "2026-03-07T11:29:15+00:00",
      "authors": [
        {
          "name": "Nour Rahal"
        }
      ],
      "categories": []
    },
    {
      "id": 6,
      "title": "Pope appoints Archbishop Gabriele Caccia as Apostolic Nuncio to the U.S. - Vatican News",
      "description": "Pope Leo appoints Archbishop Gabriele Caccia - Permanent Observer of the Holy See to the United Nations since 2019 - as Apostolic Nuncio to the United States. In a statement, Archbishop Caccia highlights this new role as a “mission at the service of communion…",
      "content": "Vatican News\r\nPope Leo appointed Archbishop Gabriele Caccia as Apostolic Nuncio to the United States on Saturday, March 7, 2026. Since 2019, he had been serving as Permanent Observer of the Holy See … [+2054 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://www.vaticannews.va/content/dam/vaticannews/multimedia/2025/settembre/05/caccia-onuAEM.jpg/_jcr_content/renditions/cq5dam.thumbnail.cropped.1500.844.jpeg",
      "external_url": "https://www.vaticannews.va/en/pope/news/2026-03/pope-leo-gabriele-caccia-apostolic-nuncio-usa.html",
      "published_at": "2026-03-07T11:06:02+00:00",
      "authors": [
        {
          "name": "Vatican News"
        }
      ],
      "categories": []
    },
    {
      "id": 7,
      "title": "Tony! Toni! Toné! Singer D'Wayne Wiggins' Family Fighting Over $700K Estate - TMZ",
      "description": "Tony! Toni! Toné! singer  D'Wayne Wiggins’ family is involved in a bitter war over his 6-figure estate nearly a year after his death ... TMZ has learned.",
      "content": "Tony! Toni! Toné! singer  D'Wayne Wiggins family is involved in a bitter war over his 6-figure estate nearly a year after his death ... TMZ has learned.\r\nAccording to court docs obtained by TMZ, D'Wa… [+1393 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://imagez.tmz.com/image/0f/16by9/2026/03/06/0fc6dfbaa9004a53be5438ea81e600e4_xl.jpg",
      "external_url": "https://www.tmz.com/2026/03/07/singer-dwayne-wiggins-family-fighting-over-estate/",
      "published_at": "2026-03-07T11:00:04+00:00",
      "authors": [
        {
          "name": "TMZ Staff"
        }
      ],
      "categories": []
    },
    {
      "id": 8,
      "title": "Noem reveals Trump will have 'big agreement' to announce at major summit with world leaders - Fox News",
      "description": "Kristi Noem will join President Donald Trump and 12 Latin American leaders at his Doral resort for Saturday’s \"Shield of the Americas\" summit on regional security partnerships.",
      "content": "Kristi Noem will reportedly join President Donald Trump and 12 Latin American leaders at his resort in Florida for a \"Shield of the Americas\" summit Saturday after her ouster as the Secretary of Home… [+3945 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://static.foxnews.com/foxnews.com/content/uploads/2025/12/trump-noem-green-card-public-benefits.png",
      "external_url": "https://www.foxnews.com/politics/noem-says-trump-have-big-agreement-announce-major-summit-world-leaders",
      "published_at": "2026-03-07T11:00:03+00:00",
      "authors": [],
      "categories": []
    },
    {
      "id": 9,
      "title": "Trump administration says it can't comply with order to start tariff refunds - Axios",
      "description": "A trade court this week had ordered the government to begin reimbursing importers who paid for tariffs.",
      "content": "<ul><li>The government's argument appears to have been successful: Later on Friday, the Court of International Trade reversed a previous order that mandated the government instantly start the refund … [+2111 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://images.axios.com/IabxNCMIyVd-DCPuU1L_ZBaUwjU=/0x317:4426x2806/1366x768/2026/03/06/1772814981937.jpeg",
      "external_url": "https://www.axios.com/2026/03/06/trump-tariffs-refunds-cbp",
      "published_at": "2026-03-07T10:41:57+00:00",
      "authors": [
        {
          "name": "Courtenay Brown"
        }
      ],
      "categories": []
    },
    {
      "id": 10,
      "title": "$1m is a retirement goal. Here's how long it lasts in every state. - USA Today",
      "description": "A new report reveals how long $1 million in retirement savings will last in every U.S. state, and it's not long enough.",
      "content": "A million dollars is an aspirational retirement-savings goal, celebrated in articles and Reddit threads about 401(k) millionaires.  \r\nBut a million dollars is not what it used to be. Only 36% of Amer… [+7239 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://www.usatoday.com/gcdn/authoring/authoring-images/2026/03/06/USAT/89017596007-money.JPG?crop=3707,2085,x0,y0&width=3200&height=1800&format=pjpg&auto=webp",
      "external_url": "https://www.usatoday.com/story/money/2026/03/07/how-long-1-million-lasts-retirement-ira-florida-arizona/89006766007/",
      "published_at": "2026-03-07T10:05:00+00:00",
      "authors": [
        {
          "name": "Daniel de Visé"
        }
      ],
      "categories": []
    },
    {
      "id": 11,
      "title": "Live updates: Iran’s president apologizes to neighbors, says country will halt strikes on them unless attacked - CNN",
      "description": "Iran’s president said his country will never surrender, as its military continued to trade strikes with Israel. Follow for live updates.",
      "content": "Dramatic footage showed Tehrans Mehrabad Airport on fire after airstrikes hit the Iranian capital in the early hours of Saturday, state media reported.\r\nThe airport, which opened in 1938, is located … [+1847 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://media.cnn.com/api/v1/images/stellar/prod/c-gettyimages-2264600187.jpg?c=16x9&q=w_800,c_fill",
      "external_url": "https://www.cnn.com/world/live-news/iran-war-us-israel-trump-03-07-26",
      "published_at": "2026-03-07T09:30:00+00:00",
      "authors": [
        {
          "name": "Jessie Yeung"
        },
        {
          "name": "James Legge"
        },
        {
          "name": "Laura Sharman"
        },
        {
          "name": "Adrienne Vogt"
        },
        {
          "name": "John Liu"
        },
        {
          "name": "Ross Adkin"
        }
      ],
      "categories": []
    },
    {
      "id": 12,
      "title": "College basketball picks, schedule: Predictions for Duke vs. UNC, more games on last weekend of regular season - CBS Sports",
      "description": "The final weekend of the college basketball season has plenty of marquee matchups on tap",
      "content": "The final weekend of the college basketball regular season is here. Teams across the country will have one final game this weekend before the real fun starts with power conference tournaments startin… [+4889 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://sportshub.cbsistatic.com/i/r/2026/03/07/cd80133a-2b85-46f9-a6ad-535ce5fc3ed5/thumbnail/1200x675/5e9dc517f366b5e0c3c58d12d61b356c/gettyimages-2260458229-1.jpg",
      "external_url": "https://www.cbssports.com/college-basketball/news/college-basketball-picks-predictions-odds-top-25-games-saturday-duke-north-carolina/",
      "published_at": "2026-03-07T06:18:00+00:00",
      "authors": [
        {
          "name": "Cameron Salerno"
        }
      ],
      "categories": []
    },
    {
      "id": 13,
      "title": "Celtics' Tatum starts rough, finds groove in 15-point return - ESPN",
      "description": "Celtics star Jayson Tatum said he felt anxious in his first game in 298 days but was able to relax and finished with 15 points, 12 rebounds and seven assists in a 120-100 win over the Mavericks.",
      "content": "BOSTON -- Celtics star Jayson Tatum described his return to the court Friday night, his first NBA game in 298 days, as a step in his recovery from a torn right Achilles.\r\n\"I still got a long way to g… [+4905 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://a1.espncdn.com/combiner/i?img=%2Fphoto%2F2026%2F0307%2Fr1624567_1296x729_16%2D9.jpg",
      "external_url": "https://www.espn.com/nba/story/_/id/48129090/celtics-tatum-starts-rough-finds-groove-15-point-return",
      "published_at": "2026-03-07T05:28:00+00:00",
      "authors": [
        {
          "name": "Jamal Collier"
        }
      ],
      "categories": []
    },
    {
      "id": 14,
      "title": "Ex-rapper’s political party leads early results in Nepal’s first election since 2025 youth revolt - AP News",
      "description": "Preliminary and partial results show that a new political party led by an ex-rapper leading Nepal’s parliamentary election, the country’s first since last year’s youth-led revolt. The Rastriya Swatantra, or National Independent, Party, had already won 27 of 1…",
      "content": "KATHMANDU, Nepal (AP) Preliminary and partial results released Saturday showed a new political party led by an ex-rapper leading Nepals parliamentary election, the countrys first since last years you… [+2110 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://dims.apnews.com/dims4/default/99982a2/2147483647/strip/true/crop/8640x5757+0+1/resize/980x653!/quality/90/?url=https%3A%2F%2Fassets.apnews.com%2Fc1%2F6c%2F7c203b96718de5e81b8f8b3836d8%2Fd8a1b54448a044619edbe04172f243d7",
      "external_url": "https://apnews.com/article/nepal-parliament-election-results-balendra-shah-aac242077d2dd052e7e9bdd7c088ccc9",
      "published_at": "2026-03-07T05:17:00+00:00",
      "authors": [
        {
          "name": "Binaj Gurubacharya"
        }
      ],
      "categories": []
    },
    {
      "id": 15,
      "title": "No. 19 Miami (Ohio) caps undefeated regular season with OT win - ESPN",
      "description": "Miami (Ohio) finished the regular season as the only unbeaten team in Division I men's basketball, winning 110-108 in overtime over Ohio.",
      "content": "Mar 7, 2026, 12:08 AM ET\r\nMiami (Ohio) finished the regular season as the only unbeaten team in Division I men's basketball thanks to Eian Elmer scoring a career-high 32 points and grabbing 12 reboun… [+1377 chars]",
      "author": null,
      "category": null,
      "source": "NewsAPI",
      "image_url": "https://a.espncdn.com/combiner/i?img=%2Fphoto%2F2026%2F0307%2Fr1624561_1296x729_16%2D9.jpg",
      "external_url": "https://www.espn.com/mens-college-basketball/story/_/id/48128883/no-19-miami-ohio-caps-undefeated-regular-season-ot-win",
      "published_at": "2026-03-07T05:08:00+00:00",
      "authors": [],
      "categories": []
    }
  ],
  "path": "http://127.0.0.1:8000/api/articles",
  "per_page": 15,
  "next_cursor": "eyJwdWJsaXNoZWRfYXQiOiIyMDI2LTAzLTA3IDA1OjA4OjAwIiwiX3BvaW50c1RvTmV4dEl0ZW1zIjp0cnVlfQ",
  "next_page_url": "http://127.0.0.1:8000/api/articles?cursor=eyJwdWJsaXNoZWRfYXQiOiIyMDI2LTAzLTA3IDA1OjA4OjAwIiwiX3BvaW50c1RvTmV4dEl0ZW1zIjp0cnVlfQ",
  "prev_cursor": null,
  "prev_page_url": null
}
```

Submitted by [Abdullahi Abdulkabir]([https://github.com/abdullahiabdulkabir])




