<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>News Browser</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-light">
<div class="container py-4">
  <h1 class="mb-4">The news browser</h1>

  <!-- Controls -->
  <div class="card mb-3 p-3">
    <div class="row g-2 align-items-center">
      <div class="col-md-4">
        <button id="btn-headlines" class="btn btn-primary w-100">
          <i>To load current headlines</i>
        </button>
      </div>
      <div class="col-md-4">
        <input id="search-input" type="text" class="form-control" placeholder="Search news...">
      </div>
      <div class="col-md-4">
        <button id="btn-search" class="btn btn-outline-secondary w-100">
          <b>Search</b>
        </button>
      </div>
    </div>
  </div>

  <div id="status" class="mb-3 text-muted"></div>
  <div id="results" class="row g-3"></div>
</div>

<script>
  const btnHeadlines = document.getElementById('btn-headlines');
  const btnSearch = document.getElementById('btn-search');
  const searchInput = document.getElementById('search-input');
  const statusEl = document.getElementById('status');
  const resultsEl = document.getElementById('results');

  function setStatus(msg) {
    statusEl.textContent = msg || '';
  }

  function clearResults() {
    resultsEl.innerHTML = '';
  }

  function renderArticles(articles) {
    clearResults();
    if (!articles || articles.length === 0) {
      setStatus('No articles found.');
      return;
    }

    setStatus(`Showing ${articles.length} articles.`);

    articles.forEach(article => {
      const col = document.createElement('div');
      col.className = 'col-md-4';

      const card = document.createElement('div');
      card.className = 'card h-100';

      if (article.urlToImage) {
        const img = document.createElement('img');
        img.src = article.urlToImage;
        img.className = 'card-img-top';
        img.alt = article.title || 'Article image';
        card.appendChild(img);
      }

      const body = document.createElement('div');
      body.className = 'card-body d-flex flex-column';

      const title = document.createElement('h5');
      title.className = 'card-title';
      title.textContent = article.title || '(No title)';
      body.appendChild(title);

      if (article.description) {
        const p = document.createElement('p');
        p.className = 'card-text';
        p.textContent = article.description;
        body.appendChild(p);
      }

      const meta = document.createElement('p');
      meta.className = 'text-muted small mt-auto';
      const sourceName = article.source && article.source.name ? article.source.name : 'Unknown source';
      const dateText = article.publishedAt ? new Date(article.publishedAt).toLocaleString() : '';
      meta.textContent = sourceName + (dateText ? ' – ' + dateText : '');
      body.appendChild(meta);

      if (article.url) {
        const link = document.createElement('a');
        link.href = article.url;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        link.className = 'btn btn-sm btn-primary mt-2';
        link.textContent = 'Read full article';
        body.appendChild(link);
      }

      card.appendChild(body);
      col.appendChild(card);
      resultsEl.appendChild(col);
    });
  }

  async function fetchNews(url) {
    try {
      setStatus('Loading...');
      clearResults();
      const res = await fetch(url);
      const data = await res.json();

      if (data.status === 'error') {
        setStatus('Error: ' + (data.message || 'Unknown error'));
        return;
      }
      renderArticles(data.articles);
    } catch (err) {
      console.error(err);
      setStatus('Network error');
    }
  }

  btnHeadlines.addEventListener('click', () => {
    fetchNews('news.php?action=headlines');
  });

  btnSearch.addEventListener('click', () => {
    const q = searchInput.value.trim();
    if (!q) {
      setStatus('Kindly please write what to search');
      return;
    }
    fetchNews('news.php?action=search&q=' + encodeURIComponent(q));
  });

  // Load headlines on first page load
  fetchNews('news.php?action=headlines');
</script>
</body>
</html>
