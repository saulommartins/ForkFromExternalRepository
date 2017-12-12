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
  * Página de Formulario Oculto
  * Data de criação : 13/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: OCAvaliarImovel.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.9  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_FW_URBEM."MontaLocalizacao.class.php");
include_once (CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php");
include_once( CAM_GT_CIM_NEGOCIO . "RCIMImovel.class.php"        );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php"              );
include_once( CAM_GT_CIM_NEGOCIO . "RCIMTransferencia.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AvaliarImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obMontaLocalizacao = new MontaLocalizacao;
$obRARRAvaliacaoImobiliaria = new RARRAvaliacaoImobiliaria;
$obErro = new Erro;

/* Lista Proprietarios */
function ListaProprietarios($rsRecordset, $boExecuta=true)
{
                    $obLista = new Lista;
                    $obLista->setMostraPaginacao                   ( false                                       );
                    $obLista->setTitulo                            ( "Lista de Proprietários"                    );
                    $obLista->setRecordSet                         ( $rsRecordset                                );
                    $obLista->addCabecalho                         (                                             );
                    $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
                    $obLista->ultimoCabecalho->setWidth            ( 3                                           );
                    $obLista->commitCabecalho                      (                                             );
                    $obLista->addCabecalho                         (                                             );
                    $obLista->ultimoCabecalho->addConteudo         ( "CGM"                                       );
                    $obLista->ultimoCabecalho->setWidth            ( 10                                          );
                    $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
                    $obLista->commitCabecalho                      (                                             );
                    $obLista->addCabecalho                         (                                             );
                    $obLista->ultimoCabecalho->addConteudo         ( "Nome"                                      );
                    $obLista->ultimoCabecalho->setWidth            ( 64                                          );
                    $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
                    $obLista->commitCabecalho                      (                                             );
                    $obLista->addCabecalho                         (                                             );
                    $obLista->ultimoCabecalho->addConteudo         ( "Quota Atual(%)"                            );
                    $obLista->ultimoCabecalho->setWidth            ( 10                                          );
                    $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
                    $obLista->commitCabecalho                      (                                             );
                    $obLista->addDado                              (                                             );
                    $obLista->ultimoDado->setCampo                 ( "cgm"                                       );
                    $obLista->commitDado                           (                                             );
                    $obLista->addDado                              (                                             );
                    $obLista->ultimoDado->setCampo                 ( "nome"                                      );
                    $obLista->commitDado                           (                                             );
                    $obLista->addDado                              (                                             );
                    $obLista->ultimoDado->setCampo                 ( "quota"                                     );
                    $obLista->commitDado                           (                                             );

                    $obLista->montaHTML();
                    $stHtml = $obLista->getHTML();
                    $stHtml = str_replace("\n","",$stHtml);
                    $stHtml = str_replace("  ","",$stHtml);
                    $stHtml = str_replace("'","\\'",$stHtml);

                    // preenche a lista com innerHTML
                    $stJs .= "d.getElementById('spnProprietarios').innerHTML = '".$stHtml."';";

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }

}
/************ Fim - Lista de Proprietarios *************/

switch ($_REQUEST["stCtrl"]) {
    case "preencheProxCombo":
        $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );

    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
        $obMontaLocalizacao->preencheCombos();
    break;
    case "calculaTotal":
        $flTmpTerritorial = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTerritorialInformado'])), 2, '.', '' );
        $flTmpPredial     = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flPredialInformado'])), 2, '.', '' );
        $flTmpTotal       = $flTmpTerritorial + $flTmpPredial;
        $stJs .= "f.flTotalInformado.value = '".number_format($flTmpTotal, 2, ',', '.')."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaFuncao":
        $obRARRAvaliacaoImobiliaria->obRFuncao->setCodFuncao( $_REQUEST['inCodigoFormula'] );
        $obRARRAvaliacaoImobiliaria->obRFuncao->consultar();
        if ( $obRARRAvaliacaoImobiliaria->obRFuncao->getNomeFuncao() ) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$obRARRAvaliacaoImobiliaria->obRFuncao->getNomeFuncao()."';\n";
            $stJs .= "SistemaLegado::alertaAviso('','form','aviso','".Sessao::getId()."', '../');";
        } else {
            $stMsg = "Função inválida.";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "SistemaLegado::alertaAviso('".$stMsg."(".$_REQUEST["stDescricao"].")','form','erro','".Sessao::getId()."', '../');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaCGM":
        $stText = "inNumCGM";
        $stSpan = "stNomCGM";
        if ($_REQUEST[ $stText ] != "") {
            $obRARRAvaliacaoImobiliaria->obRCIMImovel->addProprietario();
            $obRARRAvaliacaoImobiliaria->obRCIMImovel->roUltimoProprietario->obRCGM->setNumCGM( $_REQUEST[ $stText ] );
            $obRARRAvaliacaoImobiliaria->obRCIMImovel->roUltimoProprietario->obRCGM->consultar( $rsCGM );
            $stNull = "&nbsp;";

            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.'.$stText.'.value = "";';
                $stJs .= 'f.'.$stText.'.focus();';
                $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
                $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
            } else {
               $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        } else {
            $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "habilitaITBI":
        if ($_REQUEST["boChecado"] == 'true') {

            if (!$dtVencimento) {
                $nextmonth = mktime (0, 0, 0, date("m")+1, date("d"),  date("Y"));
                $dtVencimento =strftime("%d/%m/%Y", $nextmonth);
            }

            $obTxtVencimento = new Data;
            $obTxtVencimento->setName               ( "dtVencimento" );
            //$obTxtVencimento->setRotulo             ( "Vencimento da Cobrança de ITBI" );
            $obTxtVencimento->setRotulo             ( "Vencimento da Cobrança" );
            $obTxtVencimento->setValue              ( $dtVencimento );

            $obRdoCarneNao = new Radio;
            $obRdoCarneNao->setRotulo   ( "Emissão de Carnês" );
            $obRdoCarneNao->setName     ( "boCarne" );
            $obRdoCarneNao->setLabel    ( "Não emitir" );
            $obRdoCarneNao->setValue    ( "não" );
            $obRdoCarneNao->setChecked  ( false );
            $obRdoCarneNao->setNull     ( false );

            $obRdoCarneSim = new Radio;
            $obRdoCarneSim->setRotulo   ( "Emissão de Carnês" );
            $obRdoCarneSim->setName     ( "boCarne" );
            $obRdoCarneSim->setLabel    ( "Impressão local" );
            $obRdoCarneSim->setValue    ( "sim" );
            $obRdoCarneSim->setChecked  ( true );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obTxtVencimento );
            $obFormulario->agrupaComponentes( array( $obRdoCarneNao, $obRdoCarneSim ) );
            $obFormulario->show();
        } else {
            echo "";
        }
    break;

    case "MontarListas":
        $obRCIMImovel        = new RCIMImovel(new RCIMLote);
        $obRCIMProprietario  = new RCIMProprietario ( $obRCIMImovel );
        $obRCGM              = new RCGM;
        /* Listar Proprietarios */
        /* Se estiver tudo certo, busca proprietarios do imovel */
        $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
        /* Recordset com os proprietarios do imovel */
        $obRCIMProprietario->listarProprietariosPorImovel($rsProprietarios );
        $arProprietarios = array();
        $inCont = 0;

        Sessao::write( 'Proprietarios', $rsProprietarios );

        while (!$rsProprietarios->eof()) {
            $inNumCgm   = $rsProprietarios->getCampo("numcgm"   );
            $flQuota    = $rsProprietarios->getCampo("cota"     );
            $obRCGM->setNumCGM  ($inNumCgm  );
            $obRCGM->consultar  ( $rsCGM    );
            $arProprietarios[$inCont][ 'inSeq'   ] = $inCont     ;
            $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm   ;
            $arProprietarios[$inCont][ 'nome'    ] = $obRCGM->getNomCGM();
            $arProprietarios[$inCont][ 'quota'   ] = $flQuota;
            $rsProprietarios->proximo();
            $inCont++;

            }
        $rsProprietarios = new Recordset;
        $rsProprietarios->preenche($arProprietarios);
       // echo "Recordset Fora:";print_r($rsProprietarios);
        $stJs .=  ListaProprietarios        ( $rsProprietarios , false     );
        /* Fim de Listar Proprietarios */

        /* Lista de Documentos */
        $obRCIMTransferencia = new RCIMTransferencia;
        $rsRecordSet = new Recordset;

        //echo 'codTransf: '. $_REQUEST['inCodigoTransferencia']; exit;
        $obRCIMTransferencia->setCodigoTransferencia( $_REQUEST['inCodigoTransferencia'] );
        $obRCIMTransferencia->setCodigoNatureza( $_REQUEST['inCodigoNatureza'] );
        $obRCIMTransferencia->consultarDocumentos();

        Sessao::write( 'Documentos', $obRCIMTransferencia->getDocumentos() );
        $arDocumentos = $obRCIMTransferencia->getDocumentos();
        $cont = 0;
        $boAutorizacaoCalcular = true;
        while ( $cont < count ( $arDocumentos ) ) {
            if ($arDocumentos[$cont]['entregue'] == 'f') {
                $boAutorizacaoCalcular = false;
            }
            $cont++;
        }

        if ( is_array( $arDocumentos ) ) {
            $rsRecordSet->preenche ( $arDocumentos );
        }

        $stJs .= "f.boAutorizacaoCalcular.value = '". $boAutorizacaoCalcular ."';";

        //$stJs .= listaDocumentos   ( $rsRecordSet, false           );
        /* Fim de Listar Documentos */

    break;
}
if( $stJs )
    SistemaLegado::executaFrameOculto($stJs);
