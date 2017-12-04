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
    * Data de Criação   : 10/10/2014

    * @author Analista: Silvia Martins
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPECgmTipoCredor.class.php';


//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoCredor";
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
        $obTTCEPECgmTipoCredor = new TTCEPECgmTipoCredor();

        if(!$obErro->ocorreu()){
            
          $arCGMTipoCredor =Sessao::read('arCGMTipoCredor');
          $obErro = $obTTCEPECgmTipoCredor->recuperaTodos($rsCgmTipoCredor);

          if (is_array($arCGMTipoCredor) && count($arCGMTipoCredor) > 0) {
          
             if($rsCgmTipoCredor->getNumLinhas()>0){
              
              foreach( $rsCgmTipoCredor->getElementos() AS $arCredor ){
                $obTTCEPECgmTipoCredor->setDado("exercicio" ,$arCredor['exercicio']);
                $obTTCEPECgmTipoCredor->setDado("cgm_credor",$arCredor['cgm_credor']);
                $obErro = $obTTCEPECgmTipoCredor->exclusao($boTransacao);
              }
            }
             

            foreach( $arCGMTipoCredor AS $arCredor ){
              $obTTCEPECgmTipoCredor->setDado("exercicio"              ,Sessao::getExercicio());
              $obTTCEPECgmTipoCredor->setDado("cgm_credor"             ,$arCredor['cgm_credor']);
              $obTTCEPECgmTipoCredor->setDado("cod_tipo_credor"        ,$arCredor['cod_tipo_credor']);
              $obTTCEPECgmTipoCredor->recuperaPorChave($rsRecordSet, $boTransacao);
              
              if ($rsRecordSet->eof()) {
                  $obErro = $obTTCEPECgmTipoCredor->inclusao($boTransacao);
              } else {
                  $obErro = $obTTCEPECgmTipoCredor->alteracao($boTransacao);
              }
            }

          } else {
            if($rsCgmTipoCredor->getNumLinhas()>0){
              foreach( $rsCgmTipoCredor->getElementos() AS $arCredor ){
                $obTTCEPECgmTipoCredor->setDado("exercicio" ,$arCredor['exercicio']);
                $obTTCEPECgmTipoCredor->setDado("cgm_credor",$arCredor['cgm_credor']);
                $obErro = $obTTCEPECgmTipoCredor->exclusao($boTransacao);
              }
            }
          }
          
          if(!$obErro->ocorreu()){
              $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEPECgmTipoCredor);
              SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao."&modulo=".$stModulo,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
          }else{
              SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
          }
        }
    
  break;
}

?>
