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
    * Pagina de processamento para Licenca Diversa(Geral)
    * Data de Criação   : 26/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: PRConcederLicencaGeral.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.6  2007/05/14 20:33:07  dibueno
Alterações para possibilitar a emissao do alvará diverso

Revision 1.5  2007/05/11 20:23:05  dibueno
Alterações para possibilitar a emissao do alvará

Revision 1.4  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaObservacao.class.php"  );

$stAcao = $request->get('stAcao');
//Define o nome dos arquivos PHP
$stPrograma    = "ConcederLicencaGeral"         ;
$pgFilt        = "FL".$stPrograma.".php"        ;
$pgList        = "LS".$stPrograma.".php".$stLink;
$pgListAlt     = "LSLicencaGeral.php".$stLink;
$pgForm        = "FM".$stPrograma.".php"        ;
$pgProc        = "PR".$stPrograma.".php"        ;
$pgOcul        = "OC".$stPrograma.".php"        ;
$pgJs          = "JS".$stPrograma.".js"         ;

$obAtributos = new MontaAtributos;
$obAtributos->setName('AtributoLicenca_');
$obAtributos->recuperaVetor( $arChave );
$obErro = new Erro;

switch ($stAcao) {

    case "incGeral":
        // INICIA INCLUINDO LICENCA DIVERSA NO BANCO

        $inCodigoDocumento      = $_REQUEST['stCodDocumento'];
        $inCodigoTipoDocumento  = $_REQUEST['inCodTipoDocumento'];

        $obRCEMLicencaDiversa = new RCEMLicencaDiversa;
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if( is_array($value) )
                $value = implode(",",$value);
            $obRCEMLicencaDiversa->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        if ($_REQUEST["inCodigoLicenca"]) {
            $obRCEMLicencaDiversa->setCodigoLicenca ( $_REQUEST["inCodigoLicenca"] )  ;
        }

        $obRCEMLicencaDiversa->setExercicio     ( date('Y') );
        $obRCEMLicencaDiversa->setDataInicio    ( $_REQUEST["dtDataInicio"  ]   );
        if ($_REQUEST["dtDataTermino"]) {
            $obRCEMLicencaDiversa->setDataTermino   ( $_REQUEST["dtDataTermino" ]   );
        }

        $obRCEMLicencaDiversa->obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $_REQUEST["inCodigoTipoLicenca"] );
        $obRCEMLicencaDiversa->obRCGM->setNumCGM( $_REQUEST["inNumCGM"      ]   );
        $obRCEMLicencaDiversa->setArrayElementos( Sessao::read( "lsElementos" ) );
        #echo '<h3>CONCEDER LICENÇA</h3>';
        $obErro = $obRCEMLicencaDiversa->concederLicenca(true);
        #echo '<br>PR CONCEDER LICENCA GERAL :'.$this->inCodigoLicenca;
        $inCodLicenca = $obRCEMLicencaDiversa->getCodigoLicenca();

        $exercicio_divida = $obRCEMLicencaDiversa->getExercicio();

        $boEmissaoDocumento = $_REQUEST['boEmissaoDocumento'];

        if ( !$obErro->ocorreu() ) {
            #echo '<h2>LICENCA DOCUMENTO</h2>';
            $obRCEMConfiguracao = new RCEMConfiguracao;
            $obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
            $obErro = $obRCEMConfiguracao->consultarConfiguracao();
            if ( !$obErro->ocorreu() ) {
                #===================== INSERE NA TABELA EMISSAO DOCUMENTO
                include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDocumento.class.php" );
                $obTCEMLicencaDocumento = new TCEMLicencaDocumento;
                $stFiltro = " WHERE ";
                if ( $obRCEMConfiguracao->getNroAlvara() == "Exercicio" ) {
                    //por exercicio
                    $stFiltro .= " eld.exercicio = ".Sessao::getExercicio();
                } else {
                    $stFiltro .= " eld.exercicio = ".Sessao::getExercicio()." AND eld.cod_documento = ".$inCodigoDocumento;//por documento
                }

                $obErro = $obTCEMLicencaDocumento->buscaUltimoNumeroAlvara( $rsEmissao, $stFiltro );
                if ( !$obErro->ocorreu() ) {
                    $inNumEmissao = ( $rsEmissao->getCampo('valor') + 1 );
                    if ( !$exercicio_divida )
                        $exercicio_divida = '0000';

                    $obTCEMLicencaDocumento->setDado( "cod_licenca", $inCodLicenca );
                    $obTCEMLicencaDocumento->setDado( "exercicio", $exercicio_divida );
                    $obTCEMLicencaDocumento->setDado( "num_alvara", $inNumEmissao );
                    $obTCEMLicencaDocumento->setDado( "cod_documento", $inCodigoDocumento );
                    $obTCEMLicencaDocumento->setDado( "cod_tipo_documento", $inCodigoTipoDocumento );

                    $obErro = $obTCEMLicencaDocumento->inclusao();
                }
            }
        }

        if ( !$obErro->ocorreu() ) {

            if ($boEmissaoDocumento) { //boEmissaoDocumento

                $inCodTipoDocumento = $_REQUEST['stCodDocumentoTxt'];
                $stFiltro = "where a.cod_acao = ". Sessao::read('acao');
                $stFiltro .="AND b.cod_documento = ". $_REQUEST['stCodDocumentoTxt'];
                $obTModeloDocumento = new TAdministracaoModeloDocumento;
                $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );

                while ( !$rsDocumentos->Eof() ) {
                     $inCodTipoDocAtual  = $rsDocumentos->getCampo( "cod_tipo_documento" );
                     $inCodDocAtual      = $rsDocumentos->getCampo( "cod_documento" );
                     $stNomeArquivo      = $rsDocumentos->getCampo( "nome_arquivo_agt" );
                     $stNomeDocumento    = $rsDocumentos->getCampo( 'nome_documento' );

                     $rsDocumentos->proximo();
                 }

                $stCaminho = CAM_GT_CEM_INSTANCIAS."emissao/FMManterEmissao.php";
                $stInscricoes = $stParametros = null;
                $stParametros .= "&inNumeroLicenca=".$inCodLicenca;
                $stParametros .= "&inExercicio=".$exercicio_divida;
                $stParametros .= "&stTipoModalidade=emissao";
                $stParametros .= "&stCodAcao=".Sessao::read('acao');
                $stParametros .= "&stOrigemFormulario=conceder_licenca";

                $stParametros .= "&inCodigoDocumento=".$inCodigoDocumento;
                $stParametros .= "&inCodigoTipoDocumento=". $inCodigoTipoDocumento;

                $stParametros .= "&stNomeArquivo=".$stNomeArquivo;
                $stParametros .= "&stNomeDocumento=".$stNomeDocumento;

                $stParametros .= "&inInscricaoEconomica=".$_REQUEST["inInscricaoEconomica"];
                $stParametros .= "&stCtrl=Download";

                #echo '<hr>VAI EMITIR:<br>'.$stParametros; #exit;

                sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=incluir", "Conceder Licença", "alterar","aviso", Sessao::getId(), "../");

            } else {

                sistemaLegado::alertaAviso( $pgForm, "Licenca Número: ".$inCodLicenca, "incluir", "aviso", Sessao::getId(), "../");

            }

        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
       // ALTERAÇÃO LICENCA DIVERSA NO BANCO
        $obRCEMLicencaDiversa = new RCEMLicencaDiversa;
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if( is_array($value) )
                $value = implode(",",$value);
            $obRCEMLicencaDiversa->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        $obRCEMLicencaDiversa->setCodigoLicenca ($_REQUEST["inCodigoLicenca"])  ;
        $obRCEMLicencaDiversa->setExercicio     ( date('Y') );
        $obRCEMLicencaDiversa->setDataInicio    ( $_REQUEST["dtDataInicio"  ]   );
        $obRCEMLicencaDiversa->setDataTermino   ( $_REQUEST["dtDataTermino" ]   );
        $obRCEMLicencaDiversa->obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $_REQUEST["inCodigoTipoLicenca"] );
        $obRCEMLicencaDiversa->obRCGM->setNumCGM( $_REQUEST["inNumCGM"      ]   );
        $obErro = $obRCEMLicencaDiversa->alterarLicenca(true);

        $obTCEMLicencaObservacao = new TCEMLicencaObservacao;
        $obTCEMLicencaObservacao->setDado ( "cod_licenca", $_REQUEST["inCodigoLicenca"] );
        $obTCEMLicencaObservacao->setDado ( "exercicio"  , Sessao::getExercicio()           );
        $obTCEMLicencaObservacao->setDado ( "observacao" , $_REQUEST["stObservacao"]    );

        $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodigoLicenca"]." AND exercicio = '".Sessao::getExercicio()."'";
        $obTCEMLicencaObservacao->recuperaTodos( $rsLicencaObservacao , $stFiltro );
        if ( !$rsLicencaObservacao->Eof() ) {
            $obTCEMLicencaObservacao->alteracao();
        } else {
            $obTCEMLicencaObservacao->inclusao();
        }

        $tmpNumLicenca = $obRCEMLicencaDiversa->getCodigoLicenca();

        if ( !$obErro->ocorreu() ) {
           if ($boEmissaoDocumento) {
                $inCodTipoDocumento = $_REQUEST['stCodDocumentoTxt'];
                $stFiltro = "where a.cod_acao = ". Sessao::read('acao');
                $stFiltro .="AND b.cod_documento = ". $_REQUEST['stCodDocumentoTxt'];
                $obTModeloDocumento = new TAdministracaoModeloDocumento;
                $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );

                while ( !$rsDocumentos->Eof() ) {
                     $inCodTipoDocAtual  = $rsDocumentos->getCampo( "cod_tipo_documento" );
                     $inCodDocAtual      = $rsDocumentos->getCampo( "cod_documento" );
                     $stNomeArquivo      = $rsDocumentos->getCampo( "nome_arquivo_agt" );
                     $stNomeDocumento    = $rsDocumentos->getCampo( 'nome_documento' );
                     $rsDocumentos->proximo();
                }

                $stCaminho = CAM_GT_CEM_INSTANCIAS."emissao/LSManterEmissao.php";
                $stInscricoes = $stParametros = '';
                $stParametros .= "&inCodLicenca=".$_REQUEST["inCodigoLicenca"];
                $stParametros .= "&inExercicio=".date('Y');
                $stParametros .= "&stTipoModalidade=alteracao";
                $stParametros .= "&stCodAcao=".Sessao::read('acao');
                $stParametros .= "&stOrigemFormulario=alterar";
                $stParametros .= "&stTipoLicenca=".$_REQUEST['inCodigoTipoLicenca'];
                $stParametros .= "&inInscricaoEconomica=".$_REQUEST["inInscricaoEconomica"];
                $stParametros .= "&inCodTipoDocumento2=".$_REQUEST["inCodTipoDocumento"];
                $stParametros .= "&stCodDocumentoTxt2=".$_REQUEST["stCodDocumentoTxt"];
                $stParametros .= "&stCodDocumento2=".$_REQUEST["stCodDocumento"];
                $stParametros .= "&stNomeDocumento=".$stNomeDocumento;
                $stParametros .= "&stNomeArquivo=".$stNomeArquivo;
                $stParametros .= "&stCtrl=Download";

                sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=alterar", "Conceder Licença", "alterar","aviso", Sessao::getId(), "../");

            } else {
                sistemaLegado::alertaAviso($pgListAlt."?stAcao=".$stAcao,"Licenca Numero: ".$tmpNumLicenca,"alterar","aviso", Sessao::getId(), "../");
            }
        } else {
           sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obRCEMRT = new RCEMResponsavelTecnico;
        $obRCEMRT->setNumCgm            ( $_REQUEST[ "inNumCGM"         ]   ) ;
        $obRCEMRT->setCodigoProfissao   ( $_REQUEST[ "inCodigoProfissao"]   ) ;
        $obErro = $obRCEMRT->excluirResponsavelTecnico();

        if ( !$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgList,"Registro: ".$_REQUEST["stRegistro"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
    case "elemento":
       // ALTERAÇÃO LICENCA DIVERSA NO BANCO
        $obRCEMLicencaDiversa = new RCEMLicencaDiversa;

        $obRCEMLicencaDiversa->setCodigoLicenca ( $_REQUEST["inCodigoLicenca"])  ;
        $obRCEMLicencaDiversa->setExercicio     ( $_REQUEST["stExercicio"] );
        $obRCEMLicencaDiversa->obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $_REQUEST["inCodigoTipoLicenca"] );
        $obRCEMLicencaDiversa->excluirAtributosElementoLicencaDiversa(true);
        $arElementos = Sessao::read( "lsElementos" );

        for ($inCount=0;$inCount < count($arElementos);$inCount++) {
            $inCodElemento = $arElementos[$inCount]["inCodigoElemento"];
            $obRCEMLicencaDiversa->obRCEMElemento->setCodigoElemento($inCodElemento);
            $obRCEMLicencaDiversa->setOcorrencia( $arElementos[$inCount]["stOcorrencia"]);

            foreach ($arElementos[$inCount]["elementos"][$inCodElemento] as $chave => $valor) {
                $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->addAtributosDinamicos( $chave, $valor );
            }
            $obErro = $obRCEMLicencaDiversa->salvarElementoLicencaDiversa($boTransacao);
            $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->setAtributosDinamicos( array() );
        }

        $tmpNumLicenca = $obRCEMLicencaDiversa->getCodigoLicenca();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgListAlt."?stAcao=".$stAcao,"Licenca Numero: ".$tmpNumLicenca,"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    default:
    include_once(CAM_GT_ECONOMICO."instancias/licenca/FMConcederLicencaGeralTipo.php");
}

?>
