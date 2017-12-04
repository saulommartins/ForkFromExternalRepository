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
  * Página de Processamento de Desoneracao
  * Data de criação : 31/05/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

    * $Id: PRManterDesoneracao.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.12  2006/11/21 15:50:05  cercato
bug #6853#

Revision 1.11  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.10  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php");

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgFormConcederDesoneracao = "FMConcederDesoneracao.php?".Sessao::getId()."&stAcao=$stAcao";
$pgFormVinculo = "FMConcederDesoneracaoVinculo.php?".Sessao::getId()."&stAcao=$stAcao";
$pgFormProrrogar = "FLProrrogarDesoneracao.php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obErro = new Erro;
$obRARRDesoneracao = new RARRDesoneracao;
$inCodAtributosSelecionados = $_REQUEST["inCodAtributoSelecionados"];

$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "incluir":
        $Formula = explode ( '.', $_REQUEST['inCodigoFormula'] );

        $obRARRDesoneracao->setCodigoTipo( $_REQUEST['inCodigoTipo'] );
        $arCodigoCredito = explode('.',$_REQUEST['inCodigoCredito']);
        $obRARRDesoneracao->obRMONCredito->setCodCredito( $arCodigoCredito[0] );
        $obRARRDesoneracao->obRMONCredito->setCodEspecie( $arCodigoCredito[1] );
        $obRARRDesoneracao->obRMONCredito->setCodGenero( $arCodigoCredito[2] );
        $obRARRDesoneracao->obRMONCredito->setCodNatureza( $arCodigoCredito[3] );

        $obRARRDesoneracao->setInicio( $_REQUEST['dtInicio'] );
        $obRARRDesoneracao->setTermino( $_REQUEST['dtTermino'] );
        $obRARRDesoneracao->setExpiracao( $_REQUEST['dtExpiracao'] );
        if ($_REQUEST['boProrrogavel']) {
            $obRARRDesoneracao->setProrrogavel( true );
        }
        if ($_REQUEST['boRevogavel']) {
            $obRARRDesoneracao->setRevogavel( true );
        }
        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacao'] );
        $obRARRDesoneracao->obRMONIndicadorEconomico->setCodModulo     ( $Formula[0] );
        $obRARRDesoneracao->obRMONIndicadorEconomico->setCodBiblioteca ( $Formula[1] );
        $obRARRDesoneracao->obRMONIndicadorEconomico->setCodFuncao     ( $Formula[2] );

        for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRARRDesoneracao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo );
        }

        $obErro = $obRARRDesoneracao->definirDesoneracao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Desoneracao incluída:  ".$obRARRDesoneracao->getCodigo(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;

    case "alterar":
        $Formula = explode ( '.', $_REQUEST['inCodigoFormula'] );

        $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
        $obRARRDesoneracao->setCodigoTipo( $_REQUEST['inCodigoTipo'] );
        $obRARRDesoneracao->setInicio( $_REQUEST['dtInicio'] );
        $arCodigoCredito = explode('.',$_REQUEST['inCodigoCredito']);
        $obRARRDesoneracao->obRMONCredito->setCodCredito( $arCodigoCredito[0] );
        $obRARRDesoneracao->obRMONCredito->setCodNatureza( $arCodigoCredito[1] );
        $obRARRDesoneracao->obRMONCredito->setCodGenero( $arCodigoCredito[2] );
        $obRARRDesoneracao->obRMONCredito->setCodEspecie( $arCodigoCredito[3] );
        $obRARRDesoneracao->setTermino( $_REQUEST['dtTermino'] );
        $obRARRDesoneracao->setExpiracao( $_REQUEST['dtExpiracao'] );
        if ($_REQUEST['boProrrogavel']) {
            $obRARRDesoneracao->setProrrogavel( true );
        }
        if ($_REQUEST['boRevogavel']) {
            $obRARRDesoneracao->setRevogavel( true );
        }

        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacao'] );
        $obRARRDesoneracao->obRMONIndicadorEconomico->setCodModulo     ( $Formula[0] );
        $obRARRDesoneracao->obRMONIndicadorEconomico->setCodBiblioteca ( $Formula[1] );
        $obRARRDesoneracao->obRMONIndicadorEconomico->setCodFuncao     ( $Formula[2] );
        for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRARRDesoneracao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo );
        }

        $obErro = $obRARRDesoneracao->alterarDesoneracao();
        if ( !$obErro->ocorreu() ) {
//echo "sem erros"; exit();
            SistemaLegado::alertaAviso($pgList,"Desoneracao alterada: ".$obRARRDesoneracao->getCodigo(),"alterar","aviso", Sessao::getId(), "../");
        } else {
//echo "com erros: ".$obErro->getDescricao()."<br>"; exit();
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
        $obErro = $obRARRDesoneracao->excluirDesoneracao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Desoneracao: ".$obRARRDesoneracao->getCodigo(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
    case "conceder":

        if ($_REQUEST['tipoConcessao'] == "contribuinte") {
            $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
            $obRARRDesoneracao->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );

            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRARRDesoneracao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }

            if ($_REQUEST["inCodImobiliaria"])
                $obRARRDesoneracao->setInscricaoImovel($_REQUEST["inCodImobiliaria"]);

            if ($_REQUEST["inCodEconomica"])
                $obRARRDesoneracao->setInscricaoEconomica($_REQUEST["inCodEconomica"]);

            $obErro = $obRARRDesoneracao->concederDesoneracao();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgFormConcederDesoneracao,"Desoneracao concedida:  ".$obRARRDesoneracao->getCodigo(),"conceder","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_conceder","erro");
            }
        } else { //GRUPO
            include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
            $obRFuncao = new RFuncao;

            if ($_REQUEST['inCodigoFormula'] != "") {
                $arCodFuncao = explode('.',$_REQUEST["inCodigoFormula"]);
                $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
                $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
                $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
                $obRFuncao->consultar();

                $stNomeFuncao = $obRFuncao->getNomeFuncao();
            }

            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

                 $stSql = " SELECT fn_conceder_desoneracao_grupo(".$_REQUEST["inCodigoDesoneracao"].",'".$stNomeFuncao."','".$_REQUEST["boTipoInscricao"]."');";

                 $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
                 if ( $obErro->ocorreu() ) {
                     SistemaLegado::liberaFrames();
                     SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_conceder","erro");
                     break;
                 } else {
                     SistemaLegado::liberaFrames();
                     SistemaLegado::alertaAviso($pgFormConcederDesoneracao,"Desoneração concluída com sucesso. Total de registros Desonerados: ".$rsRecordSet->getCampo("fn_conceder_desoneracao_grupo")."" ,"conceder","aviso", Sessao::getId(), "../");
                 }
        }

        break;

    case "prorrogar":
        $arDataExp = explode("/", $_REQUEST["dtExpiracao"]);
        $arDataDes = explode("/", $_REQUEST["dtProDes"]);
        if ($arDataDes[2].$arDataDes[1].$arDataDes[0] <= $arDataExp[2].$arDataExp[1].$arDataExp[0]) {
            SistemaLegado::exibeAviso('Data da prorrogação '.$_REQUEST["dtProDes"].' é inferior a data de expiração '.$_REQUEST["dtExpiracao"].'!',"n_prorrogar","erro");
            exit;
        }

        $obRARRDesoneracao->setOcorrencia( $_REQUEST["inOcorrencia"] );
        $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
        $obRARRDesoneracao->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
        $obRARRDesoneracao->setProrrogacao( $_REQUEST['dtProDes'] );
        $obRARRDesoneracao->setConcessao( $_REQUEST["dtConcessao"] );
        $obRARRDesoneracao->setRevogacao( $_REQUEST['dtRevogacao'] );

        $obErro = $obRARRDesoneracao->prorrogarDesoneracao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFormProrrogar,"Desoneracao prorrogada:  ".$obRARRDesoneracao->getCodigo(),"prorrogar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_prorrogar","erro");
        }
        break;

    case "revogar":
        $obRARRDesoneracao->setOcorrencia( $_REQUEST["inOcorrencia"] );
        $obRARRDesoneracao->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
        $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
        $obRARRDesoneracao->setRevogacao( $_REQUEST['dtProDes'] );
        $obRARRDesoneracao->setConcessao( $_REQUEST["dtConcessao"] );
        $obRARRDesoneracao->setProrrogacao( $_REQUEST["dtProrrogacao"] );
        $obErro = $obRARRDesoneracao->revogarDesoneracao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFormProrrogar,"Desoneracao revogada:  ".$obRARRDesoneracao->getCodigo(),"revogar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_revogar","erro");
        }
        break;
}
