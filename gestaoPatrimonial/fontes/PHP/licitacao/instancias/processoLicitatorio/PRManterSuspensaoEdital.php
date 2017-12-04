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
 * Titulo do arquivo : Arquivo de processamento da Suspensão de Edital
 * Data de Criação   : 05/12/2008

 * @author Analista      Gelson Wolowski Gonçalves
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GP_LIC_MAPEAMENTO."TLicitacaoEditalSuspenso.class.php");

$stAcao = $request->get('stAcao');
//Define o nome dos arquivos PHP
$stPrograma = "ManterSuspensaoEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {

    case "incluir":
        //inicia a transacao
        Sessao::setTrataExcecao( true );

        $obTLicitacaoEditalSuspenso = new TLicitacaoEditalSuspenso;
        Sessao::getTransacao()->setMapeamento( $obTLicitacaoEditalSuspenso );

        $stMensagem = '';
        if (!$_REQUEST['stJustificativa']) {
            $stMensagem = "Deve ser informada a justificativa da suspensão!";
        }

        $arEdital = explode('/', $_REQUEST['num_edital']);
        if (count($arEdital) <= 0) {
            $stMensagem = "Deve ser informado um edital para suspensão!";
        }

        if (!$stMensagem) {
            //exclui os possiveis registros de participante_consorcio
            $obTLicitacaoEditalSuspenso->setDado("num_edital", $arEdital[0]);
            $obTLicitacaoEditalSuspenso->setDado("exercicio", $arEdital[1]);
            $obTLicitacaoEditalSuspenso->setDado("dt_suspensao", date('d/m/Y'));
            $obTLicitacaoEditalSuspenso->setDado("justificativa", $_REQUEST['stJustificativa']);
            $obTLicitacaoEditalSuspenso->inclusao();
        }

        if (!$stMensagem) {
          SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao."","Edital Suspenso com sucesso (".$_REQUEST['num_edital'].")!","aviso","aviso", Sessao::getId(), "../");
        } else {
          SistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
        }

    break;

    case "anular":
        //inicia a transacao
        Sessao::setTrataExcecao(true);

        $obTLicitacaoEditalSuspenso = new TLicitacaoEditalSuspenso;
        Sessao::getTransacao()->setMapeamento($obTLicitacaoEditalSuspenso);

        $stMensagem = '';

        $arEdital = explode('/', $_REQUEST['num_edital']);
        if (count($arEdital) <= 0) {
            $stMensagem = "Deve ser informado um edital para suspensão!";
        }

        if (!$stMensagem) {
            //exclui os possiveis registros de participante_consorcio
            $obTLicitacaoEditalSuspenso->setDado("num_edital", $arEdital[0]);
            $obTLicitacaoEditalSuspenso->setDado("exercicio", $arEdital[1]);
            $obTLicitacaoEditalSuspenso->exclusao();
        }

        if (!$stMensagem) {
          SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao."","Anulação de Suspensão do Edital concluída com sucesso (".$_REQUEST['num_edital'].")!","aviso","aviso", Sessao::getId(), "../");
        } else {
          SistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
        }

    break;

}

//encerra a transacao
Sessao::encerraExcecao();

?>
