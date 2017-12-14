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
  * Página de processamento para Configurar LOA
  * Data de criação : 21/01/2015

  * @author Analista:    
  * @author Programador: Lisiane Morais

  * @ignore
  *
  * $Id: $
  * $Date: $
  * $Author: $
  * $Rev: $
  *
***/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TCMGOConfiguracaoLOA.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoLOA";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obErro = new Erro();
switch ($stAcao) {
    case 'manter':
        $boFlagTransacao = false;
        $obTransacao = new Transacao();
        $obTCMGOConfiguracaoLOA = new TCMGOConfiguracaoLOA();
        $rsTCMGOConfiguracaoLOA = new RecordSet();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        if (!$obErro->ocorreu()) {
            $obTCMGOConfiguracaoLOA->setDado('exercicio'                             , Sessao::getExercicio()                           );
            $obTCMGOConfiguracaoLOA->setDado('cod_norma'                             , $request->get('hdnCodNorma')                     );
            $obTCMGOConfiguracaoLOA->setDado('percentual_suplementacao'              , $request->get('nuPorSuplementacao')              );
            $obTCMGOConfiguracaoLOA->setDado('percentual_credito_interna'            , $request->get('nuPorCreditoInterna')             );
            $obTCMGOConfiguracaoLOA->setDado('percentual_credito_antecipacao_receita', $request->get('nuPorCreditoAntecipacaoReceita')  );

            $obTCMGOConfiguracaoLOA->recuperaPorChave($rsTCMGOConfiguracaoLOA,$boTransacao);

            if ($rsTCMGOConfiguracaoLOA->getNumLinhas() < 0) {
                $obErro = $obTCMGOConfiguracaoLOA->inclusao($boTransacao);
            } else {
                $obErro = $obTCMGOConfiguracaoLOA->alteracao($boTransacao);
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTCMGOConfiguracaoLOA);
        }
        break;
}
?>
