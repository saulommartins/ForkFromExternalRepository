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

    * $Id: OCGeraRelatorioDadosLicitacao.php 60189 2014-10-06 13:31:59Z carolina $

    * Casos de uso: uc-05.02.12

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CLA_MPDF;


$inCodEntidades =Sessao::read('inCodEntidades');
$stDataInicial= Sessao::read('stDataInicial');
$stDataFinal= Sessao::read('stDataFinal');

Sessao::read('arLicitacao');
Sessao::read('inCodLicitacao');
Sessao::read('processo');
Sessao::read('entidade');
Sessao::read('mapa');
Sessao::read('dt_licitacao');
Sessao::read('modalidade');
Sessao::read('objeto');
Sessao::read('dt_homologacao');    

$arDados['arDadosLicitacao'] = array (
                                'arLicitacao' => Sessao::read('arLicitacao')
                              , 'inCodLicitacao' => Sessao::read('inCodLicitacao') 
                              , 'processo' => Sessao::read('processo')
                              , 'entidade' => Sessao::read('entidade')
                              , 'mapa' =>Sessao::read('mapa')
                              , 'dt_licitacao' => Sessao::read('dt_licitacao')
                              , 'modalidade' => Sessao::read('modalidade')        
                              , 'tipo_objeto' => Sessao::read('tipo_objeto')
                              , 'objeto' => Sessao::read('objeto')
                              , 'dt_homologacao' => Sessao::read('dt_homologacao')
                        );


$obMPDF = new FrameWorkMPDF(3,37,4);
$obMPDF->setCodEntidades($inCodEntidades);
$obMPDF->setDataInicio($stDataInicial);
$obMPDF->setDataFinal($stDataFinal);
$obMPDF->setNomeRelatorio("Relatorio de Dados da Licitacao");
$obMPDF->setFormatoFolha("A4");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();


?>
