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
    * Página de formulário para alteração de características de construção
    * Data de Criação   : 05/04/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Lucas Leusin Oigen

    * @ignore

    * $Id: FMManterConstrucaoCaracteristicaAbaProcesso.php 63279 2015-08-12 13:11:09Z arthur $

    * Casos de uso: uc-05.01.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//COMPONENTES PARA A ABA PROCESSOS
$obLblCodigoConstrucao = new Label;
$obLblCodigoConstrucao->setName     ( "inCodigoConstrucao" );
$obLblCodigoConstrucao->setRotulo   ( "Código" );
$obLblCodigoConstrucao->setValue    ( $obRCIMConstrucao->getCodigoConstrucao() );

$obLblNomeCondominio = new Label;
$obLblNomeCondominio->setName      ( "stNomeCondominio" );
$obLblNomeCondominio->setRotulo    ( "Condomínio" );
$obLblNomeCondominio->setValue     ( $_REQUEST["stNomeCond"] );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setName      ( "inNumeroInscricao" );
$obLblNumeroInscricao->setRotulo    ( "Inscrição Imobiliária" );
$obLblNumeroInscricao->setTitle     ( "Inscrição imobiliária com a qual a edificação está vinculada" );
$obLblNumeroInscricao->setValue     ( $_REQUEST["inNumeroInscricao"] );

$obLblDescricaoConstrucao = new Label;
$obLblDescricaoConstrucao->setName      ( "stDescricaoConstrucao" );
$obLblDescricaoConstrucao->setRotulo    ( "Descrição" );
$obLblDescricaoConstrucao->setValue     ( $obRCIMConstrucao->getDescricao() );

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

$obLista->montaHTML();

$obSpnProcesso = new Span;
$obSpnProcesso->setId ( "spnProcesso" );
$obSpnProcesso->setValue ($obLista->getHTML());

$obSpnAtributosProcesso = new Span;
$obSpnAtributosProcesso->setId ( "spnAtributosProcesso" );

?>