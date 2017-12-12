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
*/
?>
<?php
/**
    * Página para lista de Processos do Lote
    * Data de Criação   : 10/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * $Id: FMConsultaImovelLoteListaProcessos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.4  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Lista de Processos" );
$obLista->setRecordSet( $rsListaProcesso );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Hora" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_processo_ano" );
$obLista->ultimoDado->setAlinhamento( 'CENTER' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "data" );
$obLista->ultimoDado->setAlinhamento( 'CENTER' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "hora" );
$obLista->ultimoDado->setAlinhamento( 'CENTER' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "VISUALIZAR" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:visualizarProcessoLote( 'visualizarProcessoLote' );" );
$obLista->ultimaAcao->addCampo("1","cod_processo");
$obLista->ultimaAcao->addCampo("2","timestamp");
$obLista->ultimaAcao->addCampo("3","cod_lote");
$obLista->ultimaAcao->addCampo("4","ano_exercicio");
$obLista->commitAcao();

$obLista->montaHTML();

$obSpnProcesso = new Span;
$obSpnProcesso->setId ( "spnProcesso" );
$obSpnProcesso->setValue ($obLista->getHTML());

$obSpnAtributosProcesso = new Span;
$obSpnAtributosProcesso->setId ( "spnAtributosProcesso" );
