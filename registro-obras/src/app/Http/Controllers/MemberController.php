<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SADAIC\Integration;

class MemberController extends Controller
{
    protected $sadaic;

    public function __construct(Integration $sadaic)
    {
        $this->middleware('auth:members');
        $this->sadaic = $sadaic;
    }

    public function index()
    {
        return view('member.home');
    }

    public function showPerformanceReport()
    {
        $response = $this->sadaic->embed(
            "ctacorriente.ida.list.php",
            ".general-texto"
        );

        return view('sadaic.generic-embed', [
            'title'   => 'Informe de Actuación',
            'content' => $response
        ]);
    }

    public function showInternationalPerformanceReport()
    {
        $response = $this->sadaic->embed(
            "ctacorriente.formulario.exterior.php",
            ["#texto_introduccion", "#fomrulario"]
        );

        return view('sadaic.international-performance', [
            'title'   => 'Informe de Actuación Internacional',
            'content' => $response
        ]);
    }

    public function showPaymentLetters()
    {
        $response = $this->sadaic->embed(
            "cartas.de.pago.php",
            ".general-texto"
        );

        return view('sadaic.generic-embed', [
            'title'   => 'Cartas de Pago',
            'content' => $response
        ]);
    }

    public function showPaymentOrders()
    {
        $response = $this->sadaic->embed(
            "ctacorriente.formulario.cuenta.php",
            ".general-texto"
        );

        return view('sadaic.generic-embed', [
            'title'   => 'Órdenes de Pago',
            'content' => $response
        ]);
    }

    public function showPaymentRequest()
    {
        $response = $this->sadaic->embed(
            "cartas.de.pago.php",
            ".general-texto"
        );

        return view('sadaic.generic-embed', [
            'title'   => 'Solicitudes de Pago',
            'content' => $response
        ]);
    }

    public function showStatus()
    {
        $response = $this->sadaic->embed(
            "ctacorriente.hitlist.php?subarea=hitlist",
            ".general-texto"
        );

        return view('sadaic.generic-embed', [
            'title'   => 'Estado de cuenta',
            'content' => $response
        ]);
    }

    public function showWorkList()
    {
        $idSocio = session("sadaic.member_id");

        $response = $this->sadaic->embed(
            "obras.php?codigo=$idSocio&a=9ZmbL1CR208BB0Y",
            ".general-texto"
        );

        return view('sadaic.generic-embed', [
            'title'   => 'Mis Obras',
            'content' => $response
        ]);
    }
}
