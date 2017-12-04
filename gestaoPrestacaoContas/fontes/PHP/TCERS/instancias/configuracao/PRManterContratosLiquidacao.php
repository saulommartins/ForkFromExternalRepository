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
    * Página Formulário - Parâmetros do Arquivo RDEXTRA.
    * Data de Criação   : 14/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 12203 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 20:51:50 +0000 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.04
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCERS_MAPEAMENTO.'TTCERSContratosLiquidacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterContratosLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obErro = new Erro();

$arLista = Sessao::read('sessaoLista');

switch ($stAcao) {
    default:
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obTTCERSContratosLiquidacao = new TTCERSContratosLiquidacao();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu) {
            $obTTCERSContratosLiquidacao->excluirTodos($boTransacao);
            
            foreach ($arLista as $indice => $dados) {
                $obTTCERSContratosLiquidacao->setDado('cod_liquidacao', $dados['inLiquidacao']);
                $obTTCERSContratosLiquidacao->setDado('cod_contrato', $dados['inContrato']);
                $obTTCERSContratosLiquidacao->setDado('cod_contrato_tce', $dados['inContratoTCE']);
                $obTTCERSContratosLiquidacao->setDado('exercicio', $dados['stAno']);
                $obTTCERSContratosLiquidacao->recuperaPorChave($rsTTCERSContratosLiquidacao, $boTransacao);
                
                if ($rsTTCERSContratosLiquidacao->getNumLinhas() < 0) {
                    $obErro = $obTTCERSContratosLiquidacao->inclusao($boTransacao);
                } else {
                    $obErro = $obTTCERSContratosLiquidacao->alteracao($boTransacao);
                }
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Confiduração dos Contratos na Liquidação","incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

        $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCERSContratosLiquidacao);

    break;
}

?>
