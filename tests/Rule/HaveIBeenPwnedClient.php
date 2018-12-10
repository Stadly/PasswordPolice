<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\MockObject\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class HaveIBeenPwnedClient implements ClientInterface
{
    /**
     * {@inheritDoc}
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $generator = new Generator();

        $stream = $generator->getMock(StreamInterface::class);

        // SHA1 prefix 00000
        $stream->method('getContents')->willReturn(implode("\r\n", [
            '0005AD76BD555C1D6D771DE417A4B87E4B4:4',    // bkltqsmvy9
            '000A8DAE4228F821FB418F59826079BF368:2',    // 1397wpfk
            '000DD7F2A1C68A35673713783CA390C9E93:630',  // 89378305686
            '001E225B908BAC31C56DB04D892E47536E0:5',    // 64769480a
            '006BAB7FC3113AA73DE3589630FC08218E7:2',    // 6597812222
            '008CD1806EB7B9B46A8F87690B2AC16F617:3',    // 553193251
            '00A0E3B9F25FF41DE4B5AC238C2D545C7A8:15',   // 6004468405
            '00A1D4B746FAA3FD526FF6D5BC8052FDB38:16',   // +79250455754
            '00CAEF405439D57847A8657218C618160B2:15',   // k65jlnzrvtu9
         // '00FC1C08E6454BED24F463EA2129E254D43:40',   // 291vnnzrvtu9
        ]));

        /**
         * @var MockObject&ResponseInterface
         */
        $response = $generator->getMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }
}
