#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib2
import hashlib
import json


class Client:

    def __init__(self, bucket, api_key):
        self._bucket = bucket
        self._key = api_key
        self._apiurl = 'https://api.searchium.com/' + bucket + '/'
        self._searchurl = 'http://s.searchium.com/' + bucket + '/?q='
        self.error = ''

    def save(self, doc, id=''):
        """
        Saves the document received as (dict) parameter
        """
        try:
            data = json.dumps(doc)
            sig = self.signature(data)
            url = self._apiurl + 'save/' + str(id) + '?signature=' + sig
            response = self.send_request(url, data)
            if response and response.get('ok'):
                return response.get('id')
        except:
            pass
        return False

    def get(self, id):
        """
        Gets document from DB by ID
        """
        try:
            sig = self.signature(id)
            url = self._apiurl + 'get/' + str(id) + '?signature=' + sig
            response = self.send_request(url)
            if response and response.get('ok'):
                return response.get('doc')
        except:
            pass
        return False

    def delete(self, id):
        """
        Deletes document from DB by ID
        """
        try:
            sig = self.signature(id)
            url = self._apiurl + 'delete/' + str(id) + '?signature=' + sig
            response = self.send_request(url)
            if response and response.get('ok'):
                return True
        except:
            pass
        return False

    def search(self, query, fields=None):
        """
        Runs provided search query, fields to retrieve (coma separated) are optional
        """
        try:
            if fields:
                url = self._searchurl + query + '&fields=' + fields
            else:
                url = self._searchurl + query

            response = self.send_request(url)
            if response and response.get('ok'):
                return response
        except:
            pass
        return False

    def signature(self, data):
        """
        Calculates the signature for the petition
        """
        return hashlib.sha1(data + self._key).hexdigest()

    def send_request(self, url, data=None):
        """
        Sends HTTP request to searchium API servers
        """
        try:
            req = urllib2.Request(url, data)
            handler = urllib2.urlopen(req)
            response = json.load(handler)
            if response['ok']:
                return response
        except urllib2.HTTPError, e:
            self.error = 'Error code: ' + str(e.code)
        except urllib2.URLError, e:
            self.error = 'Failed to reach a server' + str(e.reason)
        except Exception:
            self.error = 'Unknown error'
        return False


if __name__ == '__main__':
    s = Client('public', 'YmZlODc3YmIyZWUzNWQ3NGZmNDIyZmQzNjJkMjMwYTBkMGUwMTgxOQ')
    doc = {'author': 'John Doe',
           'title': 'Python client example',
           'content': 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
           'url': 'http://domain.com/link',
           'date': '2012-05-18'}
    docid = s.save(doc)

    if docid:
        print 'Document saved with ID: ' + docid
        print 'Fetching back...'
        newdoc = s.get(docid)
        print newdoc
        print 'Deleting... ',
        if s.delete(docid):
            print "done"
        else:
            print 'error : ' + s.error
    else:
        print 'error : ' + s.error
