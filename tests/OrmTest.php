<?php

declare(strict_types=1);

namespace Tests;

use App\Database\ChildModel;
use App\Database\NoteModel;
use App\Database\ParentModel;

class ItemsTest extends BaseTest
{
    use WithMigrations;

    protected $parent;
    protected $child;
    protected $notes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpMigrations();

        // $this->db->getDriver()->setLogger(new TestLogger());

        $this->parent = new ParentModel();
        $this->parent->id = 100;
        $this->parent->name = 'Parent';

        $this->child = new ChildModel();
        $this->child->id = 101;
        $this->child->name = 'Child';

        $this->parent->child = $this->child;

        $note1 = new NoteModel();
        $note1->text = 'Note 1';

        $note2 = new NoteModel();
        $note2->text = 'Note 2';

        $this->notes = [$note1, $note2];
    }

    /** @test */
    public function testCreated()
    {
        $this->transaction()->persist($this->parent)->run();

        $this->assertNotNull($this->parent->version);
    }

    public function testUpdated()
    {
        $this->transaction()->persist($this->parent)->run();
        $this->clean();

        /** @var ParentModel $parent */
        $parent = $this->orm->getRepository(ParentModel::class)->findByPK($this->parent->version);

        $parent->name = 'Parent 2';

        /**
         * FIXME New version of `$parent` can't be saved because
         * 'unchanged' child_id is not being added to the columns list for insert
         */
        $this->transaction()->persist($parent)->run();

        $this->assertNotEquals($parent->version, $this->parent->version);
    }

    public function testDoesntUpdateUnchangedData()
    {
        $this->db->getDriver()->setLogger(new TestLogger());

        $this->transaction()->persist($this->parent)->run();

        $oldVersion = $this->parent->version;
        $this->parent->name = 'Parent';

        $this->transaction()->persist($this->parent)->run();

        $this->assertEquals($oldVersion, $this->parent->version);
    }

    public function testUpdateIfRelationChange()
    {
        $this->transaction()->persist($this->parent)->run();

        $this->child->name = 'Child 2';

        $this->transaction()->persist($this->child)->run();

        $this->clean();

        /** @var ParentModel $parent */
        $parent = $this->orm->getRepository(ParentModel::class)->findByPK($this->parent->version);

        /** @var ChildModel $child */
        $child = $this->orm->getRepository(ChildModel::class)->findByPK($this->child->version);

        $this->assertNotEquals($parent->child->version, $child->version);

        $parent->child = $child;
        $this->transaction()->persist($parent)->run();

        $this->assertEquals($parent->child->version, $parent->child->version);
    }

    public function testUpdateHasManyIfDataChange()
    {
        foreach ($this->notes as $note) {
            $this->parent->notes->add($note);
        }

        $this->transaction()->persist($this->parent)->run();

        $this->parent->name = 'Parent 2';

        $this->transaction()->persist($this->parent)->run();

        $this->clean();

        /** @var ParentModel $parent */
        $parent = $this->orm->getRepository(ParentModel::class)->findByPK($this->parent->version);

        /**
         * FIXME Normally, updating parent shouldn't create new versions of HasMany.
         * But in this case I'm exploring a use case for fully-versioned entities.
         *
         * Think of `notes` as a collection of random values that could well be stored in json field
         * instead of child relations.
         */
        $this->assertEquals(count($this->notes), count($parent->notes->toArray()));
    }

    public function testUpdateIfHasManyChange()
    {
        $this->parent->notes->add($this->notes[0]);
        $this->transaction()->persist($this->parent)->run();
        $this->clean();

        /** @var ParentModel $parent */
        $parent = $this->orm->getRepository(ParentModel::class)->findByPK($this->parent->version);
        $parent->notes->add($this->notes[1]);

        /**
         * FIXME New version of `$parent` can't be saved because
         * 'unchanged' child_id is not being added to the columns list for insert
         */
        $this->transaction()->persist($parent)->run();
        $this->clean();

        /**
         * FIXME New version of `$updated` must contain both $item1 and $item2.
         */
        /** @var ParentModel $parent */
        $updated = $this->orm->getRepository(ParentModel::class)->findByPK($parent->version);
        count($updated->notes->toArray());

        $this->assertEquals(2, count($updated->notes->toArray()));
    }

    public function testDeleted()
    {
        $this->transaction()->persist($this->parent)->run();
        $version = $this->parent->version;

        /**
         * On delete, we create a versioned 'delete' record incrementing PK and
         * marking it deleted with non-empty `deleted_at`
         *
         * FIXME, Currently failing because delete commands doesn't have relation fields.
         */
        $this->transaction()->delete($this->parent)->run();

        $this->assertNotNull($this->parent->deleted_at);
        $this->assertNotEquals($this->parent->version, $version);
    }
}
