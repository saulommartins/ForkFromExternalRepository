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
 * Página para lista de Edificacoes do Imóvel
 * Data de Criação   : 13/06/2005

 * @author Analista: Fabio Bertoldi
 * @author Desenvolvedor: Marcelo Boezzio Paulino

 * @ignore

 * $Id: FMConsultaImovelEdificacoes.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.18
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$obRCIMEdificacao = new RCIMEdificacao;

if ($request->get("inCodInscricao") && !$request->get("inCodCondominio")) {
    $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao( $request->get("inCodInscricao") );
    $obRCIMEdificacao->boListarBaixadas = true;
    $obRCIMEdificacao->listarEdificacoesImovelConsulta( $rsListaEdificacoes );
} else {
    $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio( $request->get("inCodCondominio") );
    $obRCIMEdificacao->setTipoVinculo( 'Condomínio' );
    $obRCIMEdificacao->listarEdificacoes( $rsListaEdificacoes );
}

    $table = new Table;

    $table->setRecordset($rsListaEdificacoes);
    $table->setSummary('Lista de edificações');
    $table->Head->addCabecalho( 'Código'             , 10 );
    $table->Head->addCabecalho( 'Tipo de Unidade'    , 45 );
    $table->Head->addCabecalho( 'Tipo de Edificação' , 45 );

    $table->Body->addCampo ('[cod_construcao]' , 'L');
    $table->Body->addCampo ('[tipo_vinculo]'   , 'L');
    $table->Body->addCampo ('[nom_tipo]'       , 'L');

    $table->Body->addAcao( "visualizar" , "visualizarConstrucao(%s, %d, %d, %s)", array( 'edificacao', 'cod_construcao', 'inscricao_municipal', 'tipo_vinculo' ) );

    $table->montaHTML();

    $obSpnListaEdificacoes = new Span;
    $obSpnListaEdificacoes->setId    ( "spnListaEdificacoes" );
    $obSpnListaEdificacoes->setValue ($table->getHtml());

    $obSpnEdificacao = new Span;
    $obSpnEdificacao->setId ( "spnEdificacao" );
