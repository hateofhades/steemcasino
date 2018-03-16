steem.api.setOptions({ url: 'https://api.steemit.com' });

function fillBlogEntries(username)
{
	steem.api.getDiscussionsByBlog({tag: username, limit: 20}, function(err, posts) 
	{
		var blogContainer = $('#blog');
		for (var i = 0; i < posts.length; i++) 
		{
			blogContainer.append('<div class="row">' + generatePreviewHtml(posts[i]) + '</div>');
		}
	});
}

const IMG_PROXY = 'https://steemitimages.com/0x0/';
const IMG_PROXY_PREVIEW = 'https://steemitimages.com/600x800/';

function getProxyImageURL(url, type)
{
	if (type === 'preview')
	{
		return `${IMG_PROXY_PREVIEW}${url}`;
	}

	return `${IMG_PROXY}${url}`;
};

function generatePreviewImageURL(post)
{
	const jsonMetadata = JSON.parse(post.json_metadata);
	let imagePath = '';
	bodyText = '';

	if (jsonMetadata.image && jsonMetadata.image[0])
	{
		imagePath = getProxyImageURL(jsonMetadata.image[0], 'preview');
	}

	return imagePath;
}

function generatePreviewText(post)
{
	bodyText = '';
	bodyText = post.body.replace(/(!\[.*?\]\()(.+?)(\))/g, '');
	bodyText= bodyText.replace(/<\/?[^>]+(>|$)/g, '');
	bodyText= bodyText.replace(/\[([^\]]+)\][^\)]+\)/g, '$1');

	return bodyText;
}

function generatePreviewHtml(post)
{
	previewHtml = 
		`<div class="blog-image col-md-2">
		<img src="`+ generatePreviewImageURL(post) + `">
		</div>
		<div class="col-md-10">
		<h5 class="font-weight-bold" style="margin-top:5px;">` + post.title + `</h5>
		<div class="multiline-ellipsis">
			<p>` + generatePreviewText(post) + `</p>
		</div>
		<a href="https://steemit.com` + post.url + `" target="_blank"><img class="media-button" src="img/steemit.png"></a>
		<a href="https://busy.org` + post.url + `" target="_blank"><img class="media-button" src="img/busy.png"></a>
		</div>`;

		return previewHtml;
}

function storyPreview ( article, author, permlink, callback)
{
	steem.api.getContent(author, permlink, function(err, post)
	{
		if(!err && post.body !== "")
		{
			if(("#spinner" + article).length)
			{
				$("#spinner" + article).hide();
			}
			
			$("#article" + article).append(generatePreviewHtml(post));

			typeof callback === 'function' && callback(post);
		}
		else
		{
			if(("#spinner" + article).length)
			{
				$("#spinner" + article).hide();
			}
			typeof callback === 'function' && callback(null);
		}
	});
}