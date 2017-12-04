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
    * Página Oculta de Cargo
    * Data de Criação   : 07/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: alex $
    $Date: 2007-12-13 10:44:24 -0200 (Qui, 13 Dez 2007) $

    * Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function gerarSpanCargoEspecialidade()
{
    if ($_GET["boEspecialidade"] == "S") {
        $stJs = gerarCamposEspecialidade();
    } else {
        $stJs = gerarCamposCargo();
    }

    return $stJs;
}

function addCBO(&$obFormulario,&$arComponentes,$boCargo=true)
{
    include_once(CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php");
    $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
    $obRConfiguracaoPessoal->consultar();

    $obBscCBO = new BuscaInner;
    $obBscCBO->setRotulo           ( "CBO"                      );
    $obBscCBO->setTitle            ( "Informe o CBO do Cargo.");
    $obBscCBO->setId               ( "inNomCBO"                 );
    $obBscCBO->obCampoCod->setName ( "inNumCBO"                 );
    $obBscCBO->obCampoCod->setId   ( "inNumCBO"                 );
    $obBscCBO->obCampoCod->setValue( $inNumCBO                  );
    $obBscCBO->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaCBO','inNumCBO');");
    $obBscCBO->setFuncaoBusca( "abrePopUp('".CAM_GRH_PES_POPUPS."cargo/FLProcurarCbo.php','frm','inNumCBO','inNomCBO','fisica','".Sessao::getId()."','800','550')" );
    $obBscCBO->obCampoCod->setMascara($obRConfiguracaoPessoal->getMascaraCBO());

    $obHdnCodCBO = new hidden();
    $obHdnCodCBO->setName("inCodCBO");
    $obHdnCodCBO->setId("inCodCBO");
    $obHdnCodCBO->setValue($inCodCBO);

    $obLnkInsereCBO = new Link;
    $obLnkInsereCBO->setHref  ( "JavaScript:abrePopUp('".CAM_GRH_PES_POPUPS."cargo/FMInserirCBOFrame.php','frm','','','','".Sessao::getId()."&stAcao=incluir','800','600');" );
    $obLnkInsereCBO->setValue ( "Cadastrar CBO" );

    if ($boCargo) {
        $obBscCBO->setNull             ( false                      );
    } else {
        $obBscCBO->setNullBarra        ( false                      );
    }

    $obFormulario->addComponente( $obBscCBO );
    $obFormulario->addComponente( $obLnkInsereCBO );
    $obFormulario->addHidden($obHdnCodCBO);
    $arComponentes[] = $obBscCBO->obCampoCod;
    $arComponentes[] = $obBscCBO;
    $arComponentes[] = $obHdnCodCBO;
}

function addPadrao(&$obFormulario,&$arComponentes,$boCargo=true)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php");
    $obTFolhaPagamentoPadrao = new TFolhaPagamentoPadrao();
    $obTFolhaPagamentoPadrao->recuperaRelacionamento($rsFolhaPagamentoPadrao,"","ORDER BY descricao");
    $rsFolhaPagamentoPadrao->addFormatacao("valor", "NUMERIC_BR");

    $obTxtPadrao = new TextBox;
    $obTxtPadrao->setRotulo              ( "Padrão"                      );
    $obTxtPadrao->setTitle               ( "Informe o padrão vinculado." );
    $obTxtPadrao->setName                ( "inCodPadraoTxt"              );
    $obTxtPadrao->setId                  ( "inCodPadraoTxt"              );
    $obTxtPadrao->setValue               ( $inCodPadraoTxt               );
    $obTxtPadrao->setSize                ( 6                             );
    $obTxtPadrao->setMaxLength           ( 6                             );
    $obTxtPadrao->setInteiro             ( true                          );
    if ($boCargo) {
        $obTxtPadrao->setNull                ( false                         );
    } else {
        $obTxtPadrao->setNullBarra           ( false                         );
    }

    $obCmbPadrao = new Select;
    $obCmbPadrao->setRotulo              ( "Padrão"                );
    $obCmbPadrao->setName                ( "inCodPadrao"           );
    $obCmbPadrao->setId                  ( "inCodPadrao"           );
    $obCmbPadrao->setValue               ( $inCodPadrao            );
    $obCmbPadrao->setStyle               ( "width: 200px"          );
    $obCmbPadrao->setCampoID             ( "cod_padrao"            );
    $obCmbPadrao->setCampoDesc           ( "[descricao] - [valor]" );
    $obCmbPadrao->addOption              ( "", "Selecione"         );

    $obLnkCadastroPadroes = new Link;
    $obLnkCadastroPadroes->setRotulo( "&nbsp;" );
    $obLnkCadastroPadroes->setHref  ( "JavaScript:abrePopUp('".CAM_GRH_FOL_POPUPS."padrao/FMManterPadrao.php','frm','','','','".Sessao::getId()."&stOrigem=CARGO&acao=1040','800','600');" );
    $obLnkCadastroPadroes->setValue ( "Cadastrar Padrão" );

    if ($boCargo) {
        $obCmbPadrao->setNull( false );
    } else {
        $obCmbPadrao->setNullBarra( false );
    }
    $obCmbPadrao->preencheCombo          ( $rsFolhaPagamentoPadrao );

    $obFormulario->addComponenteComposto( $obTxtPadrao, $obCmbPadrao );
    $obFormulario->addComponente($obLnkCadastroPadroes);

    $arComponentes[] = $obTxtPadrao;
    $arComponentes[] = $obCmbPadrao;
    $arComponentes[] = $obLnkCadastroPadroes;
}

