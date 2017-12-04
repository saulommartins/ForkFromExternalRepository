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
    * Página de Processamento de Despesas Mensais Fixas
    * Data de Criação   : 28/08/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-15 10:58:21 -0200 (Qui, 15 Fev 2007) $

    * Casos de uso: uc-02.03.29
*/

/**

$Log$
Revision 1.6  2007/02/15 12:54:37  luciano
#8397#

Revision 1.5  2006/11/16 20:31:44  gelson
Bug #7518#

Revision 1.4  2006/09/14 11:40:58  bruce
corrigindo as validações

Revision 1.3  2006/09/08 16:17:14  tonismar
alteração dos campos nro_identificacao e nro_contrato para num_identificacao e num_contrato

Revision 1.2  2006/09/04 10:29:22  tonismar
Manter Despesas Fixas Mensais

Revision 1.1  2006/09/01 17:35:03  tonismar
Manter Despesas Fixas Mensais

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TEMP."TEmpenhoDespesasFixas.class.php");
include_once(TEMP."TEmpenhoItemEmpenhoDespesasFixas.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stPrograma = "ManterDespesasMensaisFixas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

$obTEmpenhoDespesasFixas = new TEmpenhoDespesasFixas();

Sessao::getTransacao()->setMapeamento( $obTEmpenhoTipoDespesasFixas );

$obTEmpenhoDespesasFixas->obTEmpenhoItemEmpenhoDespesasFixas = new TEmpenhoItemEmpenhoDespesasFixas();

$dia = $_REQUEST['inDiaVencimento'];

switch ($stAcao) {
    case "incluir":

        if (( $dia >= 1 ) && ( $dia <= 28 )) {
            $obTEmpenhoDespesasFixas->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTEmpenhoDespesasFixas->setDado('exercicio', Sessao::getExercicio() );
            $obTEmpenhoDespesasFixas->setDado('numcgm', $_REQUEST['inCodCredor'] );
            $obTEmpenhoDespesasFixas->setDado('cod_despesa', $_REQUEST['inCodDotacao']);
            $obTEmpenhoDespesasFixas->setDado('cod_local', $_REQUEST['inCodLocal']);
            $obTEmpenhoDespesasFixas->setDado('cod_tipo', $_REQUEST['inCodTipo']);
            $obTEmpenhoDespesasFixas->setDado('num_identificacao', $_REQUEST['inIdentificacao']);
            $obTEmpenhoDespesasFixas->setDado('num_contrato', $_REQUEST['inContrato']);
            $obTEmpenhoDespesasFixas->setDado('dia_vencimento', trim($_REQUEST['inDiaVencimento']));
            $obTEmpenhoDespesasFixas->setDado('historico', $_REQUEST['stHistorico']);
            if ($_REQUEST['inCodStatus'] == 1) {
                $obTEmpenhoDespesasFixas->setDado('status', 't');
            } else {
                $obTEmpenhoDespesasFixas->setDado('status', 'f');
            }
            $obTEmpenhoDespesasFixas->setDado('dt_inicial', $_REQUEST['stDataInicial']);
            $obTEmpenhoDespesasFixas->setDado('dt_final', $_REQUEST['stDataFinal']);

            $obErro = $obTEmpenhoDespesasFixas->recuperaIdentificadorUnico($rsRecordSet);

            if (!$obErro->ocorreu()) {

                if ($rsRecordSet->getCampo('num_identificacao') == $_REQUEST['inIdentificacao']) {
                     $stMensagem = 'Já existe despesa fixa cadastrada com o número de identificação informado!';
                } elseif (!$stMensagem) {
                    $obTEmpenhoDespesasFixas->inclusao();
                }
            }
            $inCodDespesaFixa = $obTEmpenhoDespesasFixas->getDado( 'cod_despesa_fixa' );
        } else {
            $stMensagem = 'O Dia de Vencimento deve estar entre 01 e 28';
        }
        if ($stMensagem) {
            SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
        } else {
            SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=$stAcao", "Despesa Mensal Fixa: ".$inCodDespesaFixa, "incluir", "aviso", Sessao::getId(), "../");
        }
        break;
    case "alterar":

        if (( $dia >= 1 ) && ( $dia <= 28 )) {
            $obTEmpenhoDespesasFixas->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTEmpenhoDespesasFixas->setDado('exercicio', $_REQUEST['stExercicio']);
            $obTEmpenhoDespesasFixas->setDado('cod_despesa_fixa', $_REQUEST['inCodDespesaFixa']);
            $obTEmpenhoDespesasFixas->setDado('cod_despesa', $_REQUEST['inCodDotacao']);
            $obTEmpenhoDespesasFixas->setDado('numcgm', $_REQUEST['inCodCredor'] );
            $obTEmpenhoDespesasFixas->setDado('cod_local', $_REQUEST['inCodLocal']);
            $obTEmpenhoDespesasFixas->setDado('cod_tipo', $_REQUEST['inCodTipo']);
            $obTEmpenhoDespesasFixas->setDado('num_identificacao', $_REQUEST['inIdentificacao']);
            $obTEmpenhoDespesasFixas->setDado('num_contrato', $_REQUEST['inContrato']);
            $obTEmpenhoDespesasFixas->setDado('dia_vencimento', trim($_REQUEST['inDiaVencimento']));
            $obTEmpenhoDespesasFixas->setDado('historico', $_REQUEST['stHistorico']);
            if ($_REQUEST['inCodStatus'] == 1) {
                $obTEmpenhoDespesasFixas->setDado('status', 't');
            } else {
                $obTEmpenhoDespesasFixas->setDado('status', 'f');
            }
            $obTEmpenhoDespesasFixas->setDado('dt_inicial', $_REQUEST['stDataInicial']);
            $obTEmpenhoDespesasFixas->setDado('dt_final', $_REQUEST['stDataFinal']);
            $obTEmpenhoDespesasFixas->alteracao();

        } else {
            $stMensagem = 'O Dia de Vencimento deve estar entre 01 e 28';
        }

        if ($stMensagem) {
            SistemaLegado::exibeAviso(urlencode($stMensagem), "n_alterar", "erro" );
        } else {
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao&stExercicio=$stExercicio&inCodEntidade=$inCodEntidade&inCodTipo=$inCodTipo", "Despesa Mensal Fixa: ".$inCodDespesaFixa, "alterar", "aviso", Sessao::getId(), "../");
        }
        break;
    case "excluir":
        $obTEmpenhoDespesasFixas->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTEmpenhoDespesasFixas->setDado('exercicio', $_REQUEST['stExercicio']);
        $obTEmpenhoDespesasFixas->setDado('cod_despesa_fixa', $_REQUEST['inCodDespesaFixa']);
        $obTEmpenhoDespesasFixas->setDado('cod_despesa', $_REQUEST['inCodDotacao']);

        $obTEmpenhoDespesasFixas->verificaDespesaEmpenhada($boExisteEmpenho);

        if($boExisteEmpenho)
           $stMensagem = "Esta despesa não pode ser excluída, pois possui empenhos vinculados. Acesse a 'Alteração' para torná-la Inativa.";

        if ($stMensagem) {
            SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
        } else {
            $obTEmpenhoDespesasFixas->exclusao();
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao&stExercicio=$stExercicio&inCodEntidade=$inCodEntidade&inCodTipo=$inCodTipo", "Despesa Mensal Fixa: ".$inCodDespesaFixa, "excluir", "aviso", Sessao::getId(), "../");
        }

        break;
}
Sessao::encerraExcecao();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
