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
  * Página de processamento para prorrogacao de vencimentos
  * Data de criação : 16/02/2007

  * @author Analista: Fabio Bertold Rodrigues
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRProrrogarVencimentos.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.1  2007/02/16 12:38:31  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelaProrrogacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ProrrogarVencimentos";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";

$boExec = false;
if ($_REQUEST["boEmitirCarnes"]) {
    $boExec = true;
}

$obTARRParcela = new TARRParcela;
$obTARRParcelaProrrogacao = new TARRParcelaProrrogacao;

$stFiltro = "";
$stFiltroReemissao = "";
if ($_REQUEST["stExercicio"]) {
    $stFiltro .= " AND ac.exercicio = ".$_REQUEST["stExercicio"];
    $stFiltroReemissao .= "&inExercicio=".$_REQUEST["stExercicio"];
}

if ($_REQUEST["inCodGrupo"]) {
    $arDados = explode( "/", $_REQUEST["inCodGrupo"] );
    $stFiltro .= " AND acgc.cod_grupo = ".$arDados[0]." AND acgc.ano_exercicio = ".$arDados[1];
    $stFiltroReemissao .= "&inCodGrupo=".$_REQUEST["inCodGrupo"];
}

if ($_REQUEST["inCodCredito"]) {
    $arDados = explode( ".", $_REQUEST["inCodCredito"] );
    $stFiltro .= " AND ac.cod_credito = ".$arDados[0]." AND ac.cod_especie = ".$arDados[1]." AND ac.cod_natureza = ".$arDados[3]." AND ac.cod_genero = ".$arDados[2];
    $stFiltroReemissao .= "&inCodCredito=".$_REQUEST["inCodCredito"];
}

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTARRParcela );

    $obTARRParcela->recuperaListaProrrogacao( $rsListaProrrogar, $stFiltro );
    while ( !$rsListaProrrogar->Eof() ) {
        $obTARRParcelaProrrogacao->setDado('cod_parcela', $rsListaProrrogar->getCampo("cod_parcela") );
        $obTARRParcelaProrrogacao->setDado('vencimento_anterior', $rsListaProrrogar->getCampo("vencimento") );
        $obTARRParcelaProrrogacao->inclusao();

        $obTARRParcela->setDado('cod_parcela', $rsListaProrrogar->getCampo("cod_parcela") );
        $obTARRParcela->setDado('vencimento', $_REQUEST["inDataVencimento"] );
        $obTARRParcela->alteracao();

        $rsListaProrrogar->proximo();
    }

Sessao::encerraExcecao();

if ($boExec) {
    Sessao::write( "link", "" );

    SistemaLegado::alertaAviso( "LSEmitirCarne.php?".Sessao::getId()."&stAcao=incluir".$stFiltroReemissao, "Prorrogação de vencimento efetuado com sucesso", "incluir", "aviso", Sessao::getId(), "../" );
} else {
    SistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=incluir", "Prorrogação de vencimento efetuado com sucesso", "incluir", "aviso", Sessao::getId(), "../" );
}
