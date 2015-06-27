<?php

namespace SuperAwesome\Blog\Domain\Model\Post;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use SuperAwesome\Blog\Domain\Model\Post\Event\PostWasCategorized;
use SuperAwesome\Blog\Domain\Model\Post\Event\PostWasCreated;
use SuperAwesome\Blog\Domain\Model\Post\Event\PostWasPublished;
use SuperAwesome\Blog\Domain\Model\Post\Event\PostWasTagged;
use SuperAwesome\Blog\Domain\Model\Post\Event\PostWasUncategorized;
use SuperAwesome\Blog\Domain\Model\Post\Event\PostWasUntagged;

class Post extends EventSourcedAggregateRoot
{
    /** @var string */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $content;

    /** @var string */
    private $category;

    /** @var bool[] */
    private $tags = [];

    /** @var string */
    private $status;

    private function __construct()
    {}

    public static function create($id)
    {
        $instance = new static();
        $instance->apply(new PostWasCreated($id));
        return $instance;
    }

    public static function instantiateForReconstitution()
    {
        return new static();
    }

    protected function applyPostWasCreated(PostWasCreated $event)
    {
        $this->id = $event->id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return array_keys($this->tags);
    }

    /**
     * Publish a post.
     *
     * @param $title
     * @param $content
     * @param $category
     */
    public function publish($title, $content, $category)
    {
        if ( $title == $this->title && $content == $this->content && $category == $this->category)
        {
            return;
        }

        if ( $this->category && $category != $this->category )
        {
            $this->apply(
                new PostWasUncategorized(
                    $this->id,
                    $category
                )
            );
        }

        if ( $this->category != $category )
        {
            $this->apply(new PostWasCategorized(
                $this->id,
                $category
            ));
        }

        $this->apply(
            new PostWasPublished(
                $this->id,
                $title,
                $content,
                $category
            )
        );
    }

    protected function applyPostWasUntagged(PostWasUntagged $event)
    {
        unset($this->tags[$event->tag]);
    }

    protected function applyPostWasPublished(PostWasPublished $event)
    {
        $this->title = $event->title;
        $this->content = $event->content;
        $this->category = $event->category;
    }

    /**
     * Tag a post.
     *
     * @param string $tag
     */
    public function addTag($tag)
    {
        if (isset($this->tags[$tag])) {
            return;
        }

        $this->apply(new PostWasTagged($this->id, $tag));
    }

    protected function applyPostWasTagged(PostWasTagged $event)
    {
        $this->tags[$event->tag] = true;
    }

    /**
     * Untag a post.
     *
     * @param string $tag
     */
    public function removeTag($tag)
    {
        if (!isset($this->tags[$tag])) {
            return;
        }

        $this->apply(
            new PostWasUntagged(
                $this->id, $tag
            )
        );
    }

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return (string) $this->id;
    }
}
