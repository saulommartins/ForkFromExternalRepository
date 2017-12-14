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
    * Página de Processamento do Aposentadoria
    * Data de Criação: 21/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-04 17:49:07 -0300 (Sex, 04 Abr 2008) $

    * Casos de uso: uc-04.04.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoria.class.php"                                 );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoriaEncerramento.class.php"                     );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoriaExcluida.class.php"                         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php"                              );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPrevidencia.class.php"                              );

$link = Sessao::read("link");
$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
//Define o nome dos arquivos PHP
$stPrograma = "ManterAposentadoria";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$obErro = new Erro;
$obTPessoalAposentadoria           = new TPessoalAposentadoria;
$obTPessoalContratoServidor        = new TPessoalContratoServidor();
$obTPessoalContratoServidorPrevidencia = new TPessoalContratoServidorPrevidencia();

switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);

        $obTPessoalContrato = new TPessoalContrato();
        $obTPessoalContrato->setDado("registro", $_POST['inContrato']);
        $obTPessoalContrato->recuperaServidorNaoPensionista($rsContrato ,$stFiltro);

        if ( $rsContrato->getNumLinhas() == -1 ) {
            Sessao::getExcecao()->setDescricao("Servidor com o contrato ".$_POST['inContrato']." é um pensionista e não pode se aposentar!");
        }

        $stFiltro = " AND aposentadoria.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $obTPessoalAposentadoria->recuperaRelacionamento($rsAposentadoria,$stFiltro);
        if ( $rsAposentadoria->getNumLinhas() > 0 ) {
            Sessao::getExcecao()->setDescricao("O contrato ".$_POST['inContrato']." já possui aposentadoria cadastrada!");
        }

        $obTPessoalAposentadoria->setDado       ("cod_contrato"        ,$rsContrato->getCampo("cod_contrato"));
        $obTPessoalAposentadoria->setDado       ("cod_enquadramento"   ,$_POST['inCodEnquadramento']         );
        $obTPessoalAposentadoria->setDado       ("cod_classificacao"   ,$_POST['inCodClassificacao']         );
        $obTPessoalAposentadoria->setDado       ("dt_requirimento"     ,$_POST['dtRequerimentoAposentadoria']);
        $obTPessoalAposentadoria->setDado       ("num_processo_tce"    ,$_POST['stNrProcesso']               );
        $obTPessoalAposentadoria->setDado       ("dt_concessao"        ,$_POST['dtConcessao']                );
        $obTPessoalAposentadoria->setDado       ("percentual"          ,$_POST['nuPercentual']               );
        $obTPessoalAposentadoria->setDado       ("dt_publicacao"       ,$_POST['dtPublicacao']               );
        $obTPessoalAposentadoria->inclusao();

        $stFiltro = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $obTPessoalContratoServidorPrevidencia->recuperaTodos($rsContratoServidorPrevidencia,$stFiltro);
        while (!$rsContratoServidorPrevidencia->eof()) {
            $obTPessoalContratoServidorPrevidencia->setDado("cod_contrato",$rsContratoServidorPrevidencia->getCampo("cod_contrato"));
            $obTPessoalContratoServidorPrevidencia->setDado("cod_previdencia",$rsContratoServidorPrevidencia->getCampo("cod_previdencia"));
            $obTPessoalContratoServidorPrevidencia->setDado("timestamp",$rsContratoServidorPrevidencia->getCampo("timestamp"));
            $obTPessoalContratoServidorPrevidencia->setDado("bo_excluido",true);
            $obTPessoalContratoServidorPrevidencia->alteracao();
            $rsContratoServidorPrevidencia->proximo();
        }

        sistemaLegado::alertaAviso($pgForm,"Aposentadoria do contrato  ".$_POST['inContrato']." incluída com sucesso!","incluir","aviso", Sessao::getId(), "../");
        Sessao::encerraExcecao();
    break;
    case "alterar":
        if ($_POST['stMotivo'] == "" and $_POST['dtEncerramento'] != "") {
            $obErro->setDescricao("campo Motivo do Encerramento inválido!()");
        }
        if ($_POST['stMotivo'] != "" and $_POST['dtEncerramento'] == "") {
            $obErro->setDescricao("campo Data de Encerramento inválido!()");
        }
        if ( !$obErro->ocorreu() ) {
            Sessao::setTrataExcecao(true);
            $obTPessoalAposentadoriaEncerramento = new TPessoalAposentadoriaEncerramento();
            $obTPessoalAposentadoriaEncerramento->obTPessoalAposentadoria = &$obTPessoalAposentadoria;
            $obTPessoalAposentadoria->setDado   ("cod_contrato"        ,$_POST['inContrato']                       );
            $obTPessoalAposentadoria->setDado   ("cod_enquadramento"   ,$_POST['inCodEnquadramento']               );
            $obTPessoalAposentadoria->setDado   ("cod_classificacao"   ,$_POST['inCodClassificacao']               );
            $obTPessoalAposentadoria->setDado   ("dt_requirimento"     ,$_POST['dtRequerimentoAposentadoria']      );
            $obTPessoalAposentadoria->setDado   ("num_processo_tce"    ,$_POST['stNrProcesso']                     );
            $obTPessoalAposentadoria->setDado   ("dt_concessao"        ,$_POST['dtConcessao']);
            $obTPessoalAposentadoria->setDado   ("percentual"          ,$_POST['nuPercentual']                     );
            $obTPessoalAposentadoria->setDado   ("dt_publicacao"       ,$_POST['dtPublicacao']                     );
            $obTPessoalAposentadoria->inclusao();
            if ($_POST['stMotivo'] != "" and $_POST['dtEncerramento'] != "") {
                $obTPessoalAposentadoriaEncerramento->setDado("dt_encerramento",$_POST['dtEncerramento']);
                $obTPessoalAposentadoriaEncerramento->setDado("motivo",$_POST['stMotivo']);
                $obTPessoalAposentadoriaEncerramento->inclusao();
            }
            Sessao::encerraExcecao();
            sistemaLegado::alertaAviso($pgFilt,"Alteração concluída com sucesso!" ,"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "excluir":
        Sessao::setTrataExcecao(true);
        $obTPessoalAposentadoriaExcluida = new TPessoalAposentadoriaExcluida();
        $obTPessoalAposentadoriaExcluida->obTPessoalAposentadoria = &$obTPessoalAposentadoria;
        $stFiltro = " AND aposentadoria.cod_contrato = ".$_GET['inContrato'];
        $obTPessoalAposentadoria->recuperaRelacionamento($rsAposentadoria,$stFiltro);
        $obTPessoalAposentadoria->setDado       ("cod_contrato"        ,$_GET['inContrato']                        );
        $obTPessoalAposentadoria->setDado       ("timestamp"           ,$rsAposentadoria->getCampo("timestamp")    );
        $obTPessoalAposentadoriaExcluida->inclusao();

        $obTPessoalContratoServidor->setDado    ("cod_contrato"        ,$_GET['inContrato']                        );
        $obTPessoalContratoServidor->consultar();
        $obTPessoalContratoServidor->setDado    ("ativo"               ,true                                       );
        $obTPessoalContratoServidor->alteracao();

        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgFilt,"Exclusão concluída com sucesso!" ,"excluir","aviso", Sessao::getId(), "../");
    break;
}

?>