function addRegimeSubDivisao(&$obFormulario,$boCargo=true)
{
    $obTxtVagas = new TextBox;
    $obTxtVagas->setName                ( "stVagas_[cod_regime]_[cod_sub_divisao]" );
    $obTxtVagas->setId                  ( "stVagas_[cod_regime]_[cod_sub_divisao]" );
    $obTxtVagas->setInteiro             ( true      );
    $obTxtVagas->setSize                ( 15        );
    $obTxtVagas->setMaxLength           ( 4         );

    $obLblVagasAtuais = new Label();
    $obLblVagasAtuais->setName("stVagasAtuais_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasAtuais->setId("stVagasAtuais_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasAtuais->setName("stVagasAtuais_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasAtuais->setValue("&nbsp;");

    $obLblVagasOcupadas = new Label();
    $obLblVagasOcupadas->setName("stVagasOcupadas_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasOcupadas->setId("stVagasOcupadas_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasOcupadas->setName("stVagasOcupadas_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasOcupadas->setValue("&nbsp;");

    $obLblVagasDisponiveis = new Label();
    $obLblVagasDisponiveis->setName("stVagasDisponiveis_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasDisponiveis->setId("stVagasDisponiveis_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasDisponiveis->setName("stVagasDisponiveis_[cod_regime]_[cod_sub_divisao]");
    $obLblVagasDisponiveis->setValue("&nbsp;");

    $rsRegimeSubdivisao = new recordset;
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php");
    $obTPessoalSubDivisao = new TPessoalSubDivisao();
    $obTPessoalSubDivisao->recuperaRelacionamento($rsRegimeSubdivisao);
    
    $arVagasLimpeza = array();
    $inIndex = 1;
    while (!$rsRegimeSubdivisao->eof()) {
        $arVagasLimpeza["stVagas_".$rsRegimeSubdivisao->getCampo("cod_regime")."_".$rsRegimeSubdivisao->getCampo("cod_sub_divisao")."_".$inIndex] =  0;
        $inIndex++;
        $rsRegimeSubdivisao->proximo();
    }
    
    Sessao::write("arVagasLimpeza",$arVagasLimpeza);
    
    $rsRegimeSubdivisao->setPrimeiroElemento();

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
    $obLista = new TableTree();
    $obLista->setRecordset($rsRegimeSubdivisao);
    $obLista->setParametros(array("cod_regime","cod_sub_divisao"));
    $obLista->setArquivo(CAM_GRH_PES_INSTANCIAS."cargo/DTManterCargo.php");
    if ($boCargo) {
        $obLista->setSummary("Quantidade de Vagas por Cargo");
    } else {
        $obLista->setSummary("Quantidade de Vagas por Especialidade");
    }

    $obLista->Head->addCabecalho("Regime"       ,10);
    $obLista->Head->addCabecalho("Subdivisão"   ,50);
    if (Sessao::read("stAcao") == "alterar") {
        $obLista->Head->addCabecalho("Vagas Atuais"   ,10);
        $obLista->Head->addCabecalho("Vagas Ocupadas"   ,10);
        $obLista->Head->addCabecalho("Vagas Disponíveis"   ,10);
    }
    $obLista->Head->addCabecalho("Vagas"        ,20);

    $obLista->Body->addCampo( '[cod_regime]-[nom_regime]'      , 'E' );
    $obLista->Body->addCampo( '[cod_sub_divisao]-[nom_sub_divisao]'  , 'E' );
    
    if (Sessao::read("stAcao") == "alterar") {
        $obLista->Body->addComponente( $obLblVagasAtuais );
        $obLista->Body->addComponente( $obLblVagasOcupadas );
        $obLista->Body->addComponente( $obLblVagasDisponiveis );
    }
    $obLista->Body->addComponente( $obTxtVagas );

    $obLista->montaHTML();
    $stHtml = $obLista->getHtml();

    $obSpnRegimeSubDivisao = new Span();
    $obSpnRegimeSubDivisao->setId("spnRegimeSubDivisao");
    $obSpnRegimeSubDivisao->setValue($stHtml);

    $obFormulario->addSpan($obSpnRegimeSubDivisao);
}

function addNorma(&$obFormulario,&$arComponentes,$boCargo=true)
{
    include_once( CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php" );
    if ($boCargo) {
        $obTipoNormaNorma = new IBuscaInnerNorma(false,false);
    } else {
        $obTipoNormaNorma = new IBuscaInnerNorma(false,true);
    }

    if (!$boCargo) {
        $obTipoNormaNorma->obBscNorma->setNullBarra(false);
        $obTipoNormaNorma->obBscNorma->obCampoCod->setNullBarra(false);
    }

    $obTipoNormaNorma->geraFormulario($obFormulario);
    $arComponentes[] = $obTipoNormaNorma->obBscNorma;
    $arComponentes[] = $obTipoNormaNorma->obBscNorma->obCampoCod;
}

function gerarCamposEspecialidade()
{
    $obTxtDescricaoEspecialidade = new TextBox;
    $obTxtDescricaoEspecialidade->setRotulo      ( "Descrição"                           );
    $obTxtDescricaoEspecialidade->setName        ( "stDescricaoEspecialidade"             );
    $obTxtDescricaoEspecialidade->setId          ( "stDescricaoEspecialidade"             );
    $obTxtDescricaoEspecialidade->setValue       ( $stDescricaoEspecialidade              );
    $obTxtDescricaoEspecialidade->setTitle       ( "Informe a descrição da especialidade." );
    $obTxtDescricaoEspecialidade->setSize        ( 80                                     );
    $obTxtDescricaoEspecialidade->setMaxLength   ( 80                                     );
    $obTxtDescricaoEspecialidade->setEspacosExtras ( false );
    $obTxtDescricaoEspecialidade->setNullBarra(false);

    $arComponentes = array();
    $arComponentes[] = $obTxtDescricaoEspecialidade;

    $obSpnEspecialidades = new Span();
    $obSpnEspecialidades->setId("spnEspecialidades");

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Dados da Especialização");
    $obFormulario->addComponente($obTxtDescricaoEspecialidade);
    addCBO($obFormulario,$arComponentes,false);
    addPadrao($obFormulario,$arComponentes,false);
    $obFormulario->addTitulo("Norma Regulamentadora da Especialidade");
    addNorma($obFormulario,$arComponentes,false);
    addRegimeSubDivisao($obFormulario,false);

    $stIds = "";
    foreach ($arComponentes as $arComponente) {
        $stIds .= $arComponente->getId().",";
    }
    $stIds = substr($stIds,0,strlen($stIds)-1);

    $obFormulario->incluirAlterar("Especialidade",$arComponentes,false,false,"");

    $obFormulario->addSpan($obSpnEspecialidades);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval =  $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stJs  = "jQuery('#spnCargo').html('".$obFormulario->getHTML()."');\n";
    $stJs .= "jQuery('#hdnCargo').val('".$stEval."');\n";
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= "jQuery('#btLimparEspecialidade').click( function () { montaParametrosGET('limparQuantidades'); } ); \n";

    return $stJs;
}

function gerarCamposCargo()
{
    $arComponentes = array();

    $obFormulario = new Formulario();
    addCBO($obFormulario,$arComponentes);
    addPadrao($obFormulario,$arComponentes);
    $obFormulario->addTitulo("Norma Regulamentadora do Cargo");
    addNorma($obFormulario,$arComponentes);
    addRegimeSubDivisao($obFormulario);

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval =  $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stJs  = "jQuery('#spnCargo').html('".$obFormulario->getHTML()."');\n";
    $stJs .= "jQuery('#hdnCargo').val('".$stEval."');\n";

    return $stJs;
}

function onLoad()
{
    if (Sessao::read("stAcao") == "alterar") {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidade.class.php"   );
        $obTPessoalEspecialidade        = new TPessoalEspecialidade;
        $stFiltro = " AND especialidade.cod_cargo = ".Sessao::read("inCodCargo");
        $obTPessoalEspecialidade->recuperaEspecialidadeDeCodigosCargo($rsEspecialidadeCargo,$stFiltro);

        if ($rsEspecialidadeCargo->getNumLinhas() > 0) {
            $stJs  = "jQuery('#boEspecialidadeSim').attr('checked','checked');\n";
            $stJs .= gerarCamposEspecialidade();

            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadePadrao.class.php"   );
            $obTPessoalEspecialidadePadrao = new TPessoalEspecialidadePadrao();

            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCboEspecialidade.class.php"   );
            $obTPessoalCboEspecialidade = new TPessoalCboEspecialidade();

            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadeSubDivisao.class.php"   );
            $obTPessoalEspecialidadeSubDivisao = new TPessoalEspecialidadeSubDivisao();
            
            include_once(CAM_GA_NORMAS_MAPEAMENTO."TNormasNorma.class.php");
            $obTNormasNorma = new TNormasNorma();

            $arEspecialidades = array();
            while (!$rsEspecialidadeCargo->eof()) {
                $stFiltro = " AND especialidade_sub_divisao.cod_especialidade = ".$rsEspecialidadeCargo->getCampo("cod_especialidade");
                $obTPessoalEspecialidadeSubDivisao->recuperaVagasEspecialidade($rsVagasEspecialidade,$stFiltro," ORDER BY regime.descricao,sub_divisao.descricao");
                
                $stFiltro = " WHERE cod_norma = ".$rsVagasEspecialidade->getCampo("cod_norma");
                $obTNormasNorma->recuperaTodos($rsNorma,$stFiltro);

                $stFiltro = " WHERE especialidade_padrao.cod_especialidade = ".$rsEspecialidadeCargo->getCampo("cod_especialidade");
                $obTPessoalEspecialidadePadrao->recuperaRelacionamento($rsEspecialidadePadrao,$stFiltro);

                $stFiltro = " WHERE cbo_especialidade.cod_especialidade = ".$rsEspecialidadeCargo->getCampo("cod_especialidade");
                $obTPessoalCboEspecialidade->recuperaRelacionamento($rsCboEspecialidade,$stFiltro);

                $inVagas = 0;
                $arVagas = array();
                $inIndex = 1;
                while (!$rsVagasEspecialidade->eof()) {
                    $stVaga = "stVagas_".$rsVagasEspecialidade->getCampo("cod_regime")."_".$rsVagasEspecialidade->getCampo("cod_sub_divisao")."_".$inIndex;
                    $arVagas[$stVaga] = $rsVagasEspecialidade->getCampo("nro_vaga_criada");
                    $inVagas += $rsVagasEspecialidade->getCampo("nro_vaga_criada");
                    $inIndex++;
                    $rsVagasEspecialidade->proximo();
                }

                $arTemp["inId"]                     = count($arEspecialidades);
                $arTemp["inCodEspecialidade"]       = $rsEspecialidadeCargo->getCampo("cod_especialidade");
                $arTemp["stDescricaoEspecialidade"] = $rsEspecialidadeCargo->getCampo("descricao");
                $arTemp["inNumCBO"]                 = $rsCboEspecialidade->getCampo("codigo");
                $arTemp["inCodCBO"]                 = $rsCboEspecialidade->getCampo("cod_cbo");
                $arTemp["stCBO"]                    = $rsCboEspecialidade->getCampo("descricao");
                $arTemp["inCodPadrao"]              = $rsEspecialidadePadrao->getCampo("cod_padrao");
                $arTemp["stPadrao"]                 = getDescricaoPadrao($rsEspecialidadePadrao->getCampo("cod_padrao"));
                $arTemp["stNorma"]                  = $rsNorma->getCampo("nom_norma");
                $arTemp["stCodNorma"]               = $rsNorma->getCampo("num_norma")."/".$rsNorma->getCampo("exercicio");
                $arTemp["inVagas"]                  = $inVagas;
                $arTemp["arVagas"]                  = $arVagas;
                $arEspecialidades[] = $arTemp;
                $rsEspecialidadeCargo->proximo();
            }
            Sessao::write("arEspecialidades",$arEspecialidades);
            $stJs .= "jQuery('#boEspecialidadeNao').attr('disabled','disabled');\n";
            Sessao::write("boEspecialidade",true);
            $stJs .= montaListaEspecialidades();
        } else {
            Sessao::write("boEspecialidade",false);
            $stJs  = "jQuery('#boEspecialidadeNao').attr('checked','checked');\n";
            $stJs .= "jQuery('#boEspecialidadeSim').attr('disabled','disabled');\n";
            $stJs .= gerarCamposCargo();

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCboCargo.class.php");
            $obTPessoalCboCargo =  new TPessoalCboCargo();
            $stFiltro = " WHERE cbo_cargo.cod_cargo = ".Sessao::read("inCodCargo");
            $obTPessoalCboCargo->recuperaRelacionamento($rsCboCargo,$stFiltro);
            $stJs .= "jQuery('#inNumCBO').val('".$rsCboCargo->getCampo("codigo")."');\n";
            $stJs .= "jQuery('#inCodCBO').val('".$rsCboCargo->getCampo("cod_cbo")."');\n";
            $stJs .= "jQuery('#inNomCBO').html('".$rsCboCargo->getCampo("descricao")."');\n";

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCargoPadrao.class.php");
            $obTPessoalCargoPadrao = new TPessoalCargoPadrao();
            $stFiltro = " AND PC.cod_cargo = ".Sessao::read("inCodCargo");
            $obTPessoalCargoPadrao->recuperaRelacionamento($rsCargoPadrao,$stFiltro);
            $stJs .= "jQuery('#inCodPadrao').val('".$rsCargoPadrao->getCampo("cod_padrao")."');\n";
            $stJs .= "jQuery('#inCodPadraoTxt').val('".$rsCargoPadrao->getCampo("cod_padrao")."');\n";

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCargoSubDivisao.class.php");
            $obTPessoalCargoSubDivisao = new TPessoalCargoSubDivisao();
            $stFiltro = " AND cargo_sub_divisao.cod_cargo = ".Sessao::read("inCodCargo");
            $obTPessoalCargoSubDivisao->recuperaVagasServidor($rsCargoSubDivisao,$stFiltro);

            include_once(CAM_GA_NORMAS_MAPEAMENTO."TNormasNorma.class.php");
            $obTNormasNorma = new TNormasNorma();
            $stFiltro = " WHERE cod_norma = ".$rsCargoSubDivisao->getCampo("cod_norma");
            $obTNormasNorma->recuperaTodos($rsNorma,$stFiltro);
            $stJs .= "jQuery('#stNorma').html('".$rsNorma->getCampo("nom_norma")."');\n";
            $stJs .= "jQuery('#stCodNorma').val('".$rsNorma->getCampo("num_norma")."/".$rsNorma->getCampo("exercicio")."');\n";

            while (!$rsCargoSubDivisao->eof()) {
                $stVagas = "stVagas_".$rsCargoSubDivisao->getCampo("cod_regime")."_".$rsCargoSubDivisao->getCampo("cod_sub_divisao");
                $stJs .= "jQuery('input[name^=\'".$stVagas."\']').val('".$rsCargoSubDivisao->getCampo("nro_vaga_criada")."');\n";

                $stVagas = str_replace("stVagas","stVagasAtuais",$stVagas);
                $stJs .= "jQuery('span[id^=\'".$stVagas."\']').html('".$rsCargoSubDivisao->getCampo("nro_vaga_criada")."');\n";

                if ($rsCargoSubDivisao->getCampo("cod_cargo") != "" and $rsCargoSubDivisao->getCampo("cod_cargo") != "" and $rsCargoSubDivisao->getCampo("cod_sub_divisao") != "") {
                    $obTPessoalCargoSubDivisao->setDado("cod_cargo",$rsCargoSubDivisao->getCampo("cod_cargo"));
                    $obTPessoalCargoSubDivisao->setDado("cod_regime",$rsCargoSubDivisao->getCampo("cod_regime"));
                    $obTPessoalCargoSubDivisao->setDado("cod_sub_divisao",$rsCargoSubDivisao->getCampo("cod_sub_divisao"));
                    $obTPessoalCargoSubDivisao->getVagasOcupadasCargo($rsOcupadas);
                    $inOcupadas = $rsOcupadas->getCampo("vagas");
                } else {
                    $inOcupadas = 0;
                }
                $stVagas = str_replace("stVagasAtuais","stVagasOcupadas",$stVagas);
                $stJs .= "jQuery('span[id^=\'".$stVagas."\']').html('".$inOcupadas."');\n";

                $stVagas = str_replace("stVagasOcupadas","stVagasDisponiveis",$stVagas);
                $stJs .= "jQuery('span[id^=\'".$stVagas."\']').html('".($rsCargoSubDivisao->getCampo("nro_vaga_criada")-$inOcupadas)."');\n";

                $rsCargoSubDivisao->proximo();
            }

        }
    } else {
        $stJs = gerarCamposCargo();
    }

    return $stJs;
}

function getDescricaoCBO($inCodCbo)
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCbo.class.php");
    $obTPessoalCbo = new TPessoalCbo();
    $stFiltro = " WHERE cod_cbo = ".trim($inCodCbo);
    $obTPessoalCbo->recuperaTodos($rsCBO,$stFiltro);

    return $rsCBO->getCampo("descricao");
}

function getDescricaoPadrao($inCodPadrao)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php");
    $obTFolhaPagamentoPadrao = new TFolhaPagamentoPadrao();
    $stFiltro = " AND  FP.cod_padrao = ".trim($inCodPadrao);
    $obTFolhaPagamentoPadrao->recuperaRelacionamento($rsFolhaPagamentoPadrao,$stFiltro,"ORDER BY descricao");
    $rsFolhaPagamentoPadrao->addFormatacao("valor", "NUMERIC_BR");

    return $rsFolhaPagamentoPadrao->getCampo("descricao")."-".$rsFolhaPagamentoPadrao->getCampo("valor");
}

function getTotalVagas()
{
    $inVagas = 0;
    foreach ($_GET as $stCampo=>$inValor) {
        if (strpos($stCampo,"stVagas") === 0) {
            if ($inValor != "") {
                $inVagas += $inValor;
            }
        }
    }

    return $inVagas;
}

function getVagas()
{
    $arVagas = array();

    foreach ($_GET as $stCampo=>$inValor) {
        if (strpos($stCampo,"stVagas") === 0) {
            if ($inValor != "") {
                $arVagas[$stCampo] = $inValor;
            }
        }
    }

    return $arVagas;
}

function incluirEspecialidade(Request $request)
{
    $arEspecialidades = array();
    $arEspecialidades = Sessao::read("arEspecialidades");

    $arTemp["inId"]                     = count($arEspecialidades);
    $arTemp["stDescricaoEspecialidade"] = $request->get("stDescricaoEspecialidade");
    $arTemp["inNumCBO"]                 = $request->get("inNumCBO");
    $arTemp["inCodCBO"]                 = $request->get("inCodCBO");
    $arTemp["stCBO"]                    = getDescricaoCBO($request->get("inCodCBO"));
    $arTemp["inCodPadrao"]              = $request->get("inCodPadrao");
    $arTemp["stPadrao"]                 = getDescricaoPadrao($request->get("inCodPadrao"));
    $arTemp["stNorma"]                  = $request->get("stNorma");
    $arTemp["stCodNorma"]               = $request->get("stCodNorma");
    $arTemp["inVagas"]                  = getTotalVagas();
    $arTemp["arVagas"]                  = getVagas();
    $arEspecialidades[] = $arTemp;

    Sessao::write('arEspecialidades', $arEspecialidades);

//	$msg = "O número de novas vagas da sub-divisão ".$rsVagas->getCampo('nom_sub_divisao')." não pode estar vazio.";
//  $stJs .= "alertaAviso('".$msg."', 'unica','erro','".Sessao::getId()."','');";
    $stJs .= montaListaEspecialidades();
    $stJs .= limparEspecialidades();
    $stJs .= limparQuantidades($arTemp["arVagas"]);

    return $stJs;
}

function alterarEspecialidade(Request $request)
{
    $arEspecialidades = Sessao::read("arEspecialidades");

    $arTemp["inId"]                     = Sessao::read("inId");
    $arTemp["inCodEspecialidade"]       = Sessao::read("inCodEspecialidade");
    $arTemp["stDescricaoEspecialidade"] = $request->get("stDescricaoEspecialidade");
    $arTemp["inNumCBO"]                 = $request->get("inNumCBO");
    $arTemp["inCodCBO"]                 = $request->get("inCodCBO");
    $arTemp["stCBO"]                    = getDescricaoCBO($request->get("inCodCBO"));
    $arTemp["inCodPadrao"]              = $request->get("inCodPadrao");
    $arTemp["stPadrao"]                 = getDescricaoPadrao($request->get("inCodPadrao"));
    $arTemp["stNorma"]                  = $request->get("stNorma");
    $arTemp["stCodNorma"]               = $request->get("stCodNorma");
    $arTemp["inVagas"]                  = getTotalVagas();
    $arTemp["arVagas"]                  = getVagas();
    // A alteração da sessão arEspecialidades está no validaEspecialidade.

//	Sessao::remove("inCodEspecialidade");
    $stJs  = validaEspecialidade($arTemp,$stJs);
    $stJs .= montaListaEspecialidades();
    $stJs .= limparEspecialidades();
    $stJs .= limparQuantidades($arTemp["arVagas"]);

    return $stJs;
}

function excluirEspecialidade()
{
    $arEspecialidades = Sessao::read("arEspecialidades");
    $arEspecialidadesExcluir = Sessao::read("arEspecialidadeExcluir");
    $arTemp = array();

    foreach ($arEspecialidades as $arEspecialidade) {
        if ($arEspecialidade["inId"] != $_GET["inId"]) {
            $arTemp[] = $arEspecialidade;
        } else {
            if ($arEspecialidade["inCodEspecialidade"]!= "") {
                $arEspecialidadesExcluir[] = $arEspecialidade["inCodEspecialidade"];
            }
        }
    }
    Sessao::write("arEspecialidades",$arTemp);
    Sessao::write("arEspecialidadeExcluir",$arEspecialidadesExcluir);
    $stJs = montaListaEspecialidades();

    return $stJs;
}

function montaListaEspecialidades()
{
    $arEspecialidades = Sessao::read("arEspecialidades");

    $rsEspecialidades = new recordset();
    $rsEspecialidades->preenche($arEspecialidades);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
    $obLista = new Table();
    $obLista->setRecordset($rsEspecialidades);
    $obLista->setSummary("Especializações Cadastradas");

    $obLista->Head->addCabecalho("Descrição"    ,30);
    $obLista->Head->addCabecalho("Padrão"       ,30);
    $obLista->Head->addCabecalho("CBO"          ,30);
    $obLista->Head->addCabecalho("Vagas"        ,10);

    $obLista->Body->addCampo( 'stDescricaoEspecialidade'        , 'E' );
    $obLista->Body->addCampo( 'stPadrao'                        , 'E' );
    $obLista->Body->addCampo( 'stCBO'                           , 'E' );
    $obLista->Body->addCampo( 'inVagas'                         , 'C' );

    if (Sessao::read("stAcao") == "alterar") {
        $obLista->Body->addAcao("alterar","executaFuncaoAjax(\'%s\',\'&inId=%s\'); return false;",array('montaAlterarEspecialidade','inId'));
    }
    $obLista->Body->addAcao("excluir","executaFuncaoAjax(\'%s\',\'&inId=%s\')",array('excluirEspecialidade','inId'));

    $obLista->montaHTML();
    $stHtml = $obLista->getHtml();
    $stHtml = str_replace("\n","",$stHtml);

    $stJs  = "jQuery('#spnEspecialidades').html('".$stHtml."');\n";

    return $stJs;
}

function limparEspecialidades()
{
    $stJs .= "jQuery('#stDescricaoEspecialidade').val(''); \n";
    $stJs .= "jQuery('#inNumCBO').val(''); \n";
    $stJs .= "jQuery('#inNomCBO').html('&nbsp;'); \n";
    $stJs .= "jQuery('#inCodPadraoTxt').val(''); \n";
    $stJs .= "jQuery('#inCodPadrao').val(''); \n";
    $stJs .= "jQuery('#stCodNorma').val(''); \n";
    $stJs .= "jQuery('#stNorma').html('&nbsp;'); \n";

    return $stJs;
}

function limparQuantidades($arVagas)
{
    foreach ($arVagas as $stCampo=>$inValor) {
        $stCampo = preg_replace('/([A-z]+_[\d]+_[\d]+)/', '$1', $stCampo);
        $stJs .= "jQuery('input[name^=\'".substr($stCampo,0,strlen($stCampo)-3)."\']').val('');\n";
        $stCampo = str_replace("stVagas","stVagasOcupadas",$stCampo);
        $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-3)."\']').html('');\n";
        $stCampo = str_replace("stVagasOcupadas","stVagasAtuais",$stCampo);
        $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-3)."\']').html('');\n";
        $stCampo = str_replace("stVagasAtuais","stVagasDisponiveis",$stCampo);
        $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-3)."\']').html('');\n";

        $stJs .= "jQuery('#btIncluirEspecialidade').removeProp('disabled');\n";
        $stJs .= "jQuery('#btAlterarEspecialidade').prop('disabled',true);\n";

//        $stJs .= "jQuery('#".$stCampo."').val('');\n";
//        $stCampo = str_replace("stVagas","stVagasOcupadas",$stCampo);
//        $stJs .= "jQuery('#".$stCampo."').html('&nbsp;');\n";
//        $stCampo = str_replace("stVagasOcupadas","stVagasAtuais",$stCampo);
//        $stJs .= "jQuery('#".$stCampo."').html('&nbsp;');\n";
//        $stCampo = str_replace("stVagasAtuais","stVagasDisponiveis",$stCampo);
//        $stJs .= "jQuery('#".$stCampo."').html('&nbsp;');\n";

    }

    return $stJs;
}

function montaAlterarEspecialidade()
{
    $arEspecialidades = Sessao::read("arEspecialidades");
    Sessao::write("inId",$_GET["inId"]);

    $arEspecialidade = $arEspecialidades[$_GET["inId"]];
    Sessao::write("inCodEspecialidade",$arEspecialidade["inCodEspecialidade"]);
    $stJs  = "jQuery('#stDescricaoEspecialidade').val('".$arEspecialidade["stDescricaoEspecialidade"]."');\n";
    $stJs .= "jQuery('#inNumCBO').val('".$arEspecialidade["inNumCBO"]."');\n";
    $stJs .= "jQuery('#inCodCBO').val('".$arEspecialidade["inCodCBO"]."');\n";
    $stJs .= "jQuery('#inNomCBO').html('".$arEspecialidade["stCBO"]."');\n";
    $stJs .= "jQuery('#inCodPadrao').val('".$arEspecialidade["inCodPadrao"]."');\n";
    $stJs .= "jQuery('#inCodPadraoTxt').val('".$arEspecialidade["inCodPadrao"]."');\n";
    $stJs .= "jQuery('#stNorma').html('".$arEspecialidade["stNorma"]."');\n";
    $stJs .= "jQuery('#stCodNorma').val('".$arEspecialidade["stCodNorma"]."');\n";
    $stJs .= limparQuantidades(Sessao::read("arVagasLimpeza"));
    $stJs .= "jQuery('#btIncluirEspecialidade').prop('disabled',true);\n";
    $stJs .= "jQuery('#btAlterarEspecialidade').removeProp('disabled');\n";

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadeSubDivisao.class.php");
    $obTPessoalEspecialidadeSubDivisao = new TPessoalEspecialidadeSubDivisao();

    if (is_array($arEspecialidade["arVagas"])) {
        foreach ($arEspecialidade["arVagas"] as $stVagas=>$inVagas) {
            $stJs .= "jQuery('input[name^=\'".substr($stVagas,0,strlen($stVagas)-2)."\']').val('".$inVagas."');\n";

            $stCampo = str_replace("stVagas","stVagasAtuais",$stVagas);
            if (!is_numeric(substr($stCampo,strlen($stCampo)-2))) {
                $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-2)."\']').html('".$inVagas."');\n";
            } else {
                $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-3)."\']').html('".$inVagas."');\n";
            }

            $arCampo = explode("_",$stVagas);
            $rsOcupadas = new recordset();
            if ($arCampo[1] != "" and $arCampo[2] != "" and $arEspecialidade["inCodEspecialidade"] != "") {
                $obTPessoalEspecialidadeSubDivisao->setDado("cod_especialidade",$arEspecialidade["inCodEspecialidade"]);
                $obTPessoalEspecialidadeSubDivisao->setDado("cod_regime",$arCampo[1]);
                $obTPessoalEspecialidadeSubDivisao->setDado("cod_sub_divisao",$arCampo[2]);
                $obTPessoalEspecialidadeSubDivisao->getVagasOcupadasEspecialidade($rsOcupadas);
                $inOcupadas = $rsOcupadas->getCampo("vagas");
            } else {
                $inOcupadas = 0;
            }

            $stCampo = str_replace("stVagas","stVagasOcupadas",$stVagas);
            //os dois ultimos caracteres nao podem ser numericos caso contrario o nome do campo a buscar fica incorreto
            if (!is_numeric(substr($stCampo,strlen($stCampo)-2))) {
                $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-2)."\']').html('".$inOcupadas."');\n";

                $stCampo = str_replace("stVagas","stVagasDisponiveis",$stVagas);
                $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-2)."\']').html('".($inVagas-$inOcupadas)."');\n";
            } else {
                $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-3)."\']').html('".$inOcupadas."');\n";

                $stCampo = str_replace("stVagas","stVagasDisponiveis",$stVagas);
                $stJs .= "jQuery('span[id^=\'".substr($stCampo,0,strlen($stCampo)-3)."\']').html('".($inVagas-$inOcupadas)."');\n";
            }
        }
    }

    return $stJs;
}

