<?php

namespace Recca0120\Upload\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class Plupload extends Base
{
    /**
     * receive.
     *
     * @param string $inputName
     *
     * @throws \Recca0120\Upload\Exceptions\ChunkedResponseException
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected function doReceive($inputName)
    {
        $uploadedFile = $this->request->file($inputName);
        $chunks = $this->request->get('chunks');

        if (empty($chunks) === true) {
            return $uploadedFile;
        }

        $chunk = $this->request->get('chunk');

        return $this->receiveChunkedFile(
            $this->request->get('name'),
            $uploadedFile->getPathname(),
            $chunk * $this->request->header('content-length'),
            $uploadedFile->getMimeType(),
            $chunk >= $chunks - 1
        );
    }

    /**
     * completedResponse.
     *
     * @method completedResponse
     *
     * @param Illuminate\Http\JsonResponse $response
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function completedResponse(JsonResponse $response)
    {
        $data = $response->getData();
        $response->setData([
            'jsonrpc' => '2.0',
            'result' => $data,
        ]);

        return $response;
    }
}
