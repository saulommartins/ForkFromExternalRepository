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
    * Página de Formulario de Inclusao/Alteracao Atividade
    * Data de Criação   : 20/11/2004

    * @author Tonismar Régis Bernardo
    * @ignore

    * $Id: OCManterAtividade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.07

*/

/*
$Log$
Revision 1.9  2007/05/17 21:12:48  cercato
Bug #9273#

Revision 1.8  2007/03/02 12:18:14  rodrigo
Bug #7527#

Revision 1.7  2007/02/23 19:52:47  rodrigo
Bug #7527#

Revision 1.6  2007/02/05 17:28:14  cercato
Bug #7527#

Revision 1.5  2006/09/15 14:32:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php"          );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php"     );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCnae.class.php"        );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelCnae.class.php"   );
include_once ( CAM_GT_CEM_COMPONENTES."MontaCnae.class.php"   );
//include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtividade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obMontaAtividade = new MontaAtividade;
$obRCEMServico    = new RCEMServico;
$obRCEMAtividade  = new RCEMAtividade;
$obMontaServico   = new MontaServico;
$obMontaServico->setCadastroAtividade( true );
$obMontaServico->setCodigoVigenciaServico   ( $_REQUEST["inCodigoVigenciaServico"] );
$obMontaAtividade->setCodigoVigencia        ( $_REQUEST["inCodigoVigencia"] );
$obMontaCnae = new MontaCnae;
$obErro      = new Erro;

function listarServico($arRecordSet, $boExecuta=true)
{
    global $obRegra;

    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Serviços" );

        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome" );
        $obLista->ultimoCabecalho->setWidth( 82 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inCodigoEstrutural" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNomeServico" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiServico');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

    }

    if ($_REQUEST["stAcao"] == "alterar") {
        $obRCEMAtividade = new RCEMAtividade;

        $obRCEMAtividade->setCodigoAtividade( $_REQUEST["inCodigoAtividade"] );
        $obRCEMAtividade->setNomeAtividade  ( $_REQUEST["stNomeAtividade"]   );
        $obRCEMAtividade->addAtividadeCnae();
        $obRCEMAtividade->roUltimoCnae->listarCnaeAtividade( $rsCnaeAtividade );

        $stCnae        = $rsCnaeAtividade->getCampo ("nom_atividade_cnae");
        $inCodigoCnae  = $rsCnaeAtividade->getCampo ("cod_cnae");
        //$stJs .= "f.inNumCnae.value = '$inCodigoCnae';";
        //$stJs .= "d.getElementById('inNumCnae').innerHTML = '$stCnae';";
    }

    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnServicoCadastrado').innerHTML = '".$stHtml."';";
    $stJs .= "f.stChaveServico.value = '';";
    $stJs .= "d.getElementById('spnServico').innerHTML = '&nbsp;';";

    $obMontaServico = new MontaServico;
    $obFormulario   = new Formulario;
    $obMontaServico->setCadastroAtividade( true );
    $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigenciaServico"] );
    $obMontaServico->geraFormulario( $obFormulario );
    $obFormulario->montaInnerHTML();
    $stHtmlServico = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnServico').innerHTML = '".$stHtmlServico."';";

    if ($boExecuta==true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

switch ($_REQUEST["stCtrl"]) {
    case "preencheProxCombo":
        $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }

        $arChaveLocal = explode("§" , $stChaveLocal );
        $obMontaAtividade->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );

        $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaAtividade->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );
        break;

    case "preencheCombosAtividade":
        $obMontaAtividade->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaAtividade->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaAtividade->setValorReduzido ( $_REQUEST["stChaveAtividade"] );
        $obMontaAtividade->preencheCombosAtividade();
        break;

    case "preencheServicoVigencia":
        $obRCEMServico->setCodigoVigencia( $_REQUEST["inCodigoVigenciaServico"] );
        $js .= "limpaSelect(f.inCodServico_1,0); \n";
        $js .= "f.inCodServico_1[0] = new Option('Selecione','', 'selected');\n";
        $obRCEMServico->listarServico( $rsListaServico );
        $inCount = 1;
        while ( !$rsListaServico->eof() ) {
            $stChaveServico  = $rsListaServico->getCampo( "cod_nivel"      ).".";
            $stChaveServico .= $rsListaServico->getCampo( "cod_servico"    ).".";
            $stChaveServico .= $rsListaServico->getCampo( "valor"          ).".";
            $stChaveServico .= $rsListaServico->getCampo( "valor_reduzido" );
            $stNomeServico   = $rsListaServico->getCampo( "nom_servico"    );
            $js .= "f.inCodServico_1.options[$inCount] = ";
            $js .= "new Option('".$stNomeServico."','".$stChaveServico."',''); \n";
            $inCount++;
            $rsListaServico->proximo();
        }
        sistemaLegado::executaFrameOculto($js);
    break;
    case "preencheVigencia":
        $stJs .= "limpaSelect(f.stDataVigencia,0); \n";
        $stJs .= "f.stDataVigencia[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST["inCodigoVigenciaServico"]) {
            $obRCEMServico->listarVigencia( $rsVigencia );
            $inContador = 1;
        }
        $obRCEMServico->setCodigoVigencia( $_REQUEST["inCodigoVigenciaServico"] );
        while ( !$rsVigencia->eof() ) {
            if ( $_REQUEST["inCodigoVigenciaServico"] == $rsVigencia->getCampo( "cod_vigencia" ) ) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $inCodigoVigencia  = $rsVigencia->getCampo( "cod_vigencia" );
            $stDataVigencia    = $rsVigencia->getCampo( "dt_inicio"  );
            $stJs .= "f.stDataVigencia.options[$inContador] = new Option('".$stDataVigencia."','".$inCodigoVigencia."','".$stSelected."'); \n";
            $inContador++;
            $rsVigencia->proximo();
        }

        $obFormulario   = new Formulario;
        $obMontaServico->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stJs .= "d.getElementById('spnServico').innerHTML = '".$stHtml."';\n";

        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "preencheProxComboServico":
        $stNomeComboServico = "inCodServico_".( $_REQUEST["inPosicaoServico"] - 1);
        $stChaveLocalServico = $_REQUEST[$stNomeComboServico];
        $inPosicaoServico = $_REQUEST["inPosicaoServico"];
        if ( empty( $stChaveLocalServico ) and $_REQUEST["inPosicaoServico"] > 2 ) {
            $stNomeComboServico = "inCodServico_".( $_REQUEST["inPosicaoServico"] - 2);
            $stChaveLocalServico = $_REQUEST[$stNomeComboServico];
            $inPosicaoServico = $_REQUEST["inPosicaoServico"] - 1;
        }
        $arChaveLocalServico = explode("-" , $stChaveLocalServico );
        $obMontaServico->setCodigoVigenciaServico    ( $_REQUEST["inCodigoVigenciaServico"] );
        $obMontaServico->setCodigoNivelServico       ( $arChaveLocalServico[0] );
        $obMontaServico->setCodigoServico            ( $arChaveLocalServico[1] );
        $obMontaServico->setValorReduzidoServico     ( $arChaveLocalServico[3] );
        $obMontaServico->preencheProxCombo           ( $inPosicaoServico , $_REQUEST["inNumNiveisServico"] );
    break;
    case "preencheCombosServico":
        $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigenciaServico"]   );
        $obMontaServico->setCodigoNivelServico   ( $_REQUEST["inCodigoNivelServico"]      );
        $obMontaServico->setValorReduzidoServico ( $_REQUEST["stChaveServico"] );
        $obMontaServico->preencheCombos();
    break;
    case "buscaCodigoVigencia":
        if ($_REQUEST['inCodigoVigenciaServico'] != "") {
            $obFormulario   = new Formulario;
            $obMontaServico = new MontaServico;
            $obMontaServico->setCodigoVigenciaServico( $_REQUEST['inCodigoVigenciaServico'] );
            $obMontaServico->setCadastroAtividade( true );
            $obMontaServico->geraFormulario( $obFormulario );
            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();
            $stJs .= "d.getElementById('spnServico').innerHTML = '".$stHtml."';\n";
            $stJs .= "f.inCodigoVigenciaServico.value = '".$_REQUEST["stDataVigencia"]."'\n";
        } else {
            $stJs .= "d.getElementById('spnServico').innerHTML = ''\n";
        }
        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "MontaServico":
        $inTMP = $_REQUEST["inNumNiveisServico"] - 1;
        $stTMP = "inCodServico_".$inTMP;
        $arCodigoServico = explode("-",$_REQUEST[$stTMP]);

        $stMensagem = false;

        if ($arCodigoServico[1]) {
            $arServicosSessao = Sessao::read( "Servicos" );
            foreach ($arServicosSessao as $campo => $valor) {
                if ($arServicosSessao[$campo]['inCodigoServico'] == $arCodigoServico[1]) {
                    $stMensagem = " Serviço ".$arCodigoServico[1]." - já existe.";
                }
            }
        } else {
//          $stMensagem = "Serviço ".$arCodigoServico[1]." - não existe.";
            $stMensagem = "Serviço Inválido!";
        }

        if ($stMensagem == "") {
            $obRCEMServico = new RCEMServico;
            $rsRecordSet = new Recordset;
            $rsServico = new Recordset;
            $rsRecordSet->preenche( Sessao::read( "Servicos" ) );
            $rsRecordSet->setUltimoElemento();

            $obRCEMServico->setCodigoServico( $arCodigoServico[1]  );
            $obRCEMServico->listarServico( $rsServico );

            $obRCEMServico->setNomeServico  ( $rsServico->getCampo( "nom_servico" ) );

            $inUltimoId = $rsRecordSet->getCampo("inId");
            if (!$inUltimoId) {
                $inProxId = 1;
            } else {
                $inProxId = $inUltimoId + 1;
            }

            $arElementos['inId']                 = $inProxId;
            $arElementos['inCodigoServico']      = $obRCEMServico->getCodigoServico();
            $arElementos['stNomeServico']        = $obRCEMServico->getNomeServico();
            $arElementos['inCodigoEstrutural']   = $_REQUEST['stChaveServico'];
            $arServicosSessao = Sessao::read( "Servicos" );
            $arServicosSessao[]        = $arElementos;
            Sessao::write( "Servicos", $arServicosSessao );
            listarServico( $arServicosSessao );
        } else {
//          $stJs = "alertaAviso('@válido. ($stMensagem)','form','erro','".Sessao::getId()."');";
            $stJs = "alertaAviso('@".$stMensagem."','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto($stJs);
        }
        break;

    case "ListaServico":
        listarServico( Sessao::read( "Servicos" ) );
        break;

    case "limparServicoGeral":
        Sessao::write( "Servicos", array() );
    case "limparServico":
        //Sessao::write( "Servicos", array() );
        //$stJs .= "d.getElementById('spnServicoCadastrado').innerHTML = '';\n";
        $obMontaServico = new MontaServico;
        $obFormulario   = new Formulario;
        $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigenciaServico"] );
        $obMontaServico->setCadastroAtividade( true );
        $obMontaServico->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtmlServico = $obFormulario->getHTML();
        $stJs .= "d.getElementById('spnServico').innerHTML = '".$stHtmlServico."';";
        $arServicosSessao = Sessao::read( "Servicos" );
        $stJs .= listarServico( $arServicosSessao, false );
        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "excluiServico":
        $id = $_REQUEST['inId'];
        $stMensagem = false;

        if ($stMensagem==false) {
            $arServicosSessao = Sessao::read( "Servicos" );
            reset($arServicosSessao);
            while ( list( $arId ) = each( $arServicosSessao ) ) {
                if ($arServicosSessao[$arId]["inId"] != $id) {
                    $arElementos['inId']         = $arServicosSessao[$arId]["inId"];
                    $arElementos['inCodigoServico']  = $arServicosSessao[$arId]["inCodigoServico"];
                    $arElementos['stNomeServico']    = $arServicosSessao[$arId]["stNomeServico"];
                    $arElementos['inCodigoEstrutural'] = $arServicosSessao[$arId]["inCodigoEstrutural"];
                    $arTMP[] = $arElementos;
                }
            }

            Sessao::write( 'Servicos', $arTMP );
            listarServico( $arTMP );
        } else {
            $stJs = "sistemaLegado::alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
    break;
    case "buscaCnae":
        if ($_REQUEST["inNumCnae"]) {

            //$obRCEMAtividade->setCodigoAtividade( $_REQUEST["inCodigoAtividade"] );
            $obRCEMAtividade->addAtividadeCnae();
            $obRCEMAtividade->roUltimoCnae->setCodigoCnae( $_REQUEST["inNumCnae"] );
            $obRCEMAtividade->roUltimoCnae->listarCnae( $rsCnaeAtividade );

            $inNumLinhas = $rsCnaeAtividade->getNumLinhas();

            if ($inNumLinhas <= 0) {
                $stJs .= 'f.inNumCnae.value = "";';
                $stJs .= 'f.inNumCnae.focus();';
                $stJs .= 'd.getElementById("inNumCnae").innerHTML = "&nbsp;";';
                $stJs .= "sistemaLegado::alertaAviso('Valor inválido. (".$_REQUEST["inNumCnae"].")','frm','erro','".Sessao::getId()."');";
            } else {
                $stCnae        = $rsCnaeAtividade->getCampo ("nom_atividade");
                $inCodigoCnae  = $rsCnaeAtividade->getCampo ("cod_cnae");
                $stJs .= "f.inNumCnae.value = '$inCodigoCnae';";
                $stJs .= "d.getElementById('inNumCnae').innerHTML = '$stCnae'";
            }
        } else {
            $stJs .= 'f.inNumCnae.value = "";';
            $stJs .= 'f.inNumCnae.focus();';
            $stJs .= 'd.getElementById("inNumCnae").innerHTML = "&nbsp;";';
        }
        sistemaLegado::executaFrameOculto($stJs);
    break;
    /********************************************************************
    */

    case "preencheProxComboCnae":
        $stNomeComboCnae = "inCodCnae_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboCnae];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboCnae = "inCodCnae_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboCnae];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }

        $arChaveLocal = explode("," , $stChaveLocal );

        $obMontaCnae->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaCnae->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaCnae->setCodigoCnae        ( $arChaveLocal[1] );
        $obMontaCnae->boPopUp = false;
        if ($arChaveLocal[0] == 1) {
            $obMontaCnae->setValorReduzido   ( $arChaveLocal[2] );
        }else
            if ($arChaveLocal[0] == 2) {
                $obMontaCnae->setValorReduzido ( substr( $arChaveLocal[3], 0, 4 ) );
            }else
                if ($arChaveLocal[0] == 3) {
                    $obMontaCnae->setValorReduzido ( substr( $arChaveLocal[3], 0, 6 ) );
                }else
                    if ($arChaveLocal[0] == 4) {
                        $obMontaCnae->setValorReduzido ( substr( $arChaveLocal[3], 0, 9 ) );
                    } else {
                        $obMontaCnae->setValorReduzido ( $arChaveLocal[3] );
                    }

        $obMontaCnae->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveisCnae"] );
    break;
    case "preencheCombosCnae":
        $obMontaCnae->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaCnae->setValorReduzido ( $_REQUEST["stChaveCnae"] );
        $obMontaCnae->preencheCombos2();
    break;
}
