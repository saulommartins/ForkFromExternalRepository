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
    * Página de Processamento de Inclusao/Alteracao de Atividade
    * Data de Criação   : 11/04/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: PRManterAtividade.php 66548 2016-09-21 13:05:07Z evandro $

    * Casos de uso: uc-05.02.07

*/

/*
$Log$
Revision 1.9  2007/10/16 12:54:38  cercato
Ticket#10411#

Revision 1.8  2007/05/17 21:13:00  cercato
Bug #9273#

Revision 1.7  2007/03/30 20:53:15  rodrigo
Bug #8598#

Revision 1.6  2007/03/12 19:00:29  rodrigo
Bug #8598#

Revision 1.5  2006/09/15 14:32:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCnae.class.php"   );

$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtividade";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgFormNivel = "FM".$stPrograma."Nivel.php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";

$obRCEMAtividade = new RCEMAtividade;
$obRCEMCnae	 = new RCEMCnae($obRCEMAtividade);
$obErro          = new Erro;
$obTransacao     = new Transacao;

$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

switch ($stAcao) {
    case "incluir":
        $obRCEMAtividade->setUltimoNivel     ( $_REQUEST["boUltimoNivel"]      );
        $obRCEMAtividade->setCodigoVigencia  ( $_REQUEST["inCodigoVigencia"]   );
        $obRCEMAtividade->setCodigoNivel     ( $_REQUEST["inCodigoNivel"]      );
        $obRCEMAtividade->setNomeAtividade   ( $_REQUEST["stNomeAtividade"]    );
        $obRCEMAtividade->setValor           ( preg_replace("/^0*/", "", trim( $_REQUEST["inValorAtividade"] ) ) );
        $obRCEMAtividade->setAliquota        ( $_REQUEST["flAliquota"] );
        $obRCEMAtividade->setValorComposto   ( $_REQUEST["stChaveServico"]     );
        
        if ($_REQUEST["boUltimoNivel"]) {
            if ($_REQUEST["inCodCnae_5"]) {
                $obRCEMAtividade->addAtividadeCnae();
                $obRCEMCnae->setValorCompostoCnae( $_REQUEST["stChaveCnae"]);
                $obRCEMCnae->listarCnae($rsCnae);
                $inCodCnae =  $rsCnae->getCampo("cod_cnae");

                $obRCEMAtividade->roUltimoCnae->setCodigoCnae( $inCodCnae  );
            }

            if ($_REQUEST["inCodResponsaveisSelecionados"]) {
                foreach ($_REQUEST["inCodResponsaveisSelecionados"] as $valor) {
                    $obRCEMAtividade->addAtividadeProfissao();
                    $obRCEMAtividade->roUltimaProfissao->setCodigoProfissao( $valor );
                }
            }

            if ($_REQUEST["inCodElementosSelecionados"]) {
                foreach ($_REQUEST["inCodElementosSelecionados"] as $valor) {
                    $obRCEMAtividade->addAtividadeElemento();
                    $obRCEMAtividade->roUltimoElemento->setCodigoElemento( $valor );
                }
            }

            $arServicosSessao = Sessao::read( "Servicos" );
            if (count($arServicosSessao) > 0) {
                for ($inCount=0; $inCount<count($arServicosSessao); $inCount++) {
                    $obRCEMAtividade->addServico( $arServicosSessao[$inCount] );
                }
            }
        }

        //MONTAR UM LOOP PARA PEGAR O VALOR DOS COMBOS
        for ($inContCombos = 1; $inContCombos < $_REQUEST["inNumNiveis"] ; $inContCombos++) {
            $arChaveAtividade = explode( "§", $_REQUEST["inCodAtividade_".$inContCombos] );
            //[0] = cod_nivel | [1] = cod_atividade | [2] = valor | [3] = valor_reduzido
            $obRCEMAtividade->addCodigoAtividade( $arChaveAtividade );
        }
        $stLink  = "&stChaveNivel=".$_REQUEST["inCodigoVigencia"]."-".$_REQUEST["inCodigoNivel"];
        $stLink .= "&stValorComposto=".$_REQUEST["stChaveAtividade"];

        if ( !$obErro->ocorreu() ) {
            include_once( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php" );

            $obTCEMAtividade = new TCEMAtividade();
            $rsTCEMAtividade = new RecordSet();

            $stFiltro = " WHERE TRIM(atividade.nom_atividade) = '".trim($_REQUEST['stNomeAtividade'])."' \n";

            $obErro = $obTCEMAtividade->recuperaTodos($rsTCEMAtividade,$stFiltro,'',$boTransacao);
            
            if (!($obErro->ocorreu())) {
               if ( $rsTCEMAtividade->eof() ) {
                  $obErro = $obRCEMAtividade->incluirAtividade($boTransacao);
               } else {
                  $obErro->setDescricao( "Atividade já cadastrada no sistema com o mesmo nome.");
               }
            }
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList.$stLink,"Nome Atividade: ".$_REQUEST['stNomeAtividade'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obRCEMAtividade->setUltimoNivel       ( $request->get("boUltimoNivel") );
        $obRCEMAtividade->setCodigoVigencia    ( $request->get("inCodigoVigencia")    );
        $obRCEMAtividade->setCodigoNivel       ( $request->get("inCodigoNivel")       );
        $obRCEMAtividade->setCodigoAtividade   ( $request->get("inCodigoAtividade") );
        $obRCEMAtividade->setNomeAtividade     ( $request->get("stNomeAtividade")   );        

        if ($request->get("inCodigoNivel") > 1) {
            $stValorReduzido = $request->get("stValorReduzido");
            $arTMP = explode( ".", $stValorReduzido );
            $stValor = $arTMP[ $request->get("inCodigoNivel")-1 ];
        }else
            $stValor = $request->get("stValorReduzido");

        $obRCEMAtividade->setValor              ( preg_replace( "/^0*/", "",trim( $stValor ) ) );
        $obRCEMAtividade->setAliquota           ( $request->get("flAliquota") );
        $obRCEMAtividade->setValorComposto      ( $request->get("stChaveServico")     );
        if ($request->get("boUltimoNivel")) {
            if ($request->get('stChaveCnae')) {
                $obRCEMAtividade->addAtividadeCnae();
                $obRCEMCnae->setValorCompostoCnae( $request->get('stChaveCnae') );
                $obRCEMCnae->listarCnae($rsCnae);
                $inCodCnae =  $rsCnae->getCampo("cod_cnae");
                $obRCEMAtividade->roUltimoCnae->setCodigoCnae( $inCodCnae );
            }

            if ($request->get("inCodResponsaveisSelecionados")) {
                foreach ($request->get("inCodResponsaveisSelecionados") as $valor) {
                    $obRCEMAtividade->addAtividadeProfissao();
                    $obRCEMAtividade->roUltimaProfissao->setCodigoProfissao( $valor );
                }
            }

            if ($request->get("inCodElementosSelecionados")) {
                foreach ($request->get("inCodElementosSelecionados") as $valor) {
                    $obRCEMAtividade->addAtividadeElemento();
                    $obRCEMAtividade->roUltimoElemento->setCodigoElemento( $valor );
                }
            }

            $arServicosSessao = Sessao::read( "Servicos" );
            if ( count($arServicosSessao) > 0 ) {
                for ($inCount=0; $inCount<count($arServicosSessao); $inCount++) {
                    $obRCEMAtividade->addServico( $arServicosSessao[$inCount] );
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMAtividade->alterarAtividade($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome Atividade: ".$request->get('stNomeAtividade'),"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        $obRCEMAtividade->setUltimoNivel       ( $_REQUEST["boUltimoNivel"]     );
        $obRCEMAtividade->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"]  );
        $obRCEMAtividade->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]     );
        $obRCEMAtividade->setCodigoAtividade   ( $_REQUEST["inCodigoAtividade"] );
        $obRCEMAtividade->setNomeAtividade     ( $_REQUEST["stNomeAtividade"]   );
        $obRCEMAtividade->setValorReduzido     ( $_REQUEST["stValorReduzido"]   );
        $obRCEMAtividade->setValor             ( preg_replace("/^0*/", "",trim( $_REQUEST["inValorAtividade"] ) ) );
        $obRCEMAtividade->setAliquota          ( $_REQUEST["flAliquota"] );

        // verificando se atividade a ser excluída está definida para alguma inscrição econômica
        $obErro = $obRCEMAtividade->listarAtividadeInscricao( $rsInscricaoAtividade );
        if ( $rsInscricaoAtividade->getNumLinhas() > 0 && !$obErro->ocorreu() ) {
            $obErro->setDescricao("Atividade em exercício por Inscrição Econômica.");
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMAtividade->excluirAtividade($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome Atividade: ".$_REQUEST['stNomeAtividade'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_excluir", "erro", Sessao::getId(), "../");
        }
    break;

}
