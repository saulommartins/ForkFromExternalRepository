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
  * Página Oculta da Configuração de Orgão
  * Data de Criação: 14/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: OCManterConfiguracaoOrgao.php 64779 2016-03-31 14:29:57Z michel $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoOrgao.class.php";

function processarForm($boExecuta = false, $stArquivo = "Form", $stAcao = "manter")
{
    switch ($stAcao) {
        case "manter":
            $stJs .= montaListaResponsavel();
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaListaResponsavel()
{
    $rsRecordSet = new RecordSet();
    if (Sessao::read('arResponsaveis') != "") {
        $rsRecordSet->preenche(Sessao::read('arResponsaveis'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Responsáveis pela Entidade" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "CGM" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome Responsável" );
    $obLista->ultimoCabecalho->setWidth( 37 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo de Responsável" );
    $obLista->ultimoCabecalho->setWidth( 27 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data de Início" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data de Término" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "num_cgm" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_tipo_responsavel" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_inicio" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_fim" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarResponsavel');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirResponsavel');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnCGMsResponsaveis').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposContador( $inTipoResponsavel )
{
    $obFormulario = new Formulario();

    $obTxtCRCContador = new TextBox();
    $obTxtCRCContador->setRotulo   ( '*CRC' );
    $obTxtCRCContador->setName     ( 'stCRCContador' );
    $obTxtCRCContador->setId       ( 'stCRCContador' );
    $obTxtCRCContador->setMaxLength( 11 );
    $obTxtCRCContador->setValue    ( $inCrcContador );

    $obTUF = new TUF();
    $stFiltro = " WHERE cod_pais = 1 ";
    $stOrder = " sigla_uf ASC ";
    $obTUF->recuperaTodos( $rsUF, $stFiltro, $stOrder );

    $obCmbUFContador = new Select;
    $obCmbUFContador->setName       ( "stSiglaUF" );
    $obCmbUFContador->setId         ( "stSiglaUF" );
    $obCmbUFContador->setRotulo     ( "*UF CRC" );
    $obCmbUFContador->setTitle      ( "Selecione o estado do CRC." );
    $obCmbUFContador->setNull       ( true  );
    $obCmbUFContador->setCampoId    ( "[sigla_uf]" );
    $obCmbUFContador->setCampoDesc  ( "[sigla_uf]" );
    $obCmbUFContador->addOption     ( "", "Selecione" );
    $obCmbUFContador->preencheCombo ( $rsUF );
    $obCmbUFContador->setValue      ( $stUFContador );

    $obFormulario->addComponente( $obTxtCRCContador );
    $obFormulario->addComponente( $obCmbUFContador );
    $obFormulario->montaInnerHTML();

    if ( $inTipoResponsavel == 2 )
        $stJs .= "d.getElementById('spnCamposContador').innerHTML = '".$obFormulario->getHTML()."';";
    else
        $stJs .= "d.getElementById('spnCamposContador').innerHTML = '';";

    return $stJs;
}

function incluirResponsavel()
{
    switch($_REQUEST["inTipoResponsavel"]){
        case 1:
            $stNomeResponsavel = "Gestor";
        break;
        case 2:
            $stNomeResponsavel = "Contador";
        break;
        case 3:
            $stNomeResponsavel = "Controle Interno";
        break;
        case 4:
            $stNomeResponsavel = "Ordenador de Despesa por Delegação";
        break;
        case 5:
            $stNomeResponsavel = "Informações - Folha de Pagamento";
        break;
    }

    if ( $_REQUEST["stHdnAcao"] != "alterar") {
        $obTTCEMGConfiguracaoOrgao = new TTCEMGConfiguracaoOrgao();

        $obErro = new Erro();
        if ( $_REQUEST["inNumCGM"] == "" ) {
            return "alertaAviso('Preencher o campo CGM Responsável.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["inTipoResponsavel"] == "" ) {
            return "alertaAviso('Preencher o campo Tipo Responsável.','form','erro','".Sessao::getId()."');\n";
        } else if ( $_REQUEST["inTipoResponsavel"] == 2 ) {
            if ( $_REQUEST["stCRCContador"] == "" ) {
                return "alertaAviso('Preencher o campo CRC.','form','erro','".Sessao::getId()."');\n";
            }
            if ( $_REQUEST["stSiglaUF"] == "" ) {
                return "alertaAviso('Preencher o campo UF CRC.','form','erro','".Sessao::getId()."');\n";
            }
        }
        if ( $_REQUEST["dtInicio"] == "" ) {
            return "alertaAviso('Preencher o campo Data Início','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["dtFim"] == "" ) {
            return "alertaAviso('Preencher o campo Data Término.','form','erro','".Sessao::getId()."');\n";
        }

        $arResponsaveis = Sessao::read('arResponsaveis');
        $arNovoResponsavel = array();
        $arNovoResponsavel["cod_entidade"] = $_REQUEST["hdnCodEntidade"];
        $arNovoResponsavel["num_cgm"] = $_REQUEST["inNumCGM"];
        $arNovoResponsavel["nom_cgm"] = $_REQUEST["stNomCGM"];
        $arNovoResponsavel["nom_tipo_responsavel"] = $stNomeResponsavel;
        $arNovoResponsavel["dt_inicio"] = $_REQUEST["dtInicio"];
        $arNovoResponsavel["dt_fim"] = $_REQUEST["dtFim"];
        $arNovoResponsavel["crc_contador"] = $_REQUEST["stCRCContador"];
        $arNovoResponsavel["uf_crccontador"] = $_REQUEST["stSiglaUF"];
        $arNovoResponsavel["cargo_ordenador_despesa"] = $_REQUEST["stCargoGestor"];
        $arNovoResponsavel["email"] = $_REQUEST["stEMail"];
        $arNovoResponsavel["tipo_responsavel"] = $_REQUEST["inTipoResponsavel"];
        $arNovoResponsavel["inId"] = count($arResponsaveis);

        if ( $arResponsaveis != "" ) {
            foreach ($arResponsaveis as $arrResponsaveis) {
                if ($arrResponsaveis['num_cgm'] == $arNovoResponsavel['num_cgm'] &&
                    $arrResponsaveis['tipo_responsavel'] == $arNovoResponsavel['tipo_responsavel'] &&
                    $arrResponsaveis['cod_entidade'] == $arNovoResponsavel['cod_entidade'])
                {
                    if ( SistemaLegado::comparaDatas($arrResponsaveis['dt_fim'], $arNovoResponsavel['dt_inicio']) ) {
                        $obErro->setDescricao("Esta CGM já está cadastrado para esse Tipo de Responsável para essa entidade! No período informado!");
                    } else {
                        $obErro->setDescricao("Esta CGM já está cadastrado para esse Tipo de Responsável para essa entidade!");
                    }
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            $arResponsaveis[] = $arNovoResponsavel;
            Sessao::write('arResponsaveis',$arResponsaveis);
        }
    } else {
        $obErro  = new Erro();

        $arResponsaveis = Sessao::read('arResponsaveis');
        foreach ($arResponsaveis as $key => $arResponsavel) {
            if ($arResponsavel['inId'] == $_REQUEST['hdnInId']) {
                    $arResponsaveis[$key]['cod_entidade']     = $_REQUEST['hdnCodEntidade'];
                    $arResponsaveis[$key]['num_cgm']          = $_REQUEST['inNumCGM'];
                    $arResponsaveis[$key]['nom_cgm']          = $_REQUEST['stNomCGM'];
                    $arResponsaveis[$key]["nom_tipo_responsavel"] = $stNomeResponsavel;
                    $arResponsaveis[$key]['dt_inicio']        = $_REQUEST['dtInicio'];
                    $arResponsaveis[$key]['dt_fim']           = $_REQUEST['dtFim'];
                    $arResponsaveis[$key]['crc_contador']     = $_REQUEST['stCRCContador'];
                    $arResponsaveis[$key]['uf_crccontador']   = $_REQUEST['stSiglaUF'];
                    $arResponsaveis[$key]['cargo_ordenador_despesa']   = $_REQUEST['stCargoGestor'];
                    $arResponsaveis[$key]['email']            = $_REQUEST['stEMail'];
                    $arResponsaveis[$key]['tipo_responsavel'] = $_REQUEST['inTipoResponsavel'];

                    Sessao::write('arResponsaveis',$arResponsaveis);
                break;
            }
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= montaListaResponsavel();

        $stJs .= "f.inNumCGM.value = ''; \n";
        $stJs .= "d.getElementById('stNomCGM').innerHTML = '&nbsp;';\n";
        $stJs .= "f.inTipoResponsavel.value = '';\n";
        $stJs .= "f.stCargoGestor.value = '';\n";

        if ( $_REQUEST["inTipoResponsavel"] == 2 ) {
            $stJs .= "f.stCRCContador.value = '';\n";
            $stJs .= "f.stSiglaUF.value = '';\n";
            $stJs .= "d.getElementById('spnCamposContador').innerHTML = '';";
        }

        $stJs .= "f.dtInicio.value = '';\n";
        $stJs .= "f.dtFim.value = '';\n";
        $stJs .= "f.stEMail.value = '';\n";
        $stJs .= "f.hdnInId.value = '';\n";
        $stJs .= "f.stHdnAcao.value = '';\n";
        $stJs .= "f.btIncluirResponsavel.value = 'Incluir';\n";
    }

    return $stJs;
}

function excluirResponsavel()
{
    $arTemp = $arTempRemovido = array();

    $arResponsaveis = Sessao::read('arResponsaveis');
    $arResponsaveisRemovidos = Sessao::read('arResponsaveisRemovidos');

    foreach ($arResponsaveis as $arResponsavel) {
        if ($arResponsavel['inId'] != $_GET['inId']) {
            $arTemp[] = $arResponsavel;
        } else {
            $arTempRemovido[] = $arResponsavel;
        }
    }

    $arResponsaveis = $arTemp;
    $arResponsaveisRemovidos[] = $arTempRemovido;

    Sessao::write('arResponsaveisRemovidos', $arResponsaveisRemovidos);
    Sessao::write('arResponsaveis', $arResponsaveis);

    $stJs .= montaListaResponsavel();

    SistemaLegado::executaFrameOculto($stJs);
}

function alterarResponsavel()
{
    $arResponsaveis = Sessao::read('arResponsaveis');
    foreach($arResponsaveis as $arResponsavel){
        if ( $arResponsavel["inId"] == $_GET["inId"] ) {
            $stJs .= "f.inNumCGM.value = '".$arResponsavel['num_cgm']."';\n";
            $stJs .= "d.getElementById('stNomCGM').innerHTML = '".$arResponsavel['nom_cgm']."';\n";
            $stJs .= "f.stNomCGM.value = '".$arResponsavel['nom_cgm']."';\n";
            $stJs .= "d.getElementById('inTipoResponsavel').focus();\n";
            $stJs .= "f.inTipoResponsavel.value = '".$arResponsavel['tipo_responsavel']."';\n";
            $stJs .= "d.getElementById('stCargoGestor').focus();\n";
            $stJs .= "f.stCargoGestor.value = '".$arResponsavel['cargo_ordenador_despesa']."';\n";

            if ( $arResponsavel["tipo_responsavel"] == 2 ) {
                $stJs .= montaCamposContador($arResponsavel["tipo_responsavel"]);
                $stJs .= "window.parent[2].frm.stCRCContador.value = '".$arResponsavel['crc_contador']."';\n";
                $stJs .= "window.parent[2].frm.stSiglaUF.value = '".$arResponsavel['uf_crccontador']."';\n";
            }

            $stJs .= "f.dtInicio.value = '".$arResponsavel['dt_inicio']."';\n";
            $stJs .= "f.dtFim.value = '".$arResponsavel['dt_fim']."';\n";
            $stJs .= "f.stEMail.value = '".$arResponsavel['email']."';\n";
            $stJs .= "f.stHdnAcao.value = 'alterar';\n";
            $stJs .= "f.btIncluirResponsavel.value = 'Alterar';\n";
            $stJs .= "f.hdnInId.value = '".$arResponsavel["inId"]."';\n";
        }
    }
    return $stJs;
}

switch ($request->get('stCtrl')) {
    case 'verificaTipoResponsavel':
        $stJs .= montaCamposContador($request->get('tipoResponsavel'));
    break;
    case 'incluirResponsavel':
        $stJs .= incluirResponsavel();
    break;
    case 'excluirResponsavel':
        $stJs .= excluirResponsavel();
    break;
    case 'alterarResponsavel':
        $stJs .= alterarResponsavel();
    break;
}

if (isset($stJs)) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>