function validaEspecialidade($arEspecialidade,$stJs)
{
    $inVagas = 0;
    $inCodRegime = 0;
    $inCodSubDivisao = 0;
    $boErro = false;

    foreach ($arEspecialidade["arVagas"] as $campo => $inVagas) {
        $arCodigos = preg_replace('/^([A-Za-z]+_)/', '', $campo);
        $inCodRegime = preg_replace('/(^[\d]+)_(.*)/', '$1', $arCodigos);
        $inCodSubDivisao = preg_replace('/([\d]+_)([\d]+)(.*)/', '$2', $arCodigos);

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadeSubDivisao.class.php");
        $obTPessoalEspecialidadeSubDivisao = new TPessoalEspecialidadeSubDivisao();
        $obTPessoalEspecialidadeSubDivisao->setDado("cod_especialidade",$arEspecialidade["inCodEspecialidade"]);
        $obTPessoalEspecialidadeSubDivisao->setDado("cod_regime",$inCodRegime);
        $obTPessoalEspecialidadeSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
        $obTPessoalEspecialidadeSubDivisao->getVagasOcupadasEspecialidade($rsOcupadas);
        $inVagasOcupadas = $rsOcupadas->getCampo("vagas") ;

        if ( $inVagas < $inVagasOcupadas && trim($inVagas) != "" ) {
            $stJs = "alertaAviso(\"O número de novas vagas da sub-divisão tem que ser maior ou igual ao número de vagas ocupadas(".$inVagasOcupadas.").\", \"n_incluir\", \"erro\", \"".Sessao::getId()."\"); \n";
            $boErro = true;
            break;
        }

    }
    $arEspecialidades = Sessao::read("arEspecialidades");

    //caso ocorra erro não altera o array de especialidade
    if ($boErro == false) {
        $arEspecialidades[Sessao::read("inId")] = $arEspecialidade;
        Sessao::write("arEspecialidades",$arEspecialidades);
    }

    return $stJs;
}

