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
/*
    * Página Oculto para publicação do contrato
    * Data de Criação   : 10/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * $Id: PRManterPublicacaoContrato.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.23
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContrato.class.php"                                 );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoPublicacaoContrato.class.php"                       );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stPrograma = "ManterPublicacaoContrato";
$pgForm = "FM".$stPrograma.".php";
Sessao::setTrataExcecao( true );

$arValores = Sessao::read('arValores');
$inCountValores = count($arValores);
switch ($stAcao) {

 case "incluir":

   if (count($inCountValores)> 0) {
     $rsRecordSetItem       = new RecordSet;
     $obTContrato           = new TLicitacaoContrato();
     $obTContrato->setDado('num_contrato', $_REQUEST['inContrato']);
     $obTContrato->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
     $obTContrato->setDado('exercicio', Sessao::getExercicio());
     $obTContrato->recuperaPorChave( $rsContrato );

     $obTPublicacaoContrato = new TLicitacaoPublicacaoContrato();
     $obTPublicacaoContrato->setDado('num_contrato',$_REQUEST['inContrato']);
     $obTPublicacaoContrato->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
     $obTPublicacaoContrato->setDado('exercicio',Sessao::getExercicio());
     $obTPublicacaoContrato->exclusao();
     for ($inPosTransf = 0; $inPosTransf < $inCountValores; $inPosTransf++) {

       $obTPublicacaoContrato->setDado('cod_licitacao',$rsContrato->getCampo('cod_licitacao'));
       $obTPublicacaoContrato->setDado('cod_modalidade',$rsContrato->getCampo('cod_modalidade'));
       $obTPublicacaoContrato->setDado('cod_entidade',$rsContrato->getCampo('cod_entidade'));
       $obTPublicacaoContrato->setDado('exercicio',Sessao::getExercicio());
       $obTPublicacaoContrato->setDado('num_contrato',$rsContrato->getCampo('num_contrato'));
       $obTPublicacaoContrato->setDado('numcgm',$arValores[$inPosTransf]["inVeiculo"]);
       $obTPublicacaoContrato->setDado('dt_publicacao',$arValores[$inPosTransf]["dtDataVigencia"]);
       $obTPublicacaoContrato->setDado('observacao', $arValores[$inPosTransf]["stObservacao"]);

       $obTPublicacaoContrato->inclusao();
     }
   } else {
     $stMensagem = 'Deve existir ao menos um item na lista.';
   }
   if (!$stMensagem) {
    SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$_REQUEST['HdnCodContrato'],"incluir", "aviso", Sessao::getId(),"");
   } else {
    SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
   }
   Sessao::encerraExcecao();
 break;
}
