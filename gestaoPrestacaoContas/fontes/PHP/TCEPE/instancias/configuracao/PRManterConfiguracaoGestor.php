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
    * 
    * Data de Criação   : 26/09/2014

    * @author Analista: Silvia Martins
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: PRManterConfiguracaoGestor.php 60205 2014-10-06 21:06:16Z lisiane $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEConfiguracaoGestor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoGestor";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];


switch ($_REQUEST['stAcao']) {
  default:
     $obErro = new Erro();
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $obTTCEPEConfiguracaoGestor = new TTCEPEConfiguracaoGestor();

        if(!$obErro->ocorreu()){
            
          $arGestores = Sessao::read('arGestores');

          if (is_array($arGestores) && count($arGestores) > 0) {
             $obTTCEPEConfiguracaoGestor->setDado("exercicio",Sessao::getExercicio());
             $obTTCEPEConfiguracaoGestor->setDado("cod_entidade",$request->get('hdnCodEntidade'));
             $obTTCEPEConfiguracaoGestor->setDado("num_orgao",$_REQUEST['inMontaCodOrgaoM']);
             $obTTCEPEConfiguracaoGestor->setDado("num_unidade",$_REQUEST['inMontaCodUnidadeM']);
             $obErro = $obTTCEPEConfiguracaoGestor->exclusao($boTransacao);

            foreach( $arGestores AS $arGestor ){
              $obTTCEPEConfiguracaoGestor->setDado("exercicio"              ,Sessao::getExercicio());
              $obTTCEPEConfiguracaoGestor->setDado("cod_entidade"           ,$arGestor['cod_entidade']);
              $obTTCEPEConfiguracaoGestor->setDado("cgm_gestor"             ,$arGestor['cgm_gestor']);
              $obTTCEPEConfiguracaoGestor->setDado("num_unidade"            ,$_REQUEST['inMontaCodUnidadeM']);
              $obTTCEPEConfiguracaoGestor->setDado("num_orgao"              ,$_REQUEST['inMontaCodOrgaoM']);
              $obTTCEPEConfiguracaoGestor->setDado("dt_inicio_vigencia"     ,$arGestor['dt_inicio_vigencia']);
              $obTTCEPEConfiguracaoGestor->setDado("dt_fim_vigencia"        ,$arGestor['dt_fim_vigencia']);
              $obTTCEPEConfiguracaoGestor->recuperaPorChave($rsRecordSet, $boTransacao);
              
              if ($rsRecordSet->eof()) {
                  $obErro = $obTTCEPEConfiguracaoGestor->inclusao($boTransacao);
              } else {
                  $obErro = $obTTCEPEConfiguracaoGestor->alteracao($boTransacao);
              }
            }

          } else {
              $obTTCEPEConfiguracaoGestor->setDado("exercicio",Sessao::getExercicio());
              $obTTCEPEConfiguracaoGestor->setDado("cod_entidade",$request->get('hdnCodEntidade'));
              $obTTCEPEConfiguracaoGestor->setDado("num_orgao",$_REQUEST['inMontaCodOrgaoM']);
              $obTTCEPEConfiguracaoGestor->setDado("num_unidade",$_REQUEST['inMontaCodUnidadeM']);
              $obErro = $obTTCEPEConfiguracaoGestor->exclusao($boTransacao);
          }
          
          if(!$obErro->ocorreu()){
              $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEPEConfiguracaoGestor);
              SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao."&modulo=".$stModulo,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
          }else{
              SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
          }
        }
    
  break;
}

?>
