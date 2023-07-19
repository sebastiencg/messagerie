<?php

namespace App\Test\Controller;

use App\Entity\Friend;
use App\Repository\FriendRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FriendControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private FriendRepository $repository;
    private string $path = '/friend/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Friend::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Friend index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'friend[validity]' => 'Testing',
            'friend[ofUser1]' => 'Testing',
            'friend[ofUser2]' => 'Testing',
        ]);

        self::assertResponseRedirects('/friend/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Friend();
        $fixture->setValidity('My Title');
        $fixture->setOfUser1('My Title');
        $fixture->setOfUser2('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Friend');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Friend();
        $fixture->setValidity('My Title');
        $fixture->setOfUser1('My Title');
        $fixture->setOfUser2('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'friend[validity]' => 'Something New',
            'friend[ofUser1]' => 'Something New',
            'friend[ofUser2]' => 'Something New',
        ]);

        self::assertResponseRedirects('/friend/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getValidity());
        self::assertSame('Something New', $fixture[0]->getOfUser1());
        self::assertSame('Something New', $fixture[0]->getOfUser2());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Friend();
        $fixture->setValidity('My Title');
        $fixture->setOfUser1('My Title');
        $fixture->setOfUser2('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/friend/');
    }
}
