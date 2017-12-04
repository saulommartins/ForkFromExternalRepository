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
    * Página de Processamento para o cadastro de Desdobramento da Receita
    * Data de Criação   : 15/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: PRManterDesdobramentoReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeDesdobramentoReceita.class.php";

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesdobramentoReceita";
$pgFilt     = "FL".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

$arReceitasSecundarias = Sessao::read('arReceitasSecundarias');

switch ($stAcao) {
    case "desdobrar":
       $obROrcamentoReceita = new ROrcamentoReceita;
       $obROrcamentoReceita->setCodReceita( $_REQUEST["inCodigoReceitaPrincipal"] );
       $obROrcamentoReceita->setExercicio ( Sessao::getExercicio() );
       $obRContabilidadeDesdobramentoReceita = new RContabilidadeDesdobramentoReceita( $obROrcamentoReceita );
       foreach ($arReceitasSecundarias as $arReceitaSecundaria) {
           $obRContabilidadeDesdobramentoReceita->addReceitaSecundaria();
           $obRContabilidadeDesdobramentoReceita->roUltimaReceitaSecundaria->setCodReceita( $arReceitaSecundaria["cod_receita"] );
           $obRContabilidadeDesdobramentoReceita->roUltimaReceitaSecundaria->setPercentualDesdobramento( $arReceitaSecundaria["percentual"] );
       }
       $obErro = $obRContabilidadeDesdobramentoReceita->salvar();
       if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso($pgFilt,"Desdobrar receita concluído com sucesso!( Código Receita: ".$_REQUEST["inCodigoReceitaPrincipal"]." )","cc","aviso", Sessao::getId(), "../");
       } else {
           SistemaLegado::exibeAviso("Erro ao desdobrar receita!( ".urlencode($obErro->getDescricao())." )","cc","erro");
       }
       break;
}
?>
