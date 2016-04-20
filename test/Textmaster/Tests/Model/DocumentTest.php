<?php

namespace Textmaster\Tests\Model;

use Textmaster\Model\Document;
use Textmaster\Model\DocumentInterface;
use Textmaster\Model\ProjectInterface;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    protected $clientMock;

    public function setUp()
    {
        parent::setUp();

        $projectId = '654321';
        $showValues = array(
            'id' => '123456',
            'title' => 'Document 1',
            'status' => DocumentInterface::STATUS_IN_CREATION,
            'original_content' => 'Text to translate.',
            'instructions' => 'Translating instructions.',
            'project_id' => '654321',
        );
        $updateValues = array(
            'id' => '123456',
            'title' => 'New Title',
            'status' => DocumentInterface::STATUS_IN_REVIEW,
            'original_content' => 'Text to translate.',
            'instructions' => 'Translating instructions.',
            'project_id' => $projectId,
        );
        $completeValues = array(
            'id' => '123456',
            'title' => 'New Title',
            'status' => DocumentInterface::STATUS_COMPLETED,
            'original_content' => 'Text to translate.',
            'instructions' => 'Translating instructions.',
            'project_id' => $projectId,
        );
        $projectValues = array(
            'id' => $projectId,
            'name' => 'Project 1',
            'status' => ProjectInterface::STATUS_IN_CREATION,
            'ctype' => ProjectInterface::ACTIVITY_TRANSLATION,
            'language_from' => 'fr',
            'language_to' => 'en',
            'category' => 'C014',
            'project_briefing' => 'Lorem ipsum...',
            'options' => array('language_level' => 'premium'),
        );

        $clientMock = $this->getMock('Textmaster\Client', array('projects'));
        $projectApiMock = $this->getMock('Textmaster\Api\Project', array('documents', 'show'), array($clientMock));
        $documentApiMock = $this->getMock('Textmaster\Api\Document', array('show', 'update', 'complete'), array($clientMock, $projectId), '', false);

        $clientMock->method('projects')
            ->willReturn($projectApiMock);

        $projectApiMock->method('documents')
            ->willReturn($documentApiMock);

        $projectApiMock->method('show')
            ->willReturn($projectValues);

        $documentApiMock->method('show')
            ->willReturn($showValues);

        $documentApiMock->method('update')
            ->willReturn($updateValues);

        $documentApiMock->method('complete')
            ->willReturn($completeValues);

        $this->clientMock = $clientMock;
    }

    /**
     * @test
     */
    public function shouldCreateEmpty()
    {
        $title = 'Document 1';
        $originalContent = 'Text to translate.';
        $instructions = 'Translating instructions.';
        $customData = array('Custom data can be any type');

        $document = new Document($this->clientMock);
        $document
            ->setTitle($title)
            ->setOriginalContent($originalContent)
            ->setInstructions($instructions)
            ->setCustomData($customData)
        ;

        $this->assertNull($document->getId());
        $this->assertEquals(DocumentInterface::STATUS_IN_CREATION, $document->getStatus());
        $this->assertEquals($title, $document->getTitle());
        $this->assertEquals($originalContent, $document->getOriginalContent());
        $this->assertEquals($instructions, $document->getInstructions());
        $this->assertEquals($customData, $document->getCustomData());
    }

    /**
     * @test
     */
    public function shouldCreateFromValues()
    {
        $projectId = '654321';
        $values = array(
            'id' => '123456',
            'title' => 'Document 1',
            'status' => DocumentInterface::STATUS_IN_CREATION,
            'original_content' => 'Text to translate.',
            'instructions' => 'Translating instructions.',
            'project_id' => $projectId,
        );

        $document = new Document($this->clientMock, $values);

        $this->assertEquals('123456', $document->getId());
        $this->assertEquals('Document 1', $document->getTitle());
        $this->assertEquals(DocumentInterface::STATUS_IN_CREATION, $document->getStatus());
        $this->assertEquals('Text to translate.', $document->getOriginalContent());
        $this->assertEquals('Translating instructions.', $document->getInstructions());
    }

    /**
     * @test
     */
    public function shouldCreateToLoad()
    {
        $projectId = '654321';
        $valuesToCreate = array(
            'id' => '123456',
            'project_id' => $projectId,
        );

        $document = new Document($this->clientMock, $valuesToCreate);

        $this->assertEquals('123456', $document->getId());
        $this->assertEquals('Document 1', $document->getTitle());
        $this->assertEquals(DocumentInterface::STATUS_IN_CREATION, $document->getStatus());
        $this->assertEquals('Text to translate.', $document->getOriginalContent());
        $this->assertEquals('Translating instructions.', $document->getInstructions());
    }

    /**
     * @test
     */
    public function shouldUpdate()
    {
        $projectId = '654321';
        $valuesToCreate = array(
            'id' => '123456',
            'project_id' => $projectId,
        );

        $document = new Document($this->clientMock, $valuesToCreate);
        $this->assertEquals('Document 1', $document->getTitle());

        $document->setTitle('New Title');
        $document->save();

        $this->assertEquals('New Title', $document->getTitle());
    }

    /**
     * @test
     */
    public function shouldGetProject()
    {
        $projectId = '654321';
        $valuesToCreate = array(
            'id' => '123456',
            'project_id' => $projectId,
        );

        $document = new Document($this->clientMock, $valuesToCreate);
        $project = $document->getProject();

        $this->assertEquals('Textmaster\Model\Project', get_class($project));
        $this->assertEquals($projectId, $project->getId());
    }

    /**
     * @test
     */
    public function shouldSetArrayOriginalContent()
    {
        $originalContent = array(
            'translation1' => array('original_phrase' => 'Text to translate.'),
            'translation2' => array('original_phrase' => 'An other text to translate.'),
        );

        $document = new Document($this->clientMock);
        $document->setOriginalContent($originalContent);

        $this->assertEquals($originalContent, $document->getOriginalContent());
    }

    /**
     * @test
     */
    public function shouldCountWords()
    {
        $originalContent = array(
            'translation1' => array('original_phrase' => 'Text 1 to translate.'),
            'translation2' => array('original_phrase' => 'An other text to translate.'),
        );

        $document = new Document($this->clientMock);
        $document->setOriginalContent($originalContent);

        $this->assertEquals(9, $document->getWordCount());

        $document->setOriginalContent('A single phrase to translate.');

        $this->assertEquals(5, $document->getWordCount());
    }

    /**
     * @test
     */
    public function shouldSetCallback()
    {
        $callback = array(
            DocumentInterface::STATUS_CANCELED => array('url' => 'http://my.host/canceled_callback'),
            DocumentInterface::STATUS_IN_REVIEW => array('url' => 'http://my.host/in_review_callback'),
        );

        $document = new Document($this->clientMock);
        $document->setCallback($callback);

        $this->assertEquals($callback, $document->getCallback());
    }

    /**
     * @test
     */
    public function shouldComplete()
    {
        $projectId = '654321';
        $valuesToCreate = array(
            'id' => '123456',
            'project_id' => $projectId,
        );

        $document = new Document($this->clientMock, $valuesToCreate);
        $document->save();
        $document->complete(DocumentInterface::SATISFACTION_POSITIVE, 'Good job!');

        $this->assertEquals(DocumentInterface::STATUS_COMPLETED, $document->getStatus());
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\InvalidArgumentException
     */
    public function shouldNotSetBadArrayOriginalContent()
    {
        $originalContent = array(
            'translation1' => array('original_phrase' => 'Text to translate.'),
            'translation2' => array('bad_key' => 'An other text to translate.'),
        );

        $document = new Document($this->clientMock);
        $document->setOriginalContent($originalContent);
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\ObjectImmutableException
     */
    public function shouldBeImmutable()
    {
        $projectId = '654321';
        $valuesToCreate = array(
            'id' => '123456',
            'project_id' => $projectId,
        );

        $document = new Document($this->clientMock, $valuesToCreate);
        $document->save();
        $document->setTitle('Change title on immutable');
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\InvalidArgumentException
     */
    public function shouldNotSetWrongCallback()
    {
        $callback = array(
            'NOT_A_DOCUMENT_STATUS' => array('url' => 'http://my.host/bad_callback'),
        );

        $document = new Document($this->clientMock);
        $document->setCallback($callback);
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\InvalidArgumentException
     */
    public function shouldNotCompleteWithWrongSatisfaction()
    {
        $projectId = '654321';
        $valuesToCreate = array(
            'id' => '123456',
            'project_id' => $projectId,
        );

        $document = new Document($this->clientMock, $valuesToCreate);
        $document->save();
        $document->complete('Wrong satisfaction value');
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\BadMethodCallException
     */
    public function shouldNotCompleteNotInReview()
    {
        $projectId = '654321';
        $valuesToCreate = array(
            'id' => '123456',
            'project_id' => $projectId,
        );

        $document = new Document($this->clientMock, $valuesToCreate);
        $document->complete();
    }
}
