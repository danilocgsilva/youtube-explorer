# Youtube Explorer

Fetches informations from youtube channes.

## Setup

To make it work, you need to have your own Google Cloud API for Youtube.

Them, you must set an environment variable called GOOGLE_CLOUD_API_KEY. You can do this in the .env file.

You already have a docker receipt to help to run the app.

## Unit tests

The application must make fetches data from Youtube api. In tests, I used the services container to inject mocks, so no real fetching action happens.

Also, I can dinamically change the provided data in tests changing the behaviour in service container. The `services.yaml` configuration folder also played a very important role in this sense.

To do so, I used this reference for a base knowledge in how to do this: https://engineering.facile.it/blog/eng/functional-testing-symfony-guzzle/.

Notice as well the deprecation notice thrown in the test. Maybe would be better in the future to replace the `ClientMock` to something else that, instead of extending the `GuzzleClient`, would be better to *implement* the same interface from `GuzzleClient`.

## Entities

**Channel**: A Youtube channel fetched when fetching videos. A new channel entry may be filled both by `App\Services\Fetch::fetch` as well by `App\Services\FetchAllVideos::fetchAllVideos`. Both classes may search for videos from a given channel. Once videos is fetched, data from channel is captured as well. If the channel already exists in the database (referenced by the `channel_id`), no new entry is added.

**ChannelData**: Everytime when a videos fetching happens, channel data may be recorded. For exemple, the channel video count. Each time data is fetched, this data may change. So every time a video fetching happens, a new entry for `channel_data` is written. Even if the Channel already is fetched by a previous fetching, a new entry for `channel_data` may be written. Each `channel_data` entry eventually writen multiple times to the same channel represents diferent states from the Channel that will be recorded.

**ChannelSearchHistory**: Everytime that the Youtube api is consulted, some data from the returned consult is recorded. Notice: sometimes you will need to make several consults to the Youtube api to fetch the videos, as it seems that exists a limit for fetching videos by Youtube api. And if multiples consults will be required for the same search, a new entry will be written for each consulta.

**MassFetchIteration**: data for each consult for the Youtube api. This entities relates to the **MassFetchJob**. A same `MassFetchJob` may be done and represents a single channel search that may need to consult the Youtube api several times. For each, it records data for iterations position and the `next_page_token` as well, so in case of not finished job or by some other reason a bulk search does not fetches all required data, next consults may happen to continue the job, based on the `next_page_token`.

**MassFetchJob**: represents s bulk search for a channel videos.