<?php

use MyDramLibrary\Catalog\API\ISBNOpenlibrary;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ISBNOpenlibraryTest extends TestCase {

    private Client $stubGuzzleClient;
    private string $uri;
    private string $isbn;
    private string $missingIsbn;
    private string $incorrectIsbn = '9780980200441';
    private array $mockResponses;

    final public function setUp() : void {
        $this->stubGuzzleClient = $this->createStub(Client::class);
        $this->uri = 'https://openlibrary.org/api/books?bibkeys=ISBN:9780980200447&jscmd=details&format=json';
        $this->isbn = '9780980200447';
        $this->missingIsbn = '9788325325473';
        $this->mockResponses = [
            200 => '{"ISBN:9780980200447": {"bib_key": "ISBN:9780980200447", "info_url": "https://openlibrary.org/books/OL22853304M/Slow_reading", "preview": "borrow", "preview_url": "https://archive.org/details/slowreading00mied", "thumbnail_url": "https://covers.openlibrary.org/b/id/5546156-S.jpg", "details": {"number_of_pages": 92, "table_of_contents": [{"level": 0, "label": "", "title": "The personal nature of slow reading", "pagenum": ""}, {"level": 0, "label": "", "title": "Slow reading in an information ecology", "pagenum": ""}, {"level": 0, "label": "", "title": "The slow movement and slow reading", "pagenum": ""}, {"level": 0, "label": "", "title": "The psychology of slow reading", "pagenum": ""}, {"level": 0, "label": "", "title": "The practice of slow reading.", "pagenum": ""}], "contributors": [{"role": "Cover Photographs", "name": "C. Ekholm"}], "isbn_10": ["1936117363"], "covers": [5546156], "lc_classifications": ["Z1003 .M58 2009"], "ocaid": "slowreading00mied", "weight": "1 grams", "source_records": ["marc:marc_loc_updates/v37.i01.records.utf8:4714764:907", "marc:marc_loc_updates/v37.i24.records.utf8:7913973:914", "marc:marc_loc_updates/v37.i30.records.utf8:11406606:914", "ia:slowreading00mied", "marc:marc_openlibraries_sanfranciscopubliclibrary/sfpl_chq_2018_12_24_run04.mrc:135742902:2094", "marc:marc_loc_2016/BooksAll.2016.part35.utf8:160727336:914"], "title": "Slow reading", "languages": [{"key": "/languages/eng"}], "subjects": ["Books and reading", "Reading"], "publish_country": "mnu", "by_statement": "by John Miedema.", "oclc_numbers": ["297222669"], "type": {"key": "/type/edition"}, "physical_dimensions": "7.81 x 5.06 x 1 inches", "publishers": ["Litwin Books"], "description": "\"A study of voluntary slow reading from diverse angles\"--Provided by publisher.", "physical_format": "Paperback", "key": "/books/OL22853304M", "authors": [{"key": "/authors/OL6548935A", "name": "John Miedema"}], "publish_places": ["Duluth, Minn"], "pagination": "80p.", "classifications": {}, "lccn": ["2008054742"], "notes": "Includes bibliographical references and index.", "identifiers": {"amazon": ["098020044X"], "google": ["4LQU1YwhY6kC"], "librarything": ["8071257"], "goodreads": ["6383507"]}, "isbn_13": ["9780980200447", "9781936117369"], "dewey_decimal_class": ["028/.9"], "local_id": ["urn:sfpl:31223095026424"], "publish_date": "March 2009", "works": [{"key": "/works/OL13694821W"}], "latest_revision": 23, "revision": 23, "created": {"type": "/type/datetime", "value": "2009-01-07T22:16:11.381678"}, "last_modified": {"type": "/type/datetime", "value": "2020-12-20T07:22:28.904545"}}}}',
            404 => '/isbn/9788325325473.json does not exist.'
        ];
    }

    private function createResponseClient(int $responseCode) : Client {
        $mock = new MockHandler([
            new Response($responseCode, [], $this->mockResponses[$responseCode])
        ]);
        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    public function testAPIInstanceCreatedWithGuzzleClientStub() {
        $this->assertInstanceOf('MyDramLibrary\Catalog\API\ISBNOpenlibrary', new ISBNOpenlibrary($this->stubGuzzleClient, $this->isbn));
    }

    public function testAPIInstanceCreatedWithGuzzleClientInside() {
        $this->assertInstanceOf('MyDramLibrary\Catalog\API\ISBNOpenlibrary', new ISBNOpenlibrary(null, $this->isbn));
    }

    public function testThrowsValidatorExceptionWhenCreatingWithIncorrectISBN() {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $this->assertInstanceOf('MyDramLibrary\Catalog\API\ISBNOpenlibrary', new ISBNOpenlibrary($this->stubGuzzleClient, $this->incorrectIsbn));
    }

    public function testThrowExceptionIfSettingUpUriAlreadySetUp() {
        $this->expectException('Exception');
        $isbnAPI = new ISBNOpenlibrary($this->stubGuzzleClient, $this->isbn);
        $isbnAPI->setURI($this->uri);
    }

    public function testReturnDefinedUri() {
        $isbnAPI = new ISBNOpenlibrary($this->stubGuzzleClient, $this->isbn);
        $this->assertEquals($isbnAPI->getURI(), $this->uri);
    }

    public function testThrowExceptionIfSettingInvalidISBN() {
        $this->expectException('Exception');
        new ISBNOpenlibrary($this->stubGuzzleClient, 'not isbn');
    }

    public function testThrowExceptionIfSettingUpMethod() {
        $this->expectException('Exception');
        $isbnAPI = new ISBNOpenlibrary($this->stubGuzzleClient, $this->isbn);
        $isbnAPI->setMethod('GET');
    }

    public function testReturnGetAsMethod() {
        $isbnAPI = new ISBNOpenlibrary($this->stubGuzzleClient, $this->isbn);
        $this->assertEquals('GET', $isbnAPI->getMethod());
    }

    public function testThrowExceptionIfSendingRequestMoreThanOnce() {
        $this->expectException('Exception');
        $isbnAPI = new ISBNOpenlibrary($this->createResponseClient(200), $this->isbn);
        $isbnAPI->send();
        $isbnAPI->send();
    }

    public function testReturnCode404WhenRequestingMissingResource() {
        $isbnAPI = new ISBNOpenlibrary($this->createResponseClient(404), $this->missingIsbn);
        $this->assertEquals(404, $isbnAPI->getResponseCode());
    }

    public function testReturnCode200WhenRequestingExistingResource() {
        $isbnAPI = new ISBNOpenlibrary($this->createResponseClient(200), $this->isbn);
        $this->assertEquals(200, $isbnAPI->getResponseCode());
    }

    public function testReturnExpectedContentOnExistingResource() {
        $isbnAPI = new ISBNOpenlibrary($this->createResponseClient(200), $this->isbn);
        $this->assertEquals($isbnAPI->getResponseContent(), $this->mockResponses[200]);
    }

    public function testSendReturnTrueOn200Request() {
        $isbnAPI = new ISBNOpenlibrary($this->createResponseClient(200), $this->isbn);
        $this->assertEquals(true, $isbnAPI->send());
    }

    public function testSendReturnFalseOn404Request() {
        $isbnAPI = new ISBNOpenlibrary($this->createResponseClient(404), $this->isbn);
        $this->assertEquals(false, $isbnAPI->send());
    }

    public function testReturnCode200OnProperResourceWithSend() {
        $isbnAPI = new ISBNOpenlibrary($this->createResponseClient(200), $this->isbn);
        $isbnAPI->send();
        $this->assertEquals(200, $isbnAPI->getResponseCode());
    }

    public function tearDown() : void {
    }

}