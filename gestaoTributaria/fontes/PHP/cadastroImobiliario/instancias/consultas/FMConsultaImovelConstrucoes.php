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
    * Página para lista de Construções do Imóvel
    * Data de Criação   : 13/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * $Id: FMConsultaImovelConstrucoes.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php";

$obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;

if ($request->get("inCodInscricao") && !$request->get("inCodCondominio")) {
    $obRCIMConstrucaoOutros->obRCIMImovel->setNumeroInscricao( $request->get("inCodInscricao") );
    $obRCIMConstrucaoOutros->setTipoVinculo                  ( "'Dependente'"              );
} else {
    $obRCIMConstrucaoOutros->obRCIMCondominio->setCodigoCondominio( $request->get("inCodCondominio") );
    $obRCIMConstrucaoOutros->setTipoVinculo                       ( "'Condomínio'"               );
}
$obRCIMConstrucaoOutros->listarConstrucoes( $rsListaConstrucao );

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo   ( "Lista de Construçõs" );
$obLista->setRecordSet( $rsListaConstrucao    );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 75 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_construcao" );
$obLista->ultimoDado->setAlinhamento( 'RIGHT' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'LEFT' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "VISUALIZAR" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:visualizarConstrucao('construcao');" );
$obLista->ultimaAcao->addCampo("1","cod_construcao"     );
$obLista->ultimaAcao->addCampo("2","inscricao_municipal");
$obLista->ultimaAcao->addCampo("3","tipo_vinculo");
$obLista->commitAcao();

$obLista->montaHTML();

$obSpnListaConstrucoes = new Span;
$obSpnListaConstrucoes->setId    ( "spnListaConstrucoes" );
$obSpnListaConstrucoes->setValue ($obLista->getHTML());

$obSpnConstrucao = new Span;
$obSpnConstrucao->setId ( "spnConstrucao" );
