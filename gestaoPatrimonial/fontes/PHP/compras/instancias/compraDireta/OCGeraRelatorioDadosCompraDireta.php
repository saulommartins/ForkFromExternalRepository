<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************

    * Pagina executada no frame oculto para retornar valores para o principal
    * Data de Criação   : 01/09/2014
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @package URBEM
    * @subpackage Regra

    * $Id: OCGeraRelatorioDadosCompraDireta.php 60612 2014-11-03 20:09:42Z evandro $

    * Casos de uso: uc-05.02.12

*/
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;

$inCodEntidades = Sessao::read('inCodEntidades');
$stDataInicial  = Sessao::read('stDataInicial');
$stDataFinal    = Sessao::read('stDataFinal');
$arCompraDireta = Sessao::read('arCompraDireta');
$arDadosCompra  = Sessao::read('arDadosCompra');

$arDados['arCompra'] = array (
                                'arDadosCompra'        => Sessao::read('arDadosCompra')                       
                              , 'compraDireta'         => $arCompraDireta['cod_compra']                              
                              , 'entidade'             => $arDadosCompra[0]['entidade']
                              , 'mapa'                 => $arDadosCompra[0]['mapa']                 
                              , 'dt_compra_direta'     => $arCompraDireta['obDtCompraDireta']    
                              , 'modalidade'           => $arDadosCompra[0]['modalidade']    
                              , 'tipo_objeto'          => $arDadosCompra[0]['tipo_objeto'] 
                              , 'objeto'               => $arDadosCompra[0]['objeto'] 
                              , 'dt_entrega_proposta'  => $arCompraDireta['dt_entrega_proposta']
                              , 'dt_validade_proposta' => $arCompraDireta['dt_validade_proposta']
                              , 'condicoes_pagamento'  => $arCompraDireta['condicoes_pagamento']
                              , 'prazo_entrega'        => $arCompraDireta['prazo_entrega']
                              , 'dt_homologacao'       => $arDadosCompra[0]['dt_homologacao']
                        );

$obMPDF = new FrameWorkMPDF(3,35,5);
$obMPDF->setCodEntidades($inCodEntidades);
$obMPDF->setDataInicio($stDataInicial);
$obMPDF->setDataFinal($stDataFinal);
$obMPDF->setNomeRelatorio("Relatorio de Dados Compra Direta");
$obMPDF->setFormatoFolha("A4");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();
?>
