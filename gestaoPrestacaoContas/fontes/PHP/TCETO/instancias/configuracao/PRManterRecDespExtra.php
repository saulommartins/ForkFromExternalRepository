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
    * Pacote de configuração do TCETO - Processamento Configurar Receita/Despesa Extra
    * Data de Criação   : 07/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: PRManterRecDespExtra.php 60671 2014-11-07 13:27:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTTOPlanoAnaliticaClassificacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterRecDespExtra";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$obMapeamento = new TTTOPlanoAnaliticaClassificacao();
Sessao::getTransacao()->setMapeamento( $obMapeamento );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($_REQUEST['stAcao']) {
  default:
    $obMapeamento->setDado('exercicio',Sessao::getExercicio());
    
    foreach ($_REQUEST as $stKey => $stValue) {
      if (strstr($stKey,'inCodigoClassificacao') ) {
        $arCodigo = explode('_',$stKey); //Formato: inCodigoClassificacao_1
        $obMapeamento->setDado('cod_plano',$arCodigo[1]);
        $obMapeamento->setDado('cod_classificacao',$stValue);
        $obMapeamento->recuperaPorChave($rsRecordSet);
        
        if ($stValue != '') {
          if ($rsRecordSet->eof()) {
            $obMapeamento->inclusao();
          } else {
            $obMapeamento->alteracao();
          }
        } else {
          $obMapeamento->exclusao();
        }
      }
    }
    
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
  break;
}

Sessao::encerraExcecao();
?>