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
  * Página de Processamento de Configuração PERC
  * Data de Criação   : 17/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: PRManterConfiguracaoPERC.php 59612 2014-09-02 12:00:51Z gelson $
  *
  * $Revision: 59612 $
  * $Author: gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoPERC.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoPERC";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao   = $request->get('stAcao');

$obErro = new Erro;
switch ($stAcao) {
    case 'incluir':
        $obTransacao = new Transacao;
        $obTTCEMGConfiguracaoPERC = new TTCEMGConfiguracaoPERC();
        $rsTTCEMGConfiguracaoPERC = new RecordSet();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($request->get('flPercentualEstabelecido') == '') {
            $flPercentualEstabelecido = 0.00;
        } else {
            $flPercentualEstabelecido = $request->get('flPercentualEstabelecido');
        }

        if (!$obErro->ocorreu) {
            $obTTCEMGConfiguracaoPERC->setDado('exercicio'         , Sessao::getExercicio());
            $obTTCEMGConfiguracaoPERC->setDado('planejamento_anual', $request->get('stPercentualAnual'));
            $obTTCEMGConfiguracaoPERC->setDado('porcentual_anual'  , $flPercentualEstabelecido);
            $obTTCEMGConfiguracaoPERC->recuperaPorChave($rsTTCEMGConfiguracaoPERC,$boTransacao);

            if ($rsTTCEMGConfiguracaoPERC->getNumLinhas() < 0) {
                $obErro = $obTTCEMGConfiguracaoPERC->inclusao($boTransacao);
            } else {
                $obErro = $obTTCEMGConfiguracaoPERC->alteracao($boTransacao);
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Confiduração PERC","incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoPERC);
        }

        break;
}
?>
