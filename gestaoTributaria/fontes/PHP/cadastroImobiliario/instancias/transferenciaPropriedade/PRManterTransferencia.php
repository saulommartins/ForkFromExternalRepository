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
    * Página de processamento para o cadastro de transferência de proipriedade
    * Data de Criação   : 02/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini
                             Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: PRManterTransferencia.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.14  2006/09/18 10:31:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php"  );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"         );

$stAcao = $request->get('stAcao');
$stLink = Sessao::read('stLink');

if ($_REQUEST['boSegueAvaliacao']) {
    $stLink.= '&inInscricaoImobiliaria='.$_REQUEST['inInscricaoImobiliaria'];
    $stLink.= '&inCodigoTransferencia='.$_REQUEST['inCodigoTransferencia'];
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".$stLink;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAvalia = "../../../arrecadacao/instancias/movimentacoes/FMAvaliarImovel.php?stAcao=incluir&".$stLink;
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

$obRCIMTransferencia = new RCIMTransferencia;
$obErro = new Erro;

$obRCIMConfiguracao          = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();
$inInscricaoMascara = str_pad($_REQUEST["inInscricaoImobiliaria"],strlen($stMascaraInscricao),"0",STR_PAD_LEFT);

$arDocumentosSessao = Sessao::read('Documentos');
$inTotArray = count( $arDocumentosSessao ) - 1;
for ($inCount = 0; $inCount <= $inTotArray; $inCount ++) {
     if ( isset( $_REQUEST['boEntregue_'.($inCount+1)] ) ) {
         $arDocumentosSessao[$inCount]['entregue'] = 't';
     } else {
         if ($stAcao == "alterar") {
             $arDocumentosSessao[$inCount]['entregue'] = 'f';
         }
     }
}
Sessao::write('Documentos', $arDocumentosSessao);

function alertaAvisoRedirect($location="", $objeto="", $tipo="n_incluir", $chamada="erro", $sessao, $caminho="", $func="")
{
    ;
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                alertaAviso      ( "'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
           </script>';
//    session_regenerate_id();
//    Sessao::setId( "PHPSESSID=".session_id());
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
//    $sessao->geraURLRandomica();
    Sessao::write('acao'  , "739");
    Sessao::write('modulo', "12" );
    Sessao::write('acaoLote', "739"); //no rcimconfiguracao.class.php (buscaModulo) eh utilizada esta variavel para recuperar a acao atual!
//    $sessao->funcionalidade = $func;
    print '<script type="text/javascript">
                mudaMenu         ( "'.$func.'"     );
                mudaTelaPrincipal( "'.$location.'" );
           </script>';
}

function VerificaDocumentacaoEntregue($listaDocumentos)
{
        $contEntregue = 0;
        $cont = 0;
        $tam = count ( $listaDocumentos );
        while ($cont < $tam) {
            if ($listaDocumentos[$cont]['entregue']== 't' && $listaDocumentos[$cont]['obrigatorio'] != 'não') {
                $contEntregue++;
            } elseif ($listaDocumentos[$cont]['obrigatorio'] = 'não') {
                $contEntregue++;
            }
            $cont++;
        }

        if ($contEntregue == $tam) {
            return true;
        } else {
            return false;
        }

}

switch ($stAcao) {
    case "incluir":
        list($inProcesso,$inExercicio) = explode("/",$_POST['inNumProcesso']);
        $arDocumentosSessao  = Sessao::read('Documentos');
        $arAdquirentesSessao = Sessao::read('Adquirentes');
        $obRCIMTransferencia->setCodigoNatureza     ( $_REQUEST["inCodigoNatureza"      ] );
        $obRCIMTransferencia->setInscricaoMunicipal ( $_REQUEST["inInscricaoImobiliaria"] );
        $obRCIMTransferencia->setProcesso           ( $inProcesso                         );
        $obRCIMTransferencia->setExercicioProcesso  ( $inExercicio                        );
        $obRCIMTransferencia->obRCIMCorretagem->setRegistroCreci( $_REQUEST["stCreci"   ] );
        $obRCIMTransferencia->setDocumentos         ( Sessao::read('Documentos'));
        $obRCIMTransferencia->setAdquirentes        ( Sessao::read('Adquirentes'));
        $obRCIMTransferencia->setEfetivacao         ( 'f'                                 );
        $obRCIMTransferencia->listarTransferencia( $rsRecordSet );
        if( $rsRecordSet->getNumLinhas() > 0
            && $rsRecordSet->getCampo("dt_efetivacao") == ""
            && $rsRecordSet->getCampo("dt_cancelamento") == "" ){
            $obErro->setDescricao( "Inscrição Imobiliária já possiu transferência. (Inscrição Imobiliária: ".$obRCIMTransferencia->setCodigoNatureza().")." );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCIMTransferencia->cadastrarTransferencia();
        }
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['boItbi'] == 'true') {
                $js = " alertaAviso('".$pgForm."','Inscrição Imobiliária:".$inInscricaoMascara."','".Sessao::getId()."');\n";
                $js = " window.parent.close();";
                SistemaLegado::executaFrameOculto($js);
            } else {
                SistemaLegado::alertaAviso($pgForm,"Inscrição Imobiliária: ".$inInscricaoMascara,"incluir","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        list($inProcesso,$inExercicio) = explode("/",$_POST['inNumProcesso']);

        $arDocumentosSessao  = Sessao::read('Documentos');
        $arAdquirentesSessao = Sessao::read('Adquirentes');

        $obRCIMTransferencia->setCodigoTransferencia ( $_REQUEST["inCodigoTransferencia" ] );
        $obRCIMTransferencia->setCodigoNatureza      ( $_REQUEST["inCodigoNatureza"      ] );
        $obRCIMTransferencia->setInscricaoMunicipal  ( $_REQUEST["inInscricaoImobiliaria"] );
        $obRCIMTransferencia->setProcesso            ( $inProcesso                         );
        $obRCIMTransferencia->setExercicioProcesso   ( $inExercicio                        );
        $obRCIMTransferencia->obRCIMCorretagem->setRegistroCreci( $_REQUEST["stCreci"    ] );
        $obRCIMTransferencia->setDocumentos          ( $arDocumentosSessao );
        $obRCIMTransferencia->setAdquirentes         ( $arAdquirentesSessao );
        $obRCIMTransferencia->setEfetivacao          ( 'f'                                 );
        $obErro = $obRCIMTransferencia->alterarTransferencia();
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['boItbi'] == 'true') {
                $js = " alertaAviso('".$pgForm."','Inscrição Imobiliária:".$inInscricaoMascara."','".Sessao::getId()."');\n";
                $js = " window.parent.close()";
                SistemaLegado::executaFrameOculto($js);
            } else {
                SistemaLegado::alertaAviso($pgList.$stLink."&stAcao=alterar","Inscrição Imobiliária: ".$inInscricaoMascara,"alterar","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "efetivar":

        $obErro = new Erro;

        $arDocumentosSessao  = Sessao::read('Documentos');
        $arAdquirentesSessao = Sessao::read('Adquirentes');

        $inInscricaoMascara = str_pad($_REQUEST["inInscricaoImobiliaria"],strlen($_REQUEST['stInscricaoMascara']),"0",STR_PAD_LEFT);
        $obRCIMTransferencia->setCodigoTransferencia ( $_REQUEST["inCodigoTransferencia" ] );
        $obRCIMTransferencia->setInscricaoMunicipal  ( $_REQUEST["inInscricaoImobiliaria"] );
        $obRCIMTransferencia->setMatriculaRegImov    ( $_REQUEST["stCodigoMatricula"     ] );
        $obRCIMTransferencia->setDataEfetivacao      ( $_REQUEST["stDataEfetivacao"      ] );
        $obRCIMTransferencia->setObservacao          ( $_REQUEST["stJustificativa"       ] );
        $obRCIMTransferencia->setDocumentos          ( $arDocumentosSessao  );
        $obRCIMTransferencia->setAdquirentes         ( $arAdquirentesSessao );
        $obRCIMTransferencia->setEfetivacao          ( 't'                                 );

       // $obRCIMTransferencia->verificaPagamentoImovelITBI ( $rsRecordSet );

        if ( !VerificaDocumentacaoEntregue( $arDocumentosSessao )) {
            $obErro->setDescricao ('Documentos obrigatórios não foram entregues!');
        }
/*
        if ( !$obErro->ocorreu() ) {
            if ( $rsRecordSet->getNumLinhas() > 0 ) {
                if ( !$rsRecordSet->getCampo("valor_pago") ) {
                    $obErro->setDescricao ('Pagamento do ITBI não foi realizado');
                }
            } else {
                $obErro->setDescricao ('Pagamento do ITBI não foi realizado');
            }
        }
*/

        if ( !$obErro->ocorreu() ) {
            // dados do imovel
            $obRCIMImovel = new RCIMImovel( new RCIMLote);
            $obRCIMImovel->setNumeroInscricao ( $_REQUEST["inInscricaoImobiliaria"]);
            $obRCIMImovel->listarImoveisLista($rsImoveis);

            $obErro = $obRCIMTransferencia->efetivarTransferencia();
        }
        if ( !$obErro->ocorreu() ) {
            if ( $_REQUEST["boSeguir"])
                alertaAvisoRedirect("../imovel/FMManterImovelLote.php?stAcao=alterar&inInscricaoMunicipal=".$_REQUEST['inInscricaoImobiliaria']."&inCodigoLote=".$rsImoveis->getCampo("cod_lote")."&inCodigoLocalizacao=".$rsImoveis->getCampo("cod_localizacao")."&inCodigoSubLote=".$rsImoveis->getCampo("cod_sublote")."&stTipoLote=".$rsImoveis->getCampo("tipo_lote"),"Número da inscrição: ".$_REQUEST['inInscricaoImobiliaria'],"alterar","aviso",Sessao::getId(),"../imovel","179" );
            else
                SistemaLegado::alertaAviso($pgList,"Inscrição Imobiliária: ".$inInscricaoMascara,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "cancelar":
        $obRCIMTransferencia->setCodigoTransferencia ( $_REQUEST["inCodigoTransferencia"] );
        $obRCIMTransferencia->setDataCancelamento    ( $_REQUEST["stDataEfetivacao"     ] );
        $obRCIMTransferencia->setMotivo              ( $_REQUEST["stJustificativa"      ] );
        $obErro = $obRCIMTransferencia->cancelarTransferencia();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Inscrição Imobiliária: ".$_REQUEST['inInscricaoMascara'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
}
?>
