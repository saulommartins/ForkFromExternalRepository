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
    * Página de Processamento de Manter Configuração do PPA
    * Data de Criação: 28/05/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-02.09.01
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgForm     = "FM".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

include_once( CAM_GF_PPA_MAPEAMENTO."TPPAConfiguracao.class.php"  );
include_once( CAM_GF_PPA_MAPEAMENTO."TPPAConfiguracaoNorma.class.php"  );
include_once( CAM_GF_PPA_MAPEAMENTO."TPPAConfiguracaoEncaminhamento.class.php"  );
include_once( CAM_GF_PPA_MAPEAMENTO."TPPAConfiguracaoPublicacao.class.php"  );

Sessao::setTrataExcecao( true );

$obTPPAConfiguracao = new TPPAConfiguracao;
$obTPPAConfiguracaoNorma = new TPPAConfiguracaoNorma;
$obTPPAConfiguracaoEncaminhamento = new TPPAConfiguracaoEncaminhamento;
$obTPPAConfiguracaoPublicacao = new TPPAConfiguracaoPublicacao;

$obTPPAConfiguracaoNorma->obTPPAConfiguracao = &$obTPPAConfiguracao;
$obTPPAConfiguracaoEncaminhamento->obTPPAConfiguracao = &$obTPPAConfiguracao;
$obTPPAConfiguracaoPublicacao->obTPPAConfiguracao = &$obTPPAConfiguracao;
$boPreInclusao = $_POST['stTipoInclusao']=='P';
$boErro = false;
if (!$boPreInclusao) {
   if (!$_POST['inCodNorma']) {
      SistemaLegado::exibeAviso(urlencode("Campo Norma Inválido!()"),"n_incluir","alerta");
      $boErro = true;
   } elseif ($_POST['inCodNorma'] == $_POST['inCodNormaAnterior']) {
      SistemaLegado::exibeAviso(urlencode("É necessário alterar a Norma para alterar a Configuração!"),"n_incluir","erro");
      $boErro = true;
   }
}

if (!$boErro) {
   $obTPPAConfiguracao->setDado( 'pre_inclusao'       , $boPreInclusao?'t':'f' );
   $obTPPAConfiguracao->setDado( 'ano_inicio'         , $_POST['stAnoInicio'] );
   $obTPPAConfiguracao->setDado( 'ano_final'          , $_POST['stAnoInicio']+4 );
   $obTPPAConfiguracao->inclusao();
   if ($_POST['inCodNorma']) {
      $obTPPAConfiguracaoNorma->setDado ( 'cod_norma'    , $_POST['inCodNorma'] );
      $obTPPAConfiguracaoNorma->inclusao();
   }
   if ($_POST['stDtEncaminhamento'] || $_POST['stDtDevolucao'] || $_POST['stNroProtocolo'] ||  $_POST['stPeriodicidade']) {
      $obTPPAConfiguracaoEncaminhamento->setDado ( 'dt_encaminhamento'    , $_POST['stDtEncaminhamento'] );
      $obTPPAConfiguracaoEncaminhamento->setDado ( 'dt_devolucao'    , $_POST['stDtDevolucao'] );
      $obTPPAConfiguracaoEncaminhamento->setDado ( 'nro_protocolo'    , $_POST['stNroProtocolo'] );
      $obTPPAConfiguracaoEncaminhamento->setDado ( 'periodicidade'    , $_POST['stPeriodicidade'] );
      $obTPPAConfiguracaoEncaminhamento->inclusao();
   }
   if ($_POST['inVeiculo']) {
      $obTPPAConfiguracaoPublicacao->setDado( 'cgm_veiculo_publicidade'          , $_POST['inVeiculo'] );
      $obTPPAConfiguracaoPublicacao->inclusao();
   }

   sistemaLegado::alertaAviso($pgForm, "Configuração realizada com sucesso.", "incluir", "aviso", Sessao::getId(), "../");
}
Sessao::encerraExcecao();

?>
