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
  * Página de processamento para Configurar IDE
  * Data de criação : 07/01/2014

  * @author Analista:    Eduardo Paculski Schitz
  * @author Programador: Franver Sarmento de Moraes

  * @ignore

  $Id: PRManterConfiguracaoEMP.php 61709 2015-02-26 19:05:09Z carlos.silva $
  $Date: $
  $Author: $
  $Rev: $

  $Rev$:
  $Author$:
  $Date$:

  **/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoEMP.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasModalidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoEMP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');


$obErro = new Erro;
switch ($stAcao) {
    case 'manter':
        $arListaEmpenho          = Sessao::read('arListaEmpenho');
        $obTransacao             = new Transacao;
        
        $obTTCEMGConfiguracaoEMP = new TTCEMGConfiguracaoEMP();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        $obErro = $obTTCEMGConfiguracaoEMP->excluirTodos($boTransacao);

        if(count($arListaEmpenho) > 0) {
            $obErro->setDescricao('É necessário adicionar ao menos um empenho a lista antes de salvar');
        }
        
        if (!$obErro->ocorreu) {
            foreach ($arListaEmpenho as $arEmpenho) {
                $obTTCEMGConfiguracaoEMP->setDado('exercicio'          , $arEmpenho['stExercicio']);
                $obTTCEMGConfiguracaoEMP->setDado('cod_entidade'       , $arEmpenho['inCodEntidade']);
                $obTTCEMGConfiguracaoEMP->setDado('cod_empenho'        , $arEmpenho['inCodEmpenho']);
                $obTTCEMGConfiguracaoEMP->setDado('exercicio_licitacao', $arEmpenho['stExercicioLicitacao']);
                $obTTCEMGConfiguracaoEMP->setDado('cod_licitacao'      , $arEmpenho['inCodLicitacao']);
                $obTTCEMGConfiguracaoEMP->setDado('cod_modalidade'     , $arEmpenho['inCodModalidade']);
                           
                $obErro = $obTTCEMGConfiguracaoEMP->inclusao($boTransacao);
            }
            
            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Configuração EMP","incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoEMP);
        }

        break;
}
