<?php


namespace App\DAO;


use App\DTO\PostDTO;

class PostDAO extends DAO
{
    // Get All the comments
    public function getAll(array $filters = null, $limit = null, $offset = null): array
    {
        $posts = [];

        // Retrieves posts
        $query = 'SELECT * FROM `post`';

        // Add filter on post's status
        if (isset($filters['is_archived'])) {
            $query .= ' WHERE is_archived = ' . $filters['is_archived'] ;
        }

        // Order post's on last date created
        $query .= ' ORDER BY `created_at` DESC';

        // Add a limit of post's to return
        if (!empty($limit)) {
            $query .= ' LIMIT ' . $limit;
        }

        // Add an offset of post's to return
        if ($offset !== null) {
            $query .= ' OFFSET ' . $offset;
        }

        // Get's data on db
        $req = $this->db->query($query);
        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($data)) {
            // Creates Posts Objects
            foreach ($data as $post) {
                $posts[] = new PostDTO($post);
            }
        }

        return  $posts;
    }

    public function count(array $filters = null): array
    {
        // Create base query - count
        $query = 'SELECT count(id) as nb FROM post';

        // Add status
        if (!empty($filters['is_archived'])) {
            $query .= ' WHERE is_archived = ' . $filters['is_archived'] ;
        }

        // Execute request
        $req =  $this->db->query($query);

        // Return data
        return $req->fetch();
    }

    // Get Post By it's id
    public function getPostById(int $postId, bool $details = false): ?PostDTO
    {
        // Retrieves post on id
        $req = $this->db->query('SELECT * FROM post WHERE id = \''.$postId.'\' LIMIT 1');
        $post = $req->fetch(\PDO::FETCH_ASSOC);

        // If post doesn't find return null
        if (empty($post)) {
            return null;
        }

        // Creates Post's Object
        $postDTO = new PostDTO($post);

        // If is set retrieves post's comments
        if ($details) {
            // Retrieves post comments
            $commentDAO = new CommentDAO();
            $comments = $commentDAO->getByPostId($postId);
            // If comments find creates post's comments array
            if (!empty($comments)) {
                $postDTO->setComments($comments);
            }
        }

        return $postDTO;
    }

    // Get Post by it's slug
    public function getPostBySlug(string $slug): ?PostDTO
    {
        // Retrieves post on id
        $req = $this->db->query("SELECT * FROM `post` WHERE slug = '$slug' LIMIT 1");
        $post = $req->fetch(\PDO::FETCH_ASSOC);

        // If post doesn't find return null
        if (empty($post)) {
            return null;
        }

        // Creates Post's Object
        $postDTO = new PostDTO($post);

        $commentDAO = new CommentDAO();

        // Create filters to get validated comments with user activated
        $filters = ['userDeactivated' => 0, 'status' => 'validated'];

        // Retrieves post's comments
        $comments = $commentDAO->getByPostId($postDTO->getId(), $filters);

        // If comments find add them to the post object
        if (!empty($comments)) {
            $postDTO->setComments($comments);
        }

        return $postDTO;
    }

    // Creates or updates a post
    public function save(PostDTO $postDTO): bool
    {
        // Checks if the post already exists
        if (!empty($postDTO->getId())) {
            // Set archived_at date to null if not a datetime
            $archivedAt = $postDTO->getArchivedAt() ? $postDTO->getArchivedAt()->format('Y-m-d H:i:s') : null;
            // Prepare the request
            $req = $this->db->prepare('UPDATE post SET title=:title, slug=:slug, subtitle=:subtitle, updated_at=:updated_at, content=:content, resume=:resume, picture=:picture, archived_at=:archived_at, is_archived=:is_archived WHERE id = '.$postDTO->getId());
            // Update the post
            $result = $req->execute(['title' => $postDTO->getTitle(), 'slug' => $postDTO->getSlug(), 'subtitle' => $postDTO->getSubtitle(), 'updated_at' => date('Y-m-d H:i:s'), 'content' => $postDTO->getContent(), 'resume' => $postDTO->getResume(), 'picture' => $postDTO->getPicture(), 'archived_at' => $archivedAt, 'is_archived' => $postDTO->getIsArchived()]);
        } else {
            // Prepare the request
            $req = $this->db->prepare('INSERT INTO `post`(`title`, `slug`, `subtitle`, `created_at`, `content`, `resume`, `picture`, `is_archived`) VALUES(:title, :slug, :subtitle, :created_at, :content, :resume, :picture, :is_archived)');
            // Update the post
            $result = $req->execute(['title' => $postDTO->getTitle(), 'slug' => $postDTO->getSlug(), 'subtitle' => $postDTO->getSubtitle(), 'created_at' => date('Y-m-d H:i:s'), 'content' => $postDTO->getContent(), 'resume' => $postDTO->getResume(), 'picture' => $postDTO->getPicture(), 'is_archived' => 0]);
        }

        return $result;
    }

    // Deletes a post and it's comments
    public function delete(PostDTO $postDTO): int
    {
        // If the post doesn't exists return null
        if (empty($postDTO->getId())) {
            return null;
        }

        // Checks if the posts got comments
        if ($postDTO->getComments()) {
            // Deletes post's comments
            foreach ($postDTO->getComments() as $commentDTO) {
                $commentDAO = new CommentDAO();
                $commentDAO->delete($commentDTO);
            }
        }

        // Deletes post
        return $this->db->exec('DELETE FROM post WHERE id = '.$postDTO->getId());
    }
}