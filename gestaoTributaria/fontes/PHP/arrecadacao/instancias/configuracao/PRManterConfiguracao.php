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
    * Página de Processamento ConfiguracaoARR
    * Data de Criação   : 11/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: PRManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $
*/

/*
$Log$
Revision 1.14  2007/09/25 14:50:36  vitor
Ticket#10246#

Revision 1.13  2007/07/19 15:44:07  cercato
Bug #9687#

Revision 1.12  2007/02/16 11:40:41  dibueno
Bug #8432#

Revision 1.11  2007/02/16 10:11:50  dibueno
Inclusão de opção de Baixa Manual Única

Revision 1.10  2006/10/23 17:41:36  fabio
adicionado grupo de credito para escrituracao de receita

Revision 1.9  2006/09/15 11:02:28  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

//include("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgForm2 = "FM".$stPrograma."Grupos.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRARRConfiguracao = new RARRConfiguracao;
$obErro  = new Erro;

switch ($stAcao) {
    case "alterar":

        if ( !$obErro->ocorreu() ) {
            $obRARRConfiguracao->setBaixaManual                 ( $_REQUEST["stBaixaManual"]                    );
            $obRARRConfiguracao->setBaixaManualUnica            ( $_REQUEST["stBaixaManualUnica"]               );
            $obRARRConfiguracao->setBaixaManualDAVencida        ( $_REQUEST["stPrazoReceberDA"]                 );
            $obRARRConfiguracao->setValorMaximo                 ( $_REQUEST["flValorMaximo"]                    );
            $obRARRConfiguracao->setMinimoLancamentoAutomatico  ( $_REQUEST["flValorMinimoLacamentoAutomatico"] );
            $obRARRConfiguracao->setFormaVerificacao            ( $_REQUEST["stFormaVerificacao"]               );
//          $obRARRConfiguracao->setValTransfImovel             ( $_REQUEST["stValTransfImovel"]                );
            $obRARRConfiguracao->setConvenioParcelamento        ( $_REQUEST["inNumConvenio"]                    );
            $obRARRConfiguracao->setSuspensao                   ( $_REQUEST["stSupensao"]                       );
            $obRARRConfiguracao->setEmissaoCarne                ( $_REQUEST["stEmissaoCarne"]                   );
            $obRARRConfiguracao->setCodFebraban                 ( $_REQUEST["inCodFEBRABAN"]                    );
            $obRARRConfiguracao->setCarneSecretaria             ( $_REQUEST["stCarneSecretaria"]                );
            $obRARRConfiguracao->setCarneDepartamento           ( $_REQUEST["stCarneDepartamento"]              );
            $obRARRConfiguracao->setCarneDam                    ( $_REQUEST["stCarneDam"]                       );
            $obRARRConfiguracao->setNotaAvulsa                  ( $_REQUEST["stNotaAvulsa"]                     );
            $obRARRConfiguracao->setQtdViasNotaAvulsa           ( $_REQUEST["cmbViasNota"]                      );
            $obRARRConfiguracao->setEmissaoCarneIsento          ( $_REQUEST["stEmissaoCarneIsento"]             );
            $obRARRConfiguracao->setFundLegal                   ( $_REQUEST["inCodNorma"]                       );
            $obErro = $obRARRConfiguracao->salvar();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Configuração","alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        break;

    case "alterarGrupos":
        if ( !$obErro->ocorreu() ) {
            $obRARRConfiguracao->setCodigoGrupoDiferencaAcrescimoEcon  ( $_REQUEST["inNumLancAutAcrEcoCredito"]  );
            $obRARRConfiguracao->setCodigoGrupoDiferencaAcrescimoImob  ( $_REQUEST["inNumLancAutAcrImobCredito"] );
            $obRARRConfiguracao->setCodigoGrupoDiferencaAcrescimoGeral ( $_REQUEST["inNumLancAutAcrGeralCredito"]);
            $obRARRConfiguracao->setCodigoGrupoDiferencaEcon           ( $_REQUEST["inNumLancAutEcoCredito"]     );
            $obRARRConfiguracao->setCodigoGrupoDiferencaImob           ( $_REQUEST["inNumLancAutImoCredito"]     );
            $obRARRConfiguracao->setCodigoGrupoDiferencaGeral          ( $_REQUEST["inNumLancAutGerCredito"]     );
            $obRARRConfiguracao->setCodigoGrupoCreditoITBI             ( $_REQUEST["inNumCredito"]               );
            $obRARRConfiguracao->setCodigoGrupoCreditoIPTU             ( $_REQUEST["inNumCreditoIPTU"]           );
            $obRARRConfiguracao->setCodigoGrupoCreditoEscrituracao     ( $_REQUEST["inNumGrupoEscrituracao"]     );
            $obRARRConfiguracao->setCodigoGrupoNotaAvulsa              ( $_REQUEST["inNumGrupoNotaAvulsa"]       );
            foreach ($_REQUEST["inCodOrdemSelecionados"] as $stOrdemEntrega) {
                $obRARRConfiguracao->addSuperSimples( $stOrdemEntrega );
            }
            $obErro = $obRARRConfiguracao->salvarGrupo();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm2,"Configuração","alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        break;
}
?>
