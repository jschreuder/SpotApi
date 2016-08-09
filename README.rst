SpotApi
=======

.. image:: https://secure.travis-ci.org/WebspotCode/SpotApi.png
   :target: http://travis-ci.org/WebspotCode/SpotApi
   :alt: Build status
.. image:: https://scrutinizer-ci.com/g/WebspotCode/SpotApi/badges/quality-score.png?b=master
   :target: https://scrutinizer-ci.com/g/WebspotCode/SpotApi/?branch=master
   :alt: Scrutinizer Code Quality
.. image:: https://scrutinizer-ci.com/g/WebspotCode/SpotApi/badges/coverage.png?b=master
   :target: https://scrutinizer-ci.com/g/WebspotCode/SpotApi/?branch=master
   :alt: Code Coverage

There are many tasks for which a literal MVC implementation isn't ideal. The
three main ingredients are still very much present here though: the domain
layer (Model), the presentational layer (View) and application flow
(Controller) are still very much present.

But instead of modeling any of these directly, this package attempts to model
it more conceptually and acknowledge the fact that PHP applications are
basically HTTP request handlers.

The Application
---------------

When you go into the ``Application`` class you'll see the execution runs through
these three stages:

**1. HTTP Request mapping (routing & request validation)**

In the first stage the HTTP Request object (a PSR-7 ServerRequest) is mapped to
a ``Spot\Api\Application\Request\RequestInterface`` implementation. At this
stage the first bit of routing is being done by choosing the Request's name.
This is also where basic input validation should be done: does the request
adhere to the way you are expecting the request data to be given, and any
filtering and casts should also be done here. This will allow stage 2 to have a
lot cleaner code.

**2. Request Executor (controller)**

This is where the bulk of the work should be done. Domain-manipulation is only
allowed in here, and should never be done in either the first or third stage.
If you've done validated the input already in the first stage, you can start
working with the input data immediately without any further checks.

The result should be a ``Spot\Api\Application\Response\ResponseInterface``
implementation with data necessary for output. It should not do any formatting
of that data, or retrieve things that are necessary for output but not for
executing the request.

**3. Response Generator (view)**

This is where we generate the output. Any related information necessary can be
retrieved, database calls may be done. The basics are provided by the Response
object, what is done here is deciding on how to represent it in a PSR-7
Response message.

This is probably often a JSON or XML object as that is what this is meant to
enable, but HTML is a distinct possibility as well.

**How this all fits together visually**

.. image:: docs/img/schematic.png

RequestInterface & ResponseInterface messages
---------------------------------------------

The Application expects a HTTP request to be mapped to a
``Spot\Api\Application\Request\RequestInterface`` instance, which is executed
to result in a ``Spot\Api\Application\Response\ResponseInterface`` which in
turn will be used to generate a HTTP response. These messages consist of at
least a name, a content-type and attributes. They also implement the
``ArrayAccess`` interface to allow direct access to their attributes.

How to handle multiple use-cases: buses
---------------------------------------

In the examples above it shows how this works, and works well for single
use-cases. But if you want this to handle more than one request you may want to
use a bus. For each of the three stages a bus has been implemented and is fully
compatible with a single-use-case implementation of the layer.

**HttpRequestParserBus**

This bus is the most complex, as it uses a FastRouter instance to map a PSR7
message to a native Request implementing the ``HttpRequestParserInterface``.
You can register paths & HTTP methods with it and map them to names from your
DiC that must create valid ``HttpRequestParserInterface`` instances. This bus
will then delegate the actual parsing to that instance.

**ExecutorBus & GeneratorBus**

These work similar to the HttpRequestParserBus but use simple direct matches
instead of a router. They take the Request or Response object's name and check
if it is known and leads to a valid name from your DiC which in turn should
create valid ``ExecutorInterface`` or ``GeneratorInterface`` instances.

While more complex applications might benefit from routers in these buses, for
most use-cases these simple ones will suffice.

More to come...
---------------

[...]

License
-------

All code is licensed under the MIT license below, unless noted otherwise within
the file. Only free licenses similar to MIT are used.

    Copyright (c) 2015 Jelmer Schreuder

    Permission is hereby granted, free of charge, to any person obtaining a
    copy of this software and associated documentation files (the "Software"),
    to deal in the Software without restriction, including without limitation
    the rights to use, copy, modify, merge, publish, distribute, sublicense,
    and/or sell copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
    DEALINGS IN THE SOFTWARE.
