<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Traits\ActingJWTUser;

class TopicApiTest extends TestCase
{
    use ActingJWTUser;

    /**
     * 当前登录用户
     *
     * @var User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    private $user;


    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testStoreTopic()
    {
        $data = ['category_id' => 1, 'title' => 'test title', 'body' => 'test body'];


        // 发送请求
        $response = $this->JWTActingAs($this->user)->json('POST', 'api/v1/topics', $data);

        $matchable = [
            'user_id' => $this->user->id,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'body' => clean($data['body'], 'user_topic_body'),
        ];

        // 验证结果
        $response->assertStatus(201)->assertJsonFragment($matchable);
    }

    public function testUpdateTopic()
    {
        // 原来的Topic
        $topic = $this->makeTopic();

        // 新的值
        $editTopic = [
            'category_id' => 2,
            'body' => 'edited body',
            'title' => 'edited body',
        ];

        // 发送请求
        $response = $this->JWTActingAs($this->user)
            ->json('PATCH', 'api/v1/topics/' . $topic->id, $editTopic);

        // 验证请求
        $assertData = [
            'category_id' => $editTopic['category_id'],
            'body' => clean($editTopic['body'], 'user_topic_body'),
            'title' => $editTopic['title'],
            'user_id' => $this->user->id
        ];

        $response->assertStatus(200)
            ->assertJsonFragment($assertData);
    }

    /**
     * 创建话题
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|Topic
     */
    public function makeTopic()
    {
        return factory(Topic::class)->create([
            'category_id' => 1,
            'user_id' => $this->user->id,
        ]);
    }

    public function testTopicShow()
    {
        $topic = $this->makeTopic();

        $response = $this->json('GET', 'api/v1/topics/' . $topic->id);

        $assertData = [
            'category_id' => $topic->category_id,
            'body' => $topic->body,
            'title' => $topic->title,
            'user_id' => $topic->user_id,
        ];

        $response->assertStatus(200)
            ->assertJsonFragment($assertData);
    }

    public function testTopicIndex()
    {
        $response = $this->json('GET', 'api/v1/topics');

        $response->assertStatus(200)->assertJsonStructure(['data', 'meta']);
    }

    public function testDestroyTopic()
    {
        $topic = $this->makeTopic();

        $response = $this->JWTActingAs($this->user)
            ->json('DELETE', 'api/v1/topics/' . $topic->id);

        $response->assertStatus(204);


        // 验证删除的结果
        $secondResponse = $this->JWTActingAs($this->user)
            ->json('GET', 'api/v1/topics/' . $topic->id);
        $secondResponse->assertStatus(404);
    }
}

