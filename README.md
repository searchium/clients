## Searchium API clients
=======

PHP and Python clients for the [searchium API](https://searchium.com/site/docs)

### Example PHP

    include 'searchium.php';
    $s = new SearchiumClient('public', 'YmZlODc3YmIyZWUzNWQ3NGZmNDIyZmQzNjJkMjMwYTBkMGUwMTgxOQ');
    $doc = array('author' => 'John Doe',
           'title' => 'PHP client example',
           'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
           'url' => 'http://domain.com/'.rand(0,1000),
           'date' => date('Y-m-d\TH:i:s\Z'));
    $docid = $s->save($doc);
    
    
### Example Python

    from searchium import Client
    s = Client('public', 'YmZlODc3YmIyZWUzNWQ3NGZmNDIyZmQzNjJkMjMwYTBkMGUwMTgxOQ')
    doc = {'author': 'John Doe',
           'title': 'Python client example',
           'content': 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
           'url': 'http://domain.com/link',
           'date': '2012-05-18'}
    docid = s.save(doc)