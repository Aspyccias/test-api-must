<?php


namespace App\Controller;


use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * @var Manager
     */
    private Manager $fractal;
    private int $statusCode;

    /**
     * ApiController constructor.
     * @param Manager $fractal
     */
    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
        $this->fractal->setSerializer(new ArraySerializer());
        $this->statusCode = 200;
    }

    /**
     * @param int $statusCode
     * @return $this
     */
    protected function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function noContentResponse(string $message = '')
    {
        return $this->setStatusCode(Response::HTTP_NO_CONTENT)
            ->respondWithArray($message);
    }

    /**
     * @param $item mixed The object to transform
     * @param $callback mixed The transformer to call
     * @return JsonResponse
     */
    protected function respondWithItems($item, $callback)
    {
        $resource = new Item($item, $callback);
        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * @param mixed $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithArray($data, array $headers = []): JsonResponse
    {
        if ($data !== '' && !isset($data['error'])) {
            $data = ['data' => $data];
        }

        return $this->json($data, $this->statusCode, $headers);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function errorInternalError(string $message = 'Unexpected error')
    {
        return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function errorNotFound(string $message = 'Resource not found')
    {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function errorBadRequest(string $message = 'Invalid request')
    {
        return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function respondWithError(string $message): JsonResponse
    {
        return $this->respondWithArray(['error' => $message]);
    }
}
