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
    * Formulario para Modalidade de Lançamento
    * Data de Criação   : 04/01/2005

    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: PRManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.8  2006/11/10 17:17:25  cercato
alteração do uc_05.02.13

Revision 1.7  2006/11/08 10:34:57  fabio
alteração do uc_05.02.13

Revision 1.6  2006/09/15 14:33:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeLancamento.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeAtividade.class.php"  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeInscricao.class.php"  );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma    = "ManterModalidade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//include_once( $pgJs );

switch ($stAcao) {
    case "definir":
        if ($_REQUEST["boVinculoModalidade"] == "atividade") {
            $obRCEMModalidadeAtividade  = new RCEMModalidadeAtividade;
            $obRCEMModalidadeAtividade->obRCEMAtividade->setValorComposto( $_REQUEST["stValorComposto"] );
            $obRCEMModalidadeAtividade->setDataInicio                                   ( $_REQUEST["dtDataInicio"]       );
            $obRCEMModalidadeAtividade->obRCEMModalidadeLancamento->setCodigoModalidade ( $_REQUEST["inCodigoModalidade"] );
            $obRCEMModalidadeAtividade->setValor                                        ( $_REQUEST["nuValor"]            );

            if ($_REQUEST["stTipoValor"] == "percentual") {
                $obRCEMModalidadeAtividade->setPercentual                               ( "true"                          );
            } elseif ($_REQUEST["stTipoValor"] == "indicador") {
                $obRCEMModalidadeAtividade->setPercentual                               ( "false"                         );
                $obRCEMModalidadeAtividade->obRMONIndicadorEconomico->setCodIndicador   ( $_REQUEST["inCodIndicador"]     );
            } elseif ($_REQUEST["stTipoValor"] == "moeda") {
                $obRCEMModalidadeAtividade->setPercentual                               ( "false"                         );
                $obRCEMModalidadeAtividade->obRMONMoeda->setCodMoeda                    ( $_REQUEST["inCodMoeda"]         );
            }

            $tmpNivel = $_REQUEST['inNumNiveis'] - 1;
            $tmpAtividade = "inCodAtividade_".$tmpNivel;
            //CASO NÃO TENHA SIDO SELECIONADO O ULTIMO VÍVEL DAS ATIVIDADES O SISTEMA BUSCA TODOS ULTIMOS NÍVEL
            //DO ULTIMO NÍVEL DA ULTIMA ATIVIDADE SELECIONADA
            if (trim($_REQUEST[$tmpAtividade])=="") {
                $obRCEMModalidadeAtividade->obRCEMAtividade->setCodigoNivel($_REQUEST['inCodigoVigencia']);
                $obRCEMModalidadeAtividade->obRCEMAtividade->setValorComposto($_REQUEST['stChaveAtividade']);
                $obErro = $obRCEMModalidadeAtividade->cadastrarModalidadeLote();
                $stMensagem="Chave da Atividade: ".$_REQUEST['stChaveAtividade'];
            } else {
                $arCodAtividade = explode('§',$_REQUEST[$tmpAtividade]);
                $obRCEMModalidadeAtividade->obRCEMAtividade->setCodigoAtividade($arCodAtividade[1]);
                $obErro = $obRCEMModalidadeAtividade->cadastrarModalidade();
                $stMensagem="Codigo da Atividade: ".$arCodAtividade[1];
            }

            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgFormVinculo."?stAcao=definir&boVinculoModalidade=atividade",$stMensagem,"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } elseif ($_REQUEST["boVinculoModalidade"] == "inscricao") {
            $obRCEMModalidadeInscricao  = new RCEMModalidadeInscricao;
            $obRCEMModalidadeInscricao->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obRCEMModalidadeInscricao->setDataInicio($_REQUEST["dtDataInicio"]);
            $obRCEMModalidadeInscricao->setAtividadesInscricao( Sessao::read( "atividades" ) );

//-----------------------------------------------------------------------------------------------------
            if ($_REQUEST['inNumProcesso']) {
                list($inProcesso,$inExercicio) = explode("/", $_REQUEST['inNumProcesso']);
                $obRCEMModalidadeInscricao->obRProcesso->setCodigoProcesso( $inProcesso );
                $obRCEMModalidadeInscricao->obRProcesso->setExercicio( $inExercicio );
            }
//-----------------------------------------------------------------------------------------------------
            $obErro = $obRCEMModalidadeInscricao->cadastrarModalidade();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgFormVinculo."?stAcao=definir&boVinculoModalidade=inscricao","Codigo da Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"],"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
    break;

    case "excluir":
        if ($_REQUEST["boVinculoModalidade"] == "atividade") {
            $obRCEMModalidadeAtividade  = new RCEMModalidadeAtividade;
            $obRCEMModalidadeAtividade->obRCEMAtividade->setCodigoAtividade            ( $_REQUEST["inCodigoAtividade"]    );
            $obRCEMModalidadeAtividade->setDataInicio                                  ( $_REQUEST["dtVigenciaModalidade"] );
            $obRCEMModalidadeAtividade->obRCEMModalidadeLancamento->setCodigoModalidade( $_REQUEST["inCodigoModalidade"]   );
            $obErro = $obRCEMModalidadeAtividade->excluirModalidade();

            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList."?stAcao=excluir&boVinculoModalidade=atividade","Codigo da Atividade: ".$_REQUEST["inCodigoAtividade"],"excluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
            }
        } elseif ($_REQUEST["boVinculoModalidade"] == "inscricao") {
            $obRCEMModalidadeInscricao  = new RCEMModalidadeInscricao;
            $obRCEMModalidadeInscricao->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obErro = $obRCEMModalidadeInscricao->excluirModalidade();

            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList."?stAcao=excluir&boVinculoModalidade=inscricao&","Codigo da Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"],"excluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
            }
        }
    break;
    case "baixar":
        if ($_REQUEST["boVinculoModalidade"] == "atividade") {
            $obRCEMModalidadeAtividade  = new RCEMModalidadeAtividade;
            $obRCEMModalidadeAtividade->obRCEMAtividade->setCodigoAtividade            ( $_REQUEST["inCodigoAtividade"]    );
            $obRCEMModalidadeAtividade->setDataInicio                                  ( $_REQUEST["dtVigenciaModalidade"] );
            $obRCEMModalidadeAtividade->obRCEMModalidadeLancamento->setCodigoModalidade( $_REQUEST["inCodigoModalidade"]   );
            $obRCEMModalidadeAtividade->setMotivo                                      ( $_REQUEST["stMotivo"]             );
            $obErro = $obRCEMModalidadeAtividade->baixarModalidade();

            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList."?stAcao=baixar&boVinculoModalidade=atividade","Codigo da Atividade: ".$_REQUEST["inCodigoAtividade"],"baixar","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
            }
        } elseif ($_REQUEST["boVinculoModalidade"] == "inscricao") {
            $obRCEMModalidadeInscricao  = new RCEMModalidadeInscricao;
            $obRCEMModalidadeInscricao->obRCEMInscricaoAtividade->obRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obRCEMModalidadeInscricao->obRCEMInscricaoAtividade->listarAtividadesInscricao( $rsAtividades );

            $arModalidadeAtividade = array ();
            $inCount = 0;
            while ( !$rsAtividades->eof() ) {
                $arModalidadeAtividade[$inCount]["cod_atividade"       ] = $rsAtividades->getCampo("cod_atividade"       );
                $arModalidadeAtividade[$inCount]["ocorrencia_atividade"] = $rsAtividades->getCampo("ocorrencia_atividade");
                $arModalidadeAtividade[$inCount]["cod_modalidade"      ] = $rsAtividades->getCampo("cod_modalidade"      );
                $rsAtividades->proximo();
                $inCount++;
            }

            $obRCEMModalidadeInscricao->setDataInicio($_REQUEST["dtVigenciaModalidade"]);
            $obRCEMModalidadeInscricao->setAtividadesInscricao( $arModalidadeAtividade );
            $obRCEMModalidadeInscricao->setMotivo    ($_REQUEST["stMotivo"]            );
            $obErro = $obRCEMModalidadeInscricao->baixarModalidade();

            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList."?stAcao=baixar&boVinculoModalidade=inscricao&","Codigo da Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"],"baixar","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
            }
        }
    break;
}
?>