function buscaCBO()
{
    $inNumCBO = "";
    $inCodCBO = "";
    $inNomCBO = "&nbsp;";
    if (trim($_GET["inNumCBO"]) != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCbo.class.php");
        $obTPessoalCBO = new TPessoalCbo();
        $stFiltro = " WHERE codigo = ".trim($_GET["inNumCBO"]);
        $obTPessoalCBO->recuperaTodos($rsCBO,$stFiltro);
        if ($rsCBO->getNumLinhas() == 1) {
            $inNumCBO = $rsCBO->getCampo("codigo");
            $inCodCBO = $rsCBO->getCampo("cod_cbo");
            $inNomCBO = $rsCBO->getCampo("descricao");
        }
    }
    $stJs  = "jQuery('#inNumCBO').val('".$inNumCBO."');\n";
    $stJs .= "jQuery('#inCodCBO').val('".$inCodCBO."');\n";
    $stJs .= "jQuery('#inNomCBO').html('".$inNomCBO."');\n";

    return $stJs;
}

function submeter()
{
    $stJs .= "BloqueiaFrames(true,false);";
    $stJs .= "jQuery('#showLoading',parent.frames[2].document).css('width','500px');";
    $stJs .= "jQuery('#showLoading',parent.frames[2].document).css('margin','-25px 0px 0px -250px;');";
    $stJs .= "parent.frames[2].Salvar();\n";

    return $stJs;
}

switch ( $request->get("stCtrl") ) {
    case "onLoad":
        $stJs = onLoad();
        break;
    case "gerarSpanCargoEspecialidade":
        $stJs = gerarSpanCargoEspecialidade();
        break;
    case "incluirEspecialidade":
        $stJs = incluirEspecialidade($request);
        break;
    case "alterarEspecialidade":
        $stJs = alterarEspecialidade($request);
        break;
    case "excluirEspecialidade":
        $stJs = excluirEspecialidade();
        break;
    case "montaAlterarEspecialidade":
        $stJs = montaAlterarEspecialidade();
        break;
    case "limparQuantidades":
        $stJs = limparQuantidades(Sessao::read("arVagasLimpeza"));
        break;
    case "buscaCBO":
        $stJs =  buscaCBO();
        break;
    case "submeter":
        $stJs =  submeter();
        break;
}

if ($stJs) {
   echo $stJs;
}
?>
