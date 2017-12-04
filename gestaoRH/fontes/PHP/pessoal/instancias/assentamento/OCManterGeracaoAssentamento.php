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
    * Página Oculta do Gerar Assentamento
    * Data de Criação   : 19/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore
    $Id: OCManterGeracaoAssentamento.php 66364 2016-08-17 21:11:39Z michel $

    * Caso de uso: uc-04.04.14

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php";
require_once CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php";
require_once CAM_GRH_PES_COMPONENTES."IBuscaInnerLotacao.class.php";
require_once CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php";
require_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";
require_once CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php";
require_once CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php";
require_once CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php";
require_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";
require_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
require_once CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php";
require_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php";
require_once CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php";
require_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php";
require_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";

function gerarAssentamento($boExecuta=false,$stArquivo="Form")
{
    global $request;

    switch ($request->get('stModoGeracao')) {
        case 'contrato':
            $stJs = gerarSpan1($boExecuta,$stArquivo);
        break;
        case 'cgm/contrato':
            $stJs = gerarSpan2($boExecuta,$stArquivo);
        break;
        case 'cargo':
            $stJs = gerarSpan3($boExecuta,$stArquivo);
        break;
        case 'lotacao':
            $stJs = gerarSpan4($boExecuta,$stArquivo);
        break;
    }
    $stJs .= "f.hdnModoGeracao.value = '".$request->get('stModoGeracao')."';";
    $stJs .= limparArquivosAssentamentoAtual();

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan1($boExecuta=false,$stArquivo="Form")
{
    $obFormulario = new Formulario;

    $obIContrato = new IFiltroContrato(true);
    if ($stArquivo == "Form") {
        $obIContrato->setTituloFormulario ( "Gerar Assentamento por Matrícula" );
    } else {
        $obIContrato->setTituloFormulario ( "Filtro por Matrícula" );
    }
    $stOnBlur = $obIContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnBlur();
    $obIContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur($stOnBlur." preencheClassificacao(this.value, 'matricula');buscaValor('buscaContrato');");
    $obIContrato->geraFormulario( $obFormulario );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan2($boExecuta=false,$stArquivo="Form")
{
    $obFormulario = new Formulario;

    $obICGMContrato = new IFiltroCGMContrato();

    if ($stArquivo == "Form") {
        $obICGMContrato->setTituloFormulario( "Gerar Assentamento por CGM/Matrícula" );
    } else {
        $obICGMContrato->setTituloFormulario( "Filtro por CGM/Matrícula" );
    }

    $stOnBlur = $obICGMContrato->obCmbContrato->obEvento->getOnBlur();
    $obICGMContrato->obCmbContrato->obEvento->setOnBlur($stOnBlur." preencheClassificacao(this.value, 'cgm');");

    $obICGMContrato->obBscCGM->setNull(true);

    $obICGMContrato->geraFormulario( $obFormulario );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan3($boExecuta=false,$stArquivo="Form")
{
    $obRPessoalCargo = new RPessoalCargo;
    $obRPessoalCargo->listarCargo( $rsCargos );

    $obFormulario = new Formulario;

    //Define objetos RADIO para armazenar o TIPO do assentamento por cargo
    $obChkCargoExercido = new Checkbox;
    $obChkCargoExercido->setRotulo                          ( "Cargo Exercido"                                          );
    $obChkCargoExercido->setName                            ( "boCargoExercido"                                         );
    if ($stArquivo == "Form") {
        $obChkCargoExercido->setTitle                       ( "Informe se o assentamento será gerado por cargo exercido." );
    } else {
        $obChkCargoExercido->setTitle                       ( "Informe se o filtro será por cargo exercido."            );
    }
    $obChkCargoExercido->setValue                           ( true                                                      );

    $obChkFuncaoExercida = new Checkbox;
    $obChkFuncaoExercida->setRotulo                         ( "Função Exercida"                                         );
    $obChkFuncaoExercida->setName                           ( "boFuncaoExercida"                                        );
    if ($stArquivo == "Form") {
        $obChkFuncaoExercida->setTitle                      ( "Informe se o assentamento será gerado por função exercida." );
    } else {
        $obChkFuncaoExercida->setTitle                      ( "Informe se o filtro será por função exercida."           );
    }
    $obChkFuncaoExercida->setValue                          ( true                                                      );

    //Define objeto TEXTBOX para armazenar o Código do cargo
    $obTxtCodCargo = new TextBox;
    if ($stArquivo == "Form") {
        $obTxtCodCargo->setRotulo                           ( "*Cargo"                                                  );
        $obTxtCodCargo->setTitle                            ( "Informe o cargo dos servidores para os quais serão gerados assentamentos." );
    } else {
        $obTxtCodCargo->setRotulo                           ( "Cargo"                                                  );
        $obTxtCodCargo->setTitle                            ( "Informe o cargo."                                        );
    }
    $obTxtCodCargo->setName                                 ( "inCodCargoTxt"                                           );
    $obTxtCodCargo->setId                                   ( "inCodCargoTxt"                                           );
    $obTxtCodCargo->setSize                                 ( 10                                                        );
    $obTxtCodCargo->setMaxLength                            ( 10                                                        );
    $obTxtCodCargo->setInteiro                              ( true                                                      );
    $obTxtCodCargo->obEvento->setOnChange                   ( "buscaValor('preencherEspecialidade'); preencheClassificacao(this.value, 'cargo');" );

    //Define objeto SELECT para listar a DESCRIÇÃOO do cargo
    $obCmbCargo = new Select;
    if ($stArquivo == "Form") {
        $obCmbCargo->setRotulo                              ( "*Cargo"                                                  );
        $obCmbCargo->setTitle                               ( "Informe o cargo dos servidores para os quais serão gerados assentamentos." );
    } else {
        $obCmbCargo->setRotulo                              ( "Cargo"                                                   );
        $obCmbCargo->setTitle                               ( "Informe o cargo."                                        );
    }
    $obCmbCargo->setName                                    ( "inCodCargo"                                              );
    $obCmbCargo->setStyle                                   ( "width: 200px"                                            );
    $obCmbCargo->addOption                                  ( "", "Selecione"                                           );
    $obCmbCargo->setCampoID                                 ( "cod_cargo"                                               );
    $obCmbCargo->setCampoDesc                               ( "descricao"                                               );
    $obCmbCargo->preencheCombo                              ( $rsCargos                                                 );
    $obCmbCargo->obEvento->setOnChange                      ( "buscaValor('preencherEspecialidade'); preencheClassificacao(this.value, 'cargo');" );

    //Define objeto TEXTBOX para armazenar o CÓDIGO da especialidade
    $obTxtCodEspecialidade = new TextBox;
    $obTxtCodEspecialidade->setRotulo                       ( "Especialidade"                                           );
    if ($stArquivo == "Form") {
        $obTxtCodEspecialidade->setTitle                    ( "Informe a especialidade dos servidores para os quais serão gerados assentamentos." );
    } else {
        $obTxtCodEspecialidade->setTitle                    ( "Informe a especialidade."                                );
    }
    $obTxtCodEspecialidade->setName                         ( "inCodExpecialidadeTxt"                                   );
    $obTxtCodEspecialidade->setId                           ( "inCodExpecialidadeTxt"                                   );
    $obTxtCodEspecialidade->setSize                         ( 10                                                        );
    $obTxtCodEspecialidade->setMaxLength                    ( 10                                                        );
    $obTxtCodEspecialidade->setInteiro                      ( true                                                      );

    //Define objeto SELECT para listar a DESCRIÇÃO da especialidade
    $obCmbEspecialidade = new Select;
    $obCmbEspecialidade->setRotulo                          ( "Especialidade"                                           );
    if ($stArquivo == "Form") {
        $obCmbEspecialidade->setTitle                       ( "Informe a especialidade dos servidores para os quais serão gerados assentamentos." );
    } else {
        $obCmbEspecialidade->setTitle                       ( "Informe a especialidade."                                );
    }
    $obCmbEspecialidade->setName                            ( "inCodEspecialidade"                                      );
    $obCmbEspecialidade->setStyle                           ( "width: 200px"                                            );
    $obCmbEspecialidade->addOption                          ( "", "Selecione"                                           );

    if ($stArquivo == "Form") {
        $obFormulario->addTitulo                            ( "Gerar Assentamento por Cargo"                            );
    } else {
        $obFormulario->addTitulo                            ( "Filtro por Cargo"                                        );
    }
    $obFormulario->addComponente                            ( $obChkCargoExercido                                       );
    $obFormulario->addComponente                            ( $obChkFuncaoExercida                                      );
    $obFormulario->addComponenteComposto                    ( $obTxtCodCargo, $obCmbCargo                               );
    $obFormulario->addComponenteComposto                    ( $obTxtCodEspecialidade, $obCmbEspecialidade               );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan4($boExecuta=false,$stArquivo="Form")
{
    $obFormulario = new Formulario;

    $obIBuscaInnerLotacao = new IBuscaInnerLotacao;
    $obIBuscaInnerLotacao->obBscLotacao->setTitle("Informe a lotação dos servidores para os quais serão gerados assentamentos.");
    $obIBuscaInnerLotacao->obBscLotacao->setRotulo("*Lotação");

    if ($stArquivo == "Form") {
        $obFormulario->addTitulo          ( "Gerar Assentamento por Lotação"          );
    } else {
        $obFormulario->addTitulo          ( "Filtrar por Lotação"                     );
    }

    $stOnBlur = $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->getOnBlur();
    $stOnBlur = str_replace('ajaxJavaScript', 'ajaxJavaScriptSincrono', $stOnBlur);
    $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->setOnBlur($stOnBlur." preencheClassificacao(document.frm.HdninCodLotacao.value, 'lotacao'); ");
    $stOnChange = $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->getOnChange();
    $stOnChange = str_replace('ajaxJavaScript', 'ajaxJavaScriptSincrono', $stOnChange);
    $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->setOnChange($stOnChange." preencheClassificacao(document.frm.HdninCodLotacao.value, 'lotacao'); ");

    $obIBuscaInnerLotacao->geraFormulario($obFormulario);

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function calcularDataFinal($boExecuta=false)
{
    global $request;

    $inQuantidadeDias = $request->get('inQuantidadeDias');
    $stDataInicial = $request->get('stDataInicial');

    if ( !empty($stDataInicial) and $inQuantidadeDias != 0) {
        $arDataInicial  = explode("/", $stDataInicial );
        $stDataFinal = date("d/m/Y", mktime(0, 0, 0, $arDataInicial[1], ($arDataInicial[0]+$inQuantidadeDias-1), $arDataInicial[2]));

        $stJs .= "f.stDataFinal.value = '".$stDataFinal."';";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function ajustarQuantidadeDias($boExecuta=false,$boSpan=false)
{
    global $request;

    $stDataInicial = $request->get('stDataInicial');
    $stDataInicial = ( !empty($stDataInicial) ) ? $stDataInicial : Sessao::read('stDataInicial');
    $stDataFinal = $request->get('stDataFinal');
    $stDataFinal = ( !empty($stDataFinal) ) ? $stDataFinal : Sessao::read('stDataFinal');

    $arDataInicial = explode("/",$stDataInicial);
    $arDataFinal   = explode("/",$stDataFinal);
    $stDataInicial = $arDataInicial[2]."/".$arDataInicial[1]."/".$arDataInicial[0];
    $stDataFinal   = $arDataFinal[2]  ."/".$arDataFinal[1]  ."/".$arDataFinal[0];

    // Armazena nas variáveis $DataInicial e $DataFinal
    // os valores de $DataI e $DataF no formato 'timestamp'
    $DataInicial = getdate(strtotime($stDataInicial));
    $DataFinal   = getdate(strtotime($stDataFinal));

    // Calcula a Diferença
    $Dif = round (($DataFinal[0] - $DataInicial[0]) / 86400) + 1;
    if ($boSpan) {
        $stJs .= 'd.getElementById("inQuantidadeDias").innerHTML = "'.$Dif.'";';
    } else {
        $stJs .= "f.inQuantidadeDias.value = $Dif;\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherEspecialidade($boExecuta=false)
{
    global $request;

    $obRPessoalCargo = new RPessoalCargo;
    $obRPessoalCargo->setCodCargo( $request->get('inCodCargo') );
    $obRPessoalCargo->addEspecialidade();
    $obRPessoalCargo->roUltimoEspecialidade->listarEspecialidadesPorCargo( $rsEspecialidades );

    $stJs .= "limpaSelect(f.inCodEspecialidade,0);                                          \n";
    $stJs .= "f.inCodEspecialidade.options[0] = new Option('Selecione','', 'selected');     \n";
    $stJs .= "f.inCodExpecialidadeTxt.value='';                                             \n";
    $i = 1;
    while (!$rsEspecialidades->eof()) {
        $stJs .= "f.inCodEspecialidade.options[".$i++."] = new Option('".$rsEspecialidades->getCampo("descricao")."','".$rsEspecialidades->getCampo("cod_especialidade")."', '');\n";
        $rsEspecialidades->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherAssentamento($boExecuta=false, Request $request)
{
    $inCodClassificacao = $request->get('inCodClassificacao');
    $inCodClassificacao = ( !empty($inCodClassificacao) ) ? $inCodClassificacao : Sessao::read('inCodClassificacao');
    $stModoGeracao = $request->get('stModoGeracao');
    $stModoGeracao = ( !empty($stModoGeracao) ) ? $stModoGeracao : $request->get('hdnModoGeracao');
    $stModoGeracao = ( !empty($stModoGeracao) ) ? $stModoGeracao : sessao::read('stModoGeracao');
    $inCodAssentamento = $request->get('inCodAssentamento');
    $inCodAssentamento = ( !empty($inCodAssentamento) ) ? $inCodAssentamento : Sessao::read('inCodAssentamento');

    $rsAssentamentos = new RecordSet;
    $obRPessoalAssentamento = new RPessoalAssentamento( new RPessoalVantagem );
    $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $inCodClassificacao );

    switch ($stModoGeracao) {
        case 'contrato':
        case 'cgm/contrato':
            $obRPessoalAssentamento->listarAssentamentoPorContrato( $rsAssentamentos, $request->get('inContrato'), '', 'contrato' );
        break;
        case 'cargo':
            if ($request->get('boCargoExercido'))
                $obRPessoalAssentamento->listarAssentamentoPorContrato( $rsAssentamentos, $request->get('inCodCargo'), '', 'cargo_exercido' );
            else
                $obRPessoalAssentamento->listarAssentamentoPorContrato( $rsAssentamentos, $request->get('inCodCargo'), '', 'cargo' );
        break;
        case 'lotacao':
            $obRPessoalAssentamento->listarAssentamentoPorContrato( $rsAssentamentos, $request->get('HdninCodLotacao'), '', 'lotacao' );
        break;
    }

    $stJs  = "limpaSelect(f.inCodAssentamento,0);                                      \n";
    $stJs .= "f.inCodAssentamento.options[0] = new Option('Selecione','', 'selected'); \n";
    $stJs .= "f.inCodAssentamentoTxt.value='';                                         \n";

    $i = 1;
    while (!$rsAssentamentos->eof()) {
        if ( $rsAssentamentos->getCampo('cod_assentamento') == $inCodAssentamento ) {
            $stSelected = "selected";
            $stJs .= "f.inCodAssentamentoTxt.value='".$rsAssentamentos->getCampo('cod_assentamento')."';\n";
            $inAuxCodAssentamento = $rsAssentamentos->getCampo('cod_assentamento');
        } else {
            $stSelected = "";
        }
        $stJs .= "f.inCodAssentamento.options[".$i++."] = new Option('".$rsAssentamentos->getCampo('descricao')."','".$rsAssentamentos->getCampo('cod_assentamento')."', '$stSelected');\n";
        $rsAssentamentos->proximo();
    }

    $stJs .= "f.inCodAssentamento.value='".$inAuxCodAssentamento."';\n";    
    $stJs .= gerarSpanLicencaPremio($request);

    if ($boExecuta)
        sistemaLegado::executaFrameOculto( $stJs );
    else
        return $stJs;
}

function preencherLotacao($boExecuta=false)
{
    global $request;

    $obROrganogramaOrgao = new ROrganogramaOrgao;
    if ($request->get('inCodLotacao')) {
        $obROrganogramaOrgao->setCodOrgaoEstruturado( $inCodLotacao );
        $obROrganogramaOrgao->setCodOrgaoEstruturado($request->get('inCodLotacao'));
        $obROrganogramaOrgao->listarOrgaoReduzido( $rsOrgao, "", "" );
        $stNull = "";
        if ( $rsOrgao->getNumLinhas() <= 0) {
            $stJs .= 'f.inCodLotacaoTxt.value = "";';
            $stJs .= 'd.getElementById("stLotacao").innerHTML = "&nbsp;";';
        } else {
            $stJs .= 'd.getElementById("stLotacao").innerHTML = "'.($rsOrgao->getCampo('descricao')?$rsOrgao->getCampo('descricao'):$stNull ).'";';
        }
    } else {
        $stJs .= 'd.getElementById("stLotacao").innerHTML = "&nbsp;";';
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function validarAssentamento($stAcao="",&$stDescricaoClassificacao,&$stDescricaoAssentamento)
{
    global $request;

    $obErro = new erro;
    if ( !$obErro->ocorreu() ) {
        $stModoGeracao = $request->get('stModoGeracao');
        $stModoGeracao = ( !empty($stModoGeracao) ) ? $stModoGeracao : $request->get('hdnModoGeracao');

        switch ($stModoGeracao) {
            case "contrato":
            case "cgm/contrato":
                $inContrato = $request->get('inContrato');
                if (empty($inContrato)) {
                    $obErro->setDescricao("@Campo Matrícula inválido!()");
                }
            break;
            case "cargo":
                $boCargoExercido = $request->get('boCargoExercido');
                $boFuncaoExercida = $request->get('boFuncaoExercida');
                $inCodCargo = $request->get('inCodCargo');

                if ( is_null($boCargoExercido) and is_null($boFuncaoExercida)) {
                    $obErro->setDescricao("@Campo Cargo Exercido ou Função Exercida inválidos!()");
                }
                if ( !$obErro->ocorreu() and empty($inCodCargo) ) {
                    $obErro->setDescricao("@Campo Cargo inválido!()");
                }
            break;
            case "lotacao":
                if ($request->get('inCodLotacao', '') == "") {
                    $obErro->setDescricao("@Campo Lotação inválido!()");
                }
            break;
        }
    }
    if ($request->get('inCodClassificacao', '') == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Classificação inválido!()");
    }
    if ($request->get('inCodAssentamento', '') == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Assentamento inválido!()");
    }
    if ( $request->get('stDataInicial', '') == "" ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Período inválido!(Informe a data inicial)");
    }
    if ( ($request->get('stDataInicial', '') != "" and $request->get('stDataFinal', '') != "") and SistemaLegado::comparaDatas($request->get('stDataInicial'),$request->get('stDataFinal')) ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Período inválido!( Data Final (".$request->get('stDataFinal').") deve ser maior que Data Inicial (".$request->get('stDataInicial')."))");
    }
    if (Sessao::read("boValidaLicencaPremio") == "true") {
        if ( ($request->get('dtInicial', '') == "" or $request->get('dtFinal', '') == "") ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Período Aquisitivo Licença Prêmio inválido!()");
        }
        if ( ($request->get('dtInicial', '') != "" and $request->get('dtFinal', '') != "") and SistemaLegado::comparaDatas($request->get('dtInicial'),$request->get('dtFinal')) ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Período Aquisitivo Licença Prêmio inválido!( Data Final (".$request->get('dtFinal').") deve ser maior que Data Inicial (".$request->get('dtInicial')."))");
        }
    }
    $inCodRegime = $request->get('inCodRegime');
    $inCodRegimeFuncao = $request->get('inCodRegimeFuncao');
    if((!is_null($inCodRegime)&&empty($inCodRegime))||(!is_null($inCodRegimeFuncao)&&empty($inCodRegimeFuncao))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Regime inválido!()");
    }
    $inCodSubDivisao = $request->get('inCodSubDivisao');
    $inCodSubDivisaoFuncao = $request->get('inCodSubDivisaoFuncao');
    if((!is_null($inCodSubDivisao)&&empty($inCodSubDivisao))||(!is_null($inCodSubDivisaoFuncao)&&empty($inCodSubDivisaoFuncao))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Subdivisão inválido!()");
    }
    $inCodCargo = $request->get('inCodCargo');
    if((!is_null($inCodCargo)&&empty($inCodCargo))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Cargo inválido!()");
    }
    $inCodFuncao = $request->get('inCodFuncao');
    if((!is_null($inCodFuncao)&&empty($inCodFuncao))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Função inválido!()");
    }
    $dtDataAlteracaoFuncao = $request->get('dtDataAlteracaoFuncao');
    if((!is_null($dtDataAlteracaoFuncao)&&empty($dtDataAlteracaoFuncao))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Data da Alteração da Função inválido!()");
    }
    $stHorasMensais = $request->get('stHorasMensais');
    if((!is_null($stHorasMensais)&&empty($stHorasMensais))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Horas Mensais inválido!()");
    }
    $stHorasSemanais = $request->get('stHorasSemanais');
    if((!is_null($stHorasSemanais)&&empty($stHorasSemanais))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Horas Semanais inválido!()");
    }
    $inSalario = $request->get('inSalario');
    if((!is_null($inSalario)&&empty($inSalario))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Salário inválido!()");
    }
    $dtVigenciaSalario = $request->get('dtVigenciaSalario');
    if((!is_null($dtVigenciaSalario)&&empty($dtVigenciaSalario))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Vigência do Salário inválido!()");
    }
    $stObservacao = $request->get('stObservacao');
    if((!is_null($stObservacao)&&empty($stObservacao))){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Observação inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        $obRPessoalAssentamento = new RPessoalAssentamento( new RPessoalVantagem );
        $obRPessoalClassificacaoAssentamento = new RPessoalClassificacaoAssentamento();

        $obRPessoalAssentamento->setCodAssentamento($request->get('inCodAssentamento'));
        $obRPessoalAssentamento->listarAssentamento( $rsAssentamentos );
        $stDescricaoAssentamento = $rsAssentamentos->getCampo("descricao");

        $obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento($request->get('inCodClassificacao'));
        $obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
        $stDescricaoClassificacao = $rsClassificacao->getCampo("descricao");

        $arAssentamentosGerados = ( is_array(Sessao::read('arAssentamentos')) ) ? Sessao::read('arAssentamentos') : array();
        foreach ($arAssentamentosGerados as $arAssentamento) {
            if ($arAssentamento['inCodClassificacao'] == $request->get('inCodClassificacao')
            AND $arAssentamento['inCodAssentamento']  == $request->get('inCodAssentamento')) {
                $boIgual = false;
                $arPeriodo1 = array($request->get('stDataInicial'),$request->get('stDataFinal'));
                $arPeriodo2 = array($arAssentamento['stDataInicial'],$arAssentamento['stDataFinal']);
                switch ($stModoGeracao) {
                    case "contrato":
                    case "cgm/contrato":
                        $stComplemento = "contrato(".$request->get('inContrato').")";
                        if ($arAssentamento['inRegistro'] == $request->get('inContrato')) {
                            $boIgual = true;
                        }
                    break;
                    case "cargo":
                        if ($arAssentamento['stDescricaoEspecialidade'] != "") {
                            $stComplemento = "cargo/especialidade(".$arAssentamento['stDescricaoCargo']."/".$arAssentamento['stDescricaoEspecialidade'].")";
                        } else {
                            $stComplemento = "cargo(".$arAssentamento['stDescricaoCargo'].")";
                        }
                        if ($arAssentamento['inCodCargo']         == $request->get('inCodCargo')
                        AND $arAssentamento['inCodEspecialidade'] == $request->get('inCodEspecialidade')) {
                            $boIgual = true;
                        }
                    break;
                    case "lotacao":
                        $stComplemento = "lotação(".$request->get('inCodLotacao').")";
                        if ($arAssentamento['inCodLotacao'] == $request->get('inCodLotacao')) {
                            $boIgual = true;
                        }
                    break;
                }
                $stMensagem = "@Este período(".$request->get('stDataInicial')." até ".$request->get('stDataFinal').") já foi cadastrado para o ".$stComplemento.", classificação(".$stDescricaoClassificacao.") e assentamento(".$stDescricaoAssentamento.").";
                if ($stAcao == "incluir" and $boIgual) {
                    if ( verificarPeriodo($arPeriodo1,$arPeriodo2) ) {
                        $obErro->setDescricao($stMensagem);
                    }
                }
                if ($stAcao == "alterar" and $boIgual) {
                    if ( (int) $arAssentamento['inId'] !== (int) Sessao::read('inId') ) {
                        if ( verificarPeriodo($arPeriodo1,$arPeriodo2) ) {
                            $obErro->setDescricao($stMensagem);
                        }
                    }
                }
                break;
            }
        }
    }

    return $obErro;
}

function verificarPeriodo($arPeriodo1,$arPeriodo2)
{
    $boErro = false;
    list($dia,$mes,$ano) = explode("/",$arPeriodo1[0]);
    $stDataInicialP1 = $ano.$mes.$dia;
    list($dia,$mes,$ano) = explode("/",$arPeriodo1[1]);
    $stDataFinalP1 = $ano.$mes.$dia;
    list($dia,$mes,$ano) = explode("/",$arPeriodo2[0]);
    $stDataInicialP2 = $ano.$mes.$dia;
    list($dia,$mes,$ano) = explode("/",$arPeriodo2[1]);
    $stDataFinalP2 = $ano.$mes.$dia;

    if ($stDataInicialP1 >= $stDataInicialP2 and $stDataInicialP1 <= $stDataFinalP2) {
        $boErro = true;
    }
    if ($stDataFinalP1 >= $stDataInicialP2  and $stDataFinalP1 <= $stDataFinalP2) {
        $boErro = true;
    }
    if ($stDataInicialP1 < $stDataInicialP2 and $stDataFinalP1 >= $stDataInicialP2) {
        $boErro = true;
    }

    return $boErro;
}

function retornarArrayPost($stAcao,$stDescricaoClassificacao,$stDescricaoAssentamento,$request)
{    
    if ($stAcao == 'incluir') {
        $arTemp['inId'] = (is_array(Sessao::read('arAssentamentos'))) ? count(Sessao::read('arAssentamentos')) : 0;
    } else {
        $arTemp['inId'] = Sessao::read('inId');
    }
    $stModoGeracao = $request->get('stModoGeracao',$request->get('hdnModoGeracao'));
    switch ($stModoGeracao) {
        case "contrato":
            $arTemp['inRegistro']         = $request->get('inContrato');
            $arTemp['stNomCgm']           = $request->get('hdnCGM');
        break;
        case "cgm/contrato":
            $arTemp['inNumCGM']           = $request->get('inNumCGM');
            $arTemp['inCampoInner']       = $request->get('inCampoInner');
            $arTemp['inRegistro']         = $request->get('inContrato');
        break;
        case "cargo":
            $obRPessoalCargo = new RPessoalCargo;
            $obRPessoalCargo->setCodCargo( $request->get('inCodCargo') );

            if ($request->get('inCodEspecialidade', '') != "") {
                $obRPessoalEspecialidade = new RPessoalEspecialidade( $obRPessoalCargo );
                $obRPessoalEspecialidade->setCodEspecialidade( $request->get('inCodEspecialidade') );
                $obRPessoalEspecialidade->consultaEspecialidadeCargo($rsCargoEspecialidade);
            } else {
                $obRPessoalCargo->listarCargo($rsCargoEspecialidade);
            }

            $arTemp['boCargoExercido']    = $request->get('boCargoExercido');
            $arTemp['boFuncaoExercida']   = $request->get('boFuncaoExercida');
            $arTemp['inCodCargo']         = $request->get('inCodCargo');
            $arTemp['stDescricaoCargo']   = $rsCargoEspecialidade->getCampo('descricao');
            $arTemp['inCodEspecialidade'] = $request->get('inCodEspecialidade');
            $arTemp['stDescricaoEspecialidade'] = $rsCargoEspecialidade->getCampo('descricao_especialidade');
            if ( $rsCargoEspecialidade->getCampo('descricao_especialidade') != "" ) {
                $arTemp['stDescricaoCargoEspecialidade']   = $rsCargoEspecialidade->getCampo('descricao')."/".$rsCargoEspecialidade->getCampo('descricao_especialidade');
            } else {
                $arTemp['stDescricaoCargoEspecialidade']   = $rsCargoEspecialidade->getCampo('descricao');
            }
        break;
        case "lotacao":
            $arTemp['inCodLotacao']       = $request->get('inCodLotacao');
            $arTemp['HdninCodLotacao']    = $request->get('HdninCodLotacao');
        break;
    }
    //Buscando o tipo de classificacao, para evitar conflitos entre Afastamento Temporário e Afastamento Permanente
    $inCodTipoClassificacao = SistemaLegado::pegaDado("cod_tipo","pessoal".Sessao::getEntidade().".classificacao_assentamento","WHERE cod_classificacao = ".$request->get('inCodClassificacao')."", $boTransacao);

    $arTemp['inCodClassificacao']       = $request->get('inCodClassificacao');
    $arTemp['stClassificacao']          = TRIM($stDescricaoClassificacao);
    $arTemp['inCodTipoClassificacao']   = $inCodTipoClassificacao;
    $arTemp['inCodAssentamento']        = $request->get('inCodAssentamento');
    $arTemp['stAssentamento']           = TRIM($stDescricaoAssentamento);
    $arTemp['inQuantidadeDias']         = $request->get('inQuantidadeDias');
    $arTemp['stDataInicial']            = $request->get('stDataInicial');
    $arTemp['stDataFinal']              = $request->get('stDataFinal');
    $arTemp["dtInicial"]                = $request->get("dtInicial");
    $arTemp["dtFinal"]                  = $request->get("dtFinal");
    $stObservacao                       = $request->get('stObservacao');
    $stObservacao                       = stripslashes($stObservacao);
    $arTemp['stObservacao']             = TRIM($stObservacao);
    $arTemp['inCodNorma']               = $request->get('inCodNorma');
    $arTemp['inCodTipoNorma']           = $request->get('inCodTipoNorma');
    $arTemp['hdnDataAlteracaoFuncao']   = $request->get('hdnDataAlteracaoFuncao');
    $arTemp['inCodProgressao']          = $request->get('inCodProgressao');
    $arTemp['inCodRegime']              = $request->get('inCodRegime');
    $arTemp['stRegime']                 = $request->get('stRegime');
    $arTemp['inCodSubDivisao']          = $request->get('inCodSubDivisao');
    $arTemp['stSubDivisao']             = $request->get('stSubDivisao');
    $arTemp['stCargo']                  = $request->get('stCargo');
    $arTemp['inCodEspecialidadeCargo']  = $request->get('inCodEspecialidadeCargo');
    $arTemp['stEspecialidadeCargo']     = $request->get('stEspecialidadeCargo');
    $arTemp['inCodRegimeFuncao']        = $request->get('inCodRegimeFuncao');
    $arTemp['stRegimeFuncao']           = $request->get('stRegimeFuncao');
    $arTemp['inCodSubDivisaoFuncao']    = $request->get('inCodSubDivisaoFuncao');
    $arTemp['stSubDivisaoFuncao']       = $request->get('stSubDivisaoFuncao');
    $arTemp['inCodFuncao']              = $request->get('inCodFuncao');
    $arTemp['stFuncao']                 = $request->get('stFuncao');
    $arTemp['inCodEspecialidadeFuncao'] = $request->get('inCodEspecialidadeFuncao');
    $arTemp['stEspecialidadeFuncao']    = $request->get('stEspecialidadeFuncao');
    $arTemp['dtDataAlteracaoFuncao']    = $request->get('dtDataAlteracaoFuncao');
    $arTemp['stHorasMensais']           = $request->get('stHorasMensais');
    $arTemp['stHorasSemanais']          = $request->get('stHorasSemanais');
    $arTemp['inCodPadrao']              = $request->get('inCodPadrao');
    $arTemp['stPadrao']                 = $request->get('stPadrao');
    $arTemp['inSalario']                = $request->get('inSalario');
    $arTemp['dtVigenciaSalario']        = $request->get('dtVigenciaSalario');

    return $arTemp;
}

function incluirAssentamento($boExecuta=false,$request)
{
    $obErro = new erro;
    $inId = Sessao::read('inId');
    if ( !$obErro->ocorreu() and isset($inId) ) {
        $obErro->setDescricao("Há um assentamento gerado em processo de alteração.");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validarAssentamento("incluir",$stDescricaoClassificacao,$stDescricaoAssentamento);
    }
    if ( !$obErro->ocorreu() ) {
        $stModoGeracao = $request->get('stModoGeracao');
        Sessao::write('stModoGeracao',$stModoGeracao );
        $arAssentamentos = Sessao::read('arAssentamentos');
        $arArquivosDigitais = ( is_array( Sessao::read('arArquivosDigitais') ) ) ? Sessao::read('arArquivosDigitais') : array();
        $arAssentamentoAtual = ( is_array( Sessao::read('arAssentamentoAtual') ) ) ? Sessao::read('arAssentamentoAtual') : array();

        $arAssentamentos[] = retornarArrayPost('incluir',$stDescricaoClassificacao,$stDescricaoAssentamento,$request);
        $arAssentamentos[count($arAssentamentos)-1]['arNormas'] = Sessao::read('arNormas');
        Sessao::remove('arNormas');
        Sessao::write('arAssentamentos', $arAssentamentos);
        $stJs .= "f.stModoGeracao.disabled = true;  \n";
        $stJs .= montaListaNorma();
        $stJs .= montarListaAssentamento();
        $stJs .= limparAssentamento();

        $boAtualizaArqAssentamentoAtual = FALSE;

        $inChave = $request->get($arAssentamentoAtual['stNomeChave']);
        if( $arAssentamentoAtual['inChave'] != $inChave )
            $boAtualizaArqAssentamentoAtual = TRUE;
        if( $arAssentamentoAtual['inCodClassificacao'] != $request->get('inCodClassificacao') )
            $boAtualizaArqAssentamentoAtual = TRUE;
        if( $arAssentamentoAtual['inCodAssentamento'] != $request->get('inCodAssentamento') )
            $boAtualizaArqAssentamentoAtual = TRUE;
        if( $arAssentamentoAtual['stDataInicial'] != $request->get('stDataInicial') )
            $boAtualizaArqAssentamentoAtual = TRUE;
        if( $arAssentamentoAtual['stDataFinal'] != $request->get('stDataFinal') )
            $boAtualizaArqAssentamentoAtual = TRUE;

        if($boAtualizaArqAssentamentoAtual){
            $stDirTMP = CAM_GRH_PESSOAL."tmp/";
            $stDirANEXO = CAM_GRH_PESSOAL."anexos/";
            $arArquivosTemp = array();
            foreach($arArquivosDigitais AS $chave => $arquivo){
                if( $arquivo['inIdAssentamento'] == $inId && $arquivo['boCopiado'] == 'FALSE'){
                    $arquivo['inChave']            = $inChave;
                    $arquivo['inCodClassificacao'] = $request->get('inCodClassificacao');
                    $arquivo['inCodAssentamento']  = $request->get('inCodAssentamento');
                    $arquivo['stDataInicial']      = $request->get('stDataInicial');
                    $arquivo['stDataFinal']        = $request->get('stDataFinal');

                    $stNameArq  = $inChave;
                    $stNameArq .= Sessao::getEntidade();
                    $stNameArq .= '_'.$request->get('inCodClassificacao');
                    $stNameArq .= '_'.$request->get('inCodAssentamento');

                    $arDataInicial = explode('/', $request->get('stDataInicial'));
                    $stNameArq .= '_'.$arDataInicial[0].'_'.$arDataInicial[1].'_'.$arDataInicial[2];

                    if($request->get('stDataFinal', '') != ''){
                        $arDataFinal = explode('/', $request->get('stDataFinal'));
                        $stNameArq .= '_'.$arDataFinal[0].'_'.$arDataFinal[1].'_'.$arDataFinal[2];
                    }

                    $stNameArq .= '_'.$arquivo['name'];

                    rename($arquivo['tmp_name'], $stDirTMP.$stNameArq);

                    $arquivo['arquivo_digital'] = $stNameArq;
                    $arquivo['tmp_name']        = $stDirTMP.$stNameArq;
                    $arquivo['stArquivo']       = $stDirANEXO.$stNameArq;
                }

                $arArquivosTemp[] = $arquivo;
            }

            Sessao::write('arArquivosDigitais', $arArquivosTemp);
        }

    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."'); \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function alterarAssentamento($boExecuta=false,$request)
{
    $obErro = new erro;
    $inId = Sessao::read('inId');
    if ( !$obErro->ocorreu() and !isset($inId) ) {
        $obErro->setDescricao("Não há nenhum assentamento gerado em processo de alteração.");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validarAssentamento("alterar",$stDescricaoClassificacao,$stDescricaoAssentamento);
    }
    if ( !$obErro->ocorreu() ) {
        $arAssentamentos = Sessao::read('arAssentamentos');
        $arArquivosDigitais = ( is_array( Sessao::read('arArquivosDigitais') ) ) ? Sessao::read('arArquivosDigitais') : array();
        $arAssentamentoAtual = ( is_array( Sessao::read('arAssentamentoAtual') ) ) ? Sessao::read('arAssentamentoAtual') : array();

        $arAssentamentos[$inId] = retornarArrayPost('alterar',$stDescricaoClassificacao,$stDescricaoAssentamento,$request);
        $arAssentamentos[$inId]['arNormas'] = Sessao::read('arNormas');
        Sessao::remove('arNormas');
        Sessao::write('arAssentamentos', $arAssentamentos);
        Sessao::remove("inCodNormas");
        $stJs .= montaListaNorma();
        $stJs .= montarListaAssentamento();
        $stJs .= limparAssentamento();

        $boAtualizaArqAssentamentoAtual = FALSE;

        $inChave = $request->get($arAssentamentoAtual['stNomeChave']);
        if( $arAssentamentoAtual['inChave'] != $inChave )
            $boAtualizaArqAssentamentoAtual = TRUE;
        if( $arAssentamentoAtual['inCodClassificacao'] != $request->get('inCodClassificacao') )
            $boAtualizaArqAssentamentoAtual = TRUE;
        if( $arAssentamentoAtual['inCodAssentamento'] != $request->get('inCodAssentamento') )
            $boAtualizaArqAssentamentoAtual = TRUE;
        if( $arAssentamentoAtual['stDataInicial'] != $request->get('stDataInicial') )
            $boAtualizaArqAssentamentoAtual = TRUE;
        if( $arAssentamentoAtual['stDataFinal'] != $request->get('stDataFinal') )
            $boAtualizaArqAssentamentoAtual = TRUE;

        if($boAtualizaArqAssentamentoAtual){
            $stDirTMP = CAM_GRH_PESSOAL."tmp/";
            $stDirANEXO = CAM_GRH_PESSOAL."anexos/";
            $arArquivosTemp = array();
            foreach($arArquivosDigitais AS $chave => $arquivo){
                if( $arquivo['inIdAssentamento'] == $inId && $arquivo['boCopiado'] == 'FALSE'){
                    $arquivo['inChave']            = $inChave;
                    $arquivo['inCodClassificacao'] = $request->get('inCodClassificacao');
                    $arquivo['inCodAssentamento']  = $request->get('inCodAssentamento');
                    $arquivo['stDataInicial']      = $request->get('stDataInicial');
                    $arquivo['stDataFinal']        = $request->get('stDataFinal');

                    $stNameArq  = $inChave;
                    $stNameArq .= Sessao::getEntidade();
                    $stNameArq .= '_'.$request->get('inCodClassificacao');
                    $stNameArq .= '_'.$request->get('inCodAssentamento');

                    $arDataInicial = explode('/', $request->get('stDataInicial'));
                    $stNameArq .= '_'.$arDataInicial[0].'_'.$arDataInicial[1].'_'.$arDataInicial[2];

                    if($request->get('stDataFinal', '') != ''){
                        $arDataFinal = explode('/', $request->get('stDataFinal'));
                        $stNameArq .= '_'.$arDataFinal[0].'_'.$arDataFinal[1].'_'.$arDataFinal[2];
                    }

                    $stNameArq .= '_'.$arquivo['name'];

                    rename($arquivo['tmp_name'], $stDirTMP.$stNameArq);

                    $arquivo['arquivo_digital'] = $stNameArq;
                    $arquivo['tmp_name']        = $stDirTMP.$stNameArq;
                    $arquivo['stArquivo']       = $stDirANEXO.$stNameArq;
                }

                $arArquivosTemp[] = $arquivo;
            }

            Sessao::write('arArquivosDigitais', $arArquivosTemp);
        }

    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."'); \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparAssentamento($boExecuta=false)
{
    global $request;
    
    $stModoGeracao = $request->get('stModoGeracao');
    $stModoGeracao = ( !empty($stModoGeracao) ) ? $stModoGeracao : $request->get('hdnModoGeracao');

    switch ($stModoGeracao) {
        case "contrato":
            $stJs .= "f.inContrato.value = '';                                              \n";
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';                    \n";
        break;
        case "cgm/contrato":
            $stJs .= "f.inNumCGM.value = '';                                                \n";
            $stJs .= "f.inCampoInner.value = '';                                            \n";
            $stJs .= "d.getElementById('inCampoInner').innerHTML = '&nbsp;';                \n";
            $stJs .= "limpaSelect(f.inContrato,0);                                          \n";
            $stJs .= "f.inContrato.options[0] = new Option('Selecione','', 'selected');     \n";
        break;
        case "cargo":
            $stJs .= "f.boCargoExercido.ckecked = false;                                    \n";
            $stJs .= "f.boFuncaoExercida.checked = false;                                   \n";
            $stJs .= "f.inCodCargoTxt.value = '';                                           \n";
            $stJs .= "f.inCodCargo.value = '';                                              \n";
            $stJs .= "f.inCodExpecialidadeTxt.value = '';                                   \n";
            $stJs .= "limpaSelect(f.inCodEspecialidade,0);                                  \n";
            $stJs .= "f.inCodEspecialidade.options[0] = new Option('Selecione','', 'selected');     \n";
        break;
        case "lotacao":
            $stJs .= "f.inCodLotacao.value = '';                                            \n";
            $stJs .= "d.getElementById('stLotacao').innerHTML = '&nbsp;';                   \n";
        break;
    }
    $stJs .= "f.inCodContrato.value         = '';                                           \n";
    $stJs .= "f.inCodMatricula.value        = '';                                           \n";
    $stJs .= "f.inCodClassificacao.value    = '';                                           \n";
    $stJs .= "f.inCodClassificacaoTxt.value = '';                                           \n";
    $stJs .= "limpaSelect(f.inCodAssentamento,0);                                           \n";
    $stJs .= "f.inCodAssentamento.options[0] = new Option('Selecione','', 'selected');      \n";
    $stJs .= "f.inCodAssentamentoTxt.value  = '';                                           \n";
    $stJs .= "f.inQuantidadeDias.value      = '';                                           \n";
    $stJs .= "f.stDataInicial.value         = '';                                           \n";
    $stJs .= "f.stDataFinal.value           = '';                                           \n";
    $stJs .= "d.getElementById('spnLicencaPremio').innerHTML = '';                          \n";
    $stJs .= "f.stObservacao.value          = '';                                           \n";
    $stJs .= "f.stCodNorma.value           = '';                                            \n";
    $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';                             \n";
    $stJs .= "d.getElementById('spnCargoFuncaoSalario').innerHTML = '';                     \n";
    Sessao::remove('arNormas');
    $stJs .= montaListaNorma();
    Sessao::remove('inId');
    Sessao::remove('inCodClassificacao');
    $stJs .= preencheListaArqDigital();
    $stJs .= limparArqDigital();
    Sessao::remove('arAssentamentoAtual');

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function excluirAssentamento($boExecuta=false)
{
    global $request;

    $arTemp = array();
    $arArquivosTemp = array();

    $arAssentamentos = ( is_array( Sessao::read('arAssentamentos') ) ) ? Sessao::read('arAssentamentos') : array();
    $arArquivosDigitais = ( is_array( Sessao::read('arArquivosDigitais') ) ) ? Sessao::read('arArquivosDigitais') : array();

    foreach ($arAssentamentos as $arAssentamento) {
        if ($arAssentamento['inId'] != $request->get('inId')) {
            $inIdAssentamento = count($arTemp);

            foreach($arArquivosDigitais AS $chave => $arquivo){
                if( $arquivo['inIdAssentamento'] == $arAssentamento['inId'] ){
                    $arquivo['inIdAssentamento'] = $inIdAssentamento;
                    $arArquivosTemp[] = $arquivo;
                }
            }

            $arAssentamento['inId'] = $inIdAssentamento;
            $arTemp[] = $arAssentamento;
        }
    }

    if ( count($arTemp) == 0 )
        $stJs .= "f.stModoGeracao.disabled = false;";

    $inId = Sessao::read('inId');
    if(!is_null($inId) && $inId == $request->get('inId'))
        $stJs .= limparAssentamento();

    foreach($arArquivosDigitais AS $chave => $arquivo){
        if( $arquivo['inIdAssentamento'] == $request->get('inId')){
            $stErro = excluirArqDigital($arquivo['inId']);

            if ($stErro)
                $stJs .= "alertaAviso('".urlencode("Erro ao Excluir Arquivo Digital: ".$stErro)."','unica','erro','".Sessao::getId()."');";
        }
    }

    $arAssentamentos = $arTemp;
    $arArquivosDigitais = $arArquivosTemp;

    Sessao::write('arAssentamentos', $arAssentamentos);
    Sessao::write('arArquivosDigitais', $arArquivosDigitais);
    $stJs .= montarListaAssentamento();

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaAlterarAssentamento($boExecuta=false, Request $request)
{
    $arAssentamentos = Sessao::read('arAssentamentos');
    $arAssentamento  = $arAssentamentos[$request->get('inId')];
    $stModoGeracao = $request->get('stModoGeracao');
    $stModoGeracao = ( !empty($stModoGeracao) ) ? $stModoGeracao : $request->get('hdnModoGeracao');

    switch ($stModoGeracao) {
        case "contrato":
            $stJs .= "f.inContrato.value                     = '".$arAssentamento['inRegistro']."';         \n";
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '".$arAssentamento['stNomCgm']  ."';         \n";
            $stJs .= "f.hdnCGM.value                         = '".$arAssentamento['stNomCgm']  ."';         \n";

            require_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php";
            $obTPessoalServidor = new TPessoalServidor;
            $stFiltro = "AND pc.registro = ".$arAssentamento['inRegistro'];
            $obTPessoalServidor->recuperaRegistrosServidor($rsContrato, $stFiltro);

            $stJs .= "f.inCodContrato.value                  = '".$rsContrato->getCampo('cod_contrato')."'; \n";
            $inChave = $rsContrato->getCampo('cod_contrato');
            $stNomeChave = 'inCodContrato';
            $request->set('inContrato', $arAssentamento['inRegistro']);
        break;
        case "cgm/contrato":
            $stJs .= "f.inNumCGM.value                       = '".$arAssentamento['inNumCGM']  ."';         \n";
            $stJs .= "f.inCampoInner.value                   = '".$arAssentamento['inCampoInner']."';       \n";
            $stJs .= "d.getElementById('inCampoInner').innerHTML = '".$arAssentamento['inCampoInner']."';   \n";
            $stJs .= "limpaSelect(f.inContrato,0);                                                          \n";
            $stJs .= "f.inContrato.options[0] = new Option('Selecione','', 'selected');                     \n";
            $obRPessoalServidor = new RPessoalServidor;
            $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM($arAssentamento['inNumCGM']);
            $obRPessoalServidor->consultaRegistrosServidor($rsRegistros);
            $inIndex = 1;
            while ( !$rsRegistros->eof() ) {
                $stJs .= "f.inContrato[".$inIndex."] = new Option('".$rsRegistros->getCampo('registro')."','".$rsRegistros->getCampo('registro')."','');\n";
                $inIndex++;
                $rsRegistros->proximo();
            }
            $stJs .= "f.inContrato.value                     = '".$arAssentamento['inRegistro']."';         \n";
            $inChave = $arAssentamento['inRegistro'];
            $stNomeChave = 'inContrato';
            $request->set('inContrato', $arAssentamento['inRegistro']);
        break;
        case "cargo":
            $boCargoExercido = ($arAssentamento['boCargoExercido'] == "") ? 'false' : $arAssentamento['boCargoExercido'];
            $stJs .= "f.boCargoExercido.ckecked         = ".$boCargoExercido.";                             \n";
            if ($arAssentamento['boFuncaoExercida'] == "") {
                $stJs .= "f.boFuncaoExercida.checked        = false;                                        \n";
            } else {
                $stJs .= "f.boFuncaoExercida.checked        = ".$arAssentamento['boFuncaoExercida'].";      \n";
            }
            $stJs .= "f.inCodCargoTxt.value             = '".$arAssentamento['inCodCargo']."';              \n";
            $stJs .= "f.inCodCargo.value                = '".$arAssentamento['inCodCargo']."';              \n";
            $stJs .= "f.inCodExpecialidadeTxt.value     = '".$arAssentamento['inCodEspecialidade']."';      \n";
            $stJs .= "limpaSelect(f.inCodEspecialidade,0);                                                  \n";
            $obRPessoalCargo = new RPessoalCargo;
            $obRPessoalCargo->setCodCargo( $request->get('inCodCargo') );
            $obRPessoalEspecialidade = new RPessoalEspecialidade( $obRPessoalCargo );
            $obRPessoalEspecialidade->consultaEspecialidadeCargo($rsEspecialidades);
            $stJs .= "f.inCodEspecialidade.options[0] = new Option('Selecione','', 'selected');             \n";
            $inIndex = 1;
            while (!$rsEspecialidades->eof()) {
                $stJs .= "f.inCodEspecialidade.options[$inIndex] = new Option('".$rsEspecialidades->getCampo('descricao_especialidade')."','".$rsEspecialidades->getCampo('cod_especialidade')."', '');     \n";
                $inIndex++;
                $rsEspecialidades->proximo();
            }
            $stJs .= "f.inCodEspecialidade.value     = '".$arAssentamento['inCodEspecialidade']."';         \n";
            $inChave = $arAssentamento['inCodCargo'];
            $stNomeChave = 'inCodCargo';
            $request->set('inCodCargo', $arAssentamento['inCodCargo']);
        break;
        case "lotacao":
            $obROrganogramaOrgao = new ROrganogramaOrgao;
            $obROrganogramaOrgao->setCodOrgaoEstruturado( $arAssentamento['inCodLotacao'] );
            $obROrganogramaOrgao->listarOrgaoReduzido( $rsOrgaoReduzido );

            $stJs .= "f.HdninCodLotacao.value = '".$arAssentamento['HdninCodLotacao']."';                     \n";
            $stJs .= "f.inCodLotacao.value = '".$arAssentamento['inCodLotacao']."';                           \n";
            $stJs .= "d.getElementById('stLotacao').innerHTML = '".$rsOrgaoReduzido->getCampo('descricao')."';\n";
            $inChave = $arAssentamento['HdninCodLotacao'];
            $stNomeChave = 'HdninCodLotacao';
            $request->set('HdninCodLotacao', $arAssentamento['HdninCodLotacao']);
        break;
    }

    $stJs .= limparArquivosAssentamentoAtual();

    $request->set('inCodClassificacao'    , $arAssentamento['inCodClassificacao']);
    $request->set('inCodClassificacaoTxt' , $arAssentamento['inCodClassificacao']);
    $request->set('inCodAssentamento'     , $arAssentamento['inCodAssentamento']);
    $request->set('dtInicial'             , $arAssentamento['dtInicial']);
    $request->set('dtFinal'               , $arAssentamento['dtFinal']);
    Sessao::write('inId', $request->get('inId'));

    $stJs .= "f.inCodClassificacaoTxt.value = ".$arAssentamento['inCodClassificacao'].";\n";
    $stJs .= "f.inCodClassificacao.value    = ".$arAssentamento['inCodClassificacao'].";\n";
    $stJs .= preencherAssentamento(false, $request);
    $stJs .= "f.inCodAssentamentoTxt.value  = ".$arAssentamento['inCodAssentamento']."; \n";
    $stJs .= "f.inCodAssentamento.value     = ".$arAssentamento['inCodAssentamento']."; \n";
    $stJs .= "f.inQuantidadeDias.value      = '".$arAssentamento['inQuantidadeDias']."';\n";
    $stJs .= "f.stDataInicial.value         = '".$arAssentamento['stDataInicial']   ."';\n";
    $stJs .= "f.stDataFinal.value           = '".$arAssentamento['stDataFinal']     ."';\n";
    Sessao::write('arNormas', $arAssentamento['arNormas']);
    $stJs .= montaListaNorma();
    $stJs .= gerarSpanLicencaPremio($request);
    $stJs .= buscaContrato($arAssentamento['inRegistro']);
    $stJs .= "f.stObservacao.value          = \"".$arAssentamento['stObservacao']   ."\";\n";

    $arAssentamentoAtual = array();
    $arAssentamentoAtual['stModoGeracao']      = $stModoGeracao;
    $arAssentamentoAtual['inChave']            = $inChave;
    $arAssentamentoAtual['stNomeChave']        = $stNomeChave;
    $arAssentamentoAtual['inCodClassificacao'] = $arAssentamento['inCodClassificacao'];
    $arAssentamentoAtual['inCodAssentamento']  = $arAssentamento['inCodAssentamento'];
    $arAssentamentoAtual['stDataInicial']      = $arAssentamento['stDataInicial'];
    $arAssentamentoAtual['stDataFinal']        = $arAssentamento['stDataFinal'];
    $arAssentamentoAtual['inIdAssentamento']   = $arAssentamento['inId'];
    Sessao::write("arAssentamentoAtual", $arAssentamentoAtual);

    $stJs .= montaListaArqDigital($stModoGeracao, $inChave, $arAssentamento['inCodClassificacao'], $arAssentamento['inCodAssentamento'], $arAssentamento['stDataInicial'], $arAssentamento['stDataFinal']);

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montarListaAssentamento($boExecuta=false)
{
    global $request;
    
    $rsAssentamentosGerados = new recordset;
    $arAssentamentosGerados = ( is_array(Sessao::read('arAssentamentos')) ) ? Sessao::read('arAssentamentos') : array();
    $rsAssentamentosGerados->preenche($arAssentamentosGerados);
    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsAssentamentosGerados );
    $obLista->setTitulo     ("Assentamentos Gerados");
    $obLista->setMostraPaginacao(false);

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $stModoGeracao = $request->get('stModoGeracao');
    $stModoGeracao = ( !empty($stModoGeracao) ) ? $stModoGeracao : $request->get('hdnModoGeracao');

    switch ($stModoGeracao) {
        case "contrato":
        case "cgm/contrato":
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Matrícula");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();
        break;
        case "cargo":
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Cargo/Especialidade");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();
        break;
        case "lotacao":
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Lotação");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();
        break;
    }

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Classificação");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Assentamento");
    $obLista->ultimoCabecalho->setWidth( 24 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Período");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    switch ($stModoGeracao) {
        case "contrato":
        case "cgm/contrato":
            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("CENTRO");
            $obLista->ultimoDado->setCampo( "inRegistro" );
            $obLista->commitDado();
        break;
        case "cargo":
            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("CENTRO");
            $obLista->ultimoDado->setCampo( "stDescricaoCargoEspecialidade" );
            $obLista->commitDado();
        break;
        case "lotacao":
            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("CENTRO");
            $obLista->ultimoDado->setCampo( "inCodLotacao" );
            $obLista->commitDado();
        break;
    }

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stClassificacao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stAssentamento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[stDataInicial] à [stDataFinal]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "alterar" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript:modificaDado('montaAlterarAssentamento');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript:modificaDado('excluirAssentamento');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnSpan2').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function processarForm($boExecuta = false, $stArquivo = "Form", $stAcao = "incluir")
{
    global $request;

    $inCodAssentamento  = Sessao::read('inCodAssentamento');
    $inCodClassificacao = Sessao::read('inCodClassificacao');
    $stDataInicial      = Sessao::read('stDataInicial');
    $stDataFinal        = Sessao::read('stDataFinal');

    switch ($stAcao) {
        case "incluir":
            $stJs  = gerarSpan1($boExecuta,$stArquivo);
        break;

        case "alterar":
            $stJs  = preencherAssentamento($boExecuta, $request);
            $stJs .= processarTriadi(3);
            $stJs .= buscaNormas();
            $stJs .= montaListaNorma();
            $stJs .= buscaContrato($request->get('inRegistro'), false);
            
            $request->set('inRegistro', $request->get('inCodContrato'));
            $arDados = carregaDados($request);

            $inCodMotivo = SistemaLegado::pegaDado("cod_motivo"
                                            ,"pessoal.assentamento_assentamento"
                                            ,"WHERE cod_assentamento = ".$inCodAssentamento." AND cod_classificacao = ".$inCodClassificacao."");

            if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) ){
                $stJs .= gerarSpanCargoFuncaoSalario($arDados);
                $stJs .= preencheSubDivisaoAlterar();
                $stJs .= preencheCargoAlterar();
                $stJs .= preencheEspecialidadeAlterar();
                $stJs .= preencheSubDivisaoFuncaoAlterar();
                $stJs .= preencheFuncaoAlterar();
                $stJs .= preencheEspecialidadeFuncaoAlterar();
                $stJs .= preencheInformacoesSalariais($arDados['inCodFuncao'], $arDados['inCodEspecialidadeFuncao']);
                $stJs .= preencheProgressaoAlterar();
            }

            $stJs .= montaListaArqDigital('contrato', $arDados['inCodContrato'], $inCodClassificacao, $inCodAssentamento, $stDataInicial, $stDataFinal);
        break;

        case "excluir":
        case "consultar":
            $stJs  = buscaNormas();
            $stJs .= montaListaNorma();
            $stJs .= processarTriadi(3,true);
            $stJs .= buscaContrato($request->get('inRegistro'), false);
            $stJs .= montaListaArqDigital('contrato', $request->get('inCodContrato'), $inCodClassificacao, $inCodAssentamento, $stDataInicial, $stDataFinal);
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function submeter($boExecuta=false)
{
    if (is_array(Sessao::read('arAssentamentos'))) {
        $stJs .= "parent.frames[2].Salvar();    \n";
    } else {
        $stMensagem = "Lista de assentamento inválida!(Informe os assentamentos a serem gerados.)";
        $stJs .= "alertaAviso('@$stMensagem','form','erro','".Sessao::getId()."'); \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function processarTriadi($inCampo,$boSpan=false)
{
    global $request;
    
    $rsContrato = new RecordSet();
    $stDataInicial = ( $request->get('stDataInicial', '') != "" ) ? $request->get('stDataInicial') : Sessao::read('stDataInicial');
    $stDataFinal   = ( $request->get('stDataFinal', '')   != "" ) ? $request->get('stDataFinal')   : Sessao::read('stDataFinal');

    include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
    $obTPessoalContrato = new TPessoalContrato();

    $inMatricula = $request->get('inContrato');

    if (empty($inMatricula) && $inMatricula !== 0) {
        $inMatricula = $request->get('inRegistro');
    }

    if($request->get('stAcao') != 'incluir'){
        $request->set('stModoGeracao', 'contrato');
    }

    $stMensagem = "";
    if (($inMatricula=='' && $request->get('stModoGeracao') =='contrato') || ($inMatricula=='' && $request->get('stModoGeracao') =='cgm/contrato')) {
        $stMensagem = "@Informe primeiro a Matrícula.";
    } elseif ($request->get('inCodCargo', '')=='' && $request->get('stModoGeracao') =='cargo') {
        $stMensagem = "@Informe primeiro o Cargo.";
    } elseif ($request->get('inCodLotacao', '')=='' && $request->get('stModoGeracao') =='lotacao') {
        $stMensagem = "@Informe primeiro a Lotação.";
    }

    if (!empty($stMensagem)) {
        if($request->get('stAcao') == 'incluir')
            $stJs .= "f.btIncluir.disabled = true; \n";
        $stJs .= "f.stDataInicial.value = '';\n";
        $stJs .= "f.stDataFinal.value = '';\n";
        $stJs .= "f.inQuantidadeDias.value = '';\n";
        $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."'); \n";
    } else {
        $boContrato = FALSE;
        switch ($request->get('stModoGeracao')) {
            case 'contrato':
            case 'cgm/contrato':
                $stFiltro = " WHERE registro = ".$inMatricula;
                $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
                $boContrato = TRUE;
                break;
            case 'cargo':
                $stFiltro = " WHERE cod_cargo = ".$request->get('inCodCargo');
                if ($request->get('boFuncaoExercida')) {
                    include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php";
                    $obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao;
                    $obTPessoalContratoServidorFuncao->recuperaTodos( $rsContrato, $stFiltro );
                } else {
                    include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php";
                    $obTPessoalContratoServidor = new TPessoalContratoServidor;
                    $obTPessoalContratoServidor->recuperaTodos( $rsContrato, $stFiltro );
                }
                break;
            case 'lotacao':
                include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php";
                $stFiltro = " WHERE pcso.cod_orgao = ".$request->get('HdninCodLotacao');
                $obTPessoalContratoServidor = new TPessoalContratoServidor;
                $obTPessoalContratoServidor->recuperaContratosLotacao( $rsContrato, $stFiltro );
                break;
        }

        if ($request->get('inCodClassificacao', '') != "") {
            include_once CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php";
            $obTPessoalClassificacaoAssentamento = new TPessoalClassificacaoAssentamento();
            $stFiltro  = " AND ca.cod_classificacao = ".$request->get("inCodClassificacao");
            $obTPessoalClassificacaoAssentamento->recuperaRelacionamento($rsClassificacao,$stFiltro);
            $stTipoClassificacao = $rsClassificacao->getCampo('cod_tipo');
        } else {
            $stTipoClassificacao = '0';
        }

        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php";
        $rsContratoServidorCasoCausa = new RecordSet;
        $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
        $arContrato = array();
        foreach ($rsContrato->getElementos() as $contrato) {
            $stFiltro = " AND contrato.cod_contrato = ".$contrato["cod_contrato"];
            if ($stDataInicial && !$stDataFinal) {
                $stFiltro .= " AND (dt_rescisao < to_date('".$stDataInicial."','dd/mm/yyyy'))";
            } elseif (!$stDataInicial && $stDataFinal) {
                $stFiltro .= " AND (dt_rescisao < to_date('".$stDataFinal."','dd/mm/yyyy'))";
            } elseif ($stDataInicial && $stDataFinal) {
                $stFiltro .= " AND (dt_rescisao < to_date('".$stDataInicial."','dd/mm/yyyy') OR dt_rescisao < to_date('".$stDataFinal."','dd/mm/yyyy'))";
            } else {
                $stFiltro = '';
            }

            if ($stFiltro) {
                $obTPessoalContratoServidorCasoCausa->recuperaCasoCausaRegistro($rsContratoServidorCasoCausa,$stFiltro);
            }

            if ($rsContratoServidorCasoCausa->getNumLinhas() > 0 && $stTipoClassificacao != '1' && $boContrato) {
                $stMensagem .= "@Data do afastamento não deve ser posterior a ".SistemaLegado::dataToBr($rsContratoServidorCasoCausa->getCampo("dt_rescisao"))." para o contrato ".$rsContratoServidorCasoCausa->getCampo("registro");
                break;
            }else{
                $arContrato[] = $contrato["cod_contrato"];
            }
        }

        if ( ( $rsContratoServidorCasoCausa->getNumLinhas() < 0 || $stTipoClassificacao == '1' || !$boContrato ) && empty($stMensagem)) {
            if (Sessao::read("inQuantDiasAfastamentoTemporario") != "" and $request->get('inQuantidadeDias') > Sessao::read("inQuantDiasAfastamentoTemporario")) {
                $request->set('inQuantidadeDias', Sessao::read("inQuantDiasAfastamentoTemporario"));
                $stJs .= "f.inQuantidadeDias.value = '".Sessao::read("inQuantDiasAfastamentoTemporario")."';\n";
            }
            $inQuantDias   = $request->get('inQuantidadeDias');
            switch ($inCampo) {
                case 1:
                    switch (true) {
                        case $inQuantDias == "":
                            $stJs .= "f.stDataFinal.value = '';\n";
                            break;
                        case $inQuantDias != "" and $stDataInicial != "":
                            $stJs .= calcularDataFinal();
                            break;
                    }
                    break;
                case 2:
                    switch (true) {
                        case $inQuantDias != "" and $stDataFinal == "":
                            $stJs .= calcularDataFinal();
                            break;
                        case $inQuantDias == "" and $stDataFinal != "" OR
                            $inQuantDias != "" and $stDataFinal != "":
                            $stJs .=  ajustarQuantidadeDias(false,$boSpan);
                            break;
                    }
                    break;
                case 3:
                    switch (true) {
                        case $stDataFinal == "":
                            $stJs .= "f.inQuantidadeDias.value = '';\n";
                            break;
                        case $inQuantDias != "" and $stDataInicial != "":
                            $stJs .= ajustarQuantidadeDias(false,$boSpan);
                            break;
                        case $stDataFinal != "" and $stDataInicial != "":
                            $stJs .= ajustarQuantidadeDias(false,$boSpan);
                            break;
                    }
                    break;
            }
        } else {
            $stJs .= "f.stDataInicial.value = '';\n";
            $stJs .= "f.stDataFinal.value = '';\n";
            $stJs .= "f.inQuantidadeDias.value = '';\n";
            if( empty($stMensagem) )
                $stMensagem = "@Data do afastamento não deve ser posterior a ".SistemaLegado::dataToBr($rsContratoServidorCasoCausa->getCampo("dt_rescisao"));
            $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."'); \n";
        }
    }

    return $stJs;
}

function processarQuantDiasAssentamento(Request $request)
{
    $inDias = "";
    $inCodClassificacao = $request->get('inCodClassificacao');
    $inCodAssentamento = $request->get('inCodAssentamento');

    if (!empty($inCodClassificacao)) {
        require_once CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php";
        $obTPessoalClassificacaoAssentamento = new TPessoalClassificacaoAssentamento();
        $stFiltro  = " AND ca.cod_classificacao = ".$inCodClassificacao;
        $stFiltro .= " AND ca.cod_tipo = 2";
        $obTPessoalClassificacaoAssentamento->recuperaRelacionamento($rsClassificacao,$stFiltro);
    }
    if (!empty($inCodAssentamento) && ($rsClassificacao->getNumLinhas() == 1) ) {
        require_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamento.class.php";
        $obTPessoalAssentamento = new TPessoalAssentamento();
        $stFiltro  = " AND A.cod_assentamento = ".$inCodAssentamento;
        $obTPessoalAssentamento->recuperaAssentamentos($rsAssentamento,$stFiltro);
        if ($rsAssentamento->getNumLinhas() == 1)
            $inDias = $rsAssentamento->getCampo("dia");
    }
    
    if ( !empty($inCodAssentamento) && !empty($inCodClassificacao) ) {
        $inCodMotivo = SistemaLegado::pegaDado("cod_motivo","pessoal.assentamento_assentamento","WHERE cod_assentamento = ".$inCodAssentamento." AND cod_classificacao = ".$inCodClassificacao."");
        //Verifica se o cod_motivo é '18 - Readaptação' ou '14 - Alteração de Cargo'
        if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) ){            
            $request->set('inRegistro', $request->get('inCodContrato'));
            $arDados = carregaDados($request);            
            $stJs .= gerarSpanCargoFuncaoSalario($arDados);
        }
    }else
        $stJs .= "d.getElementById('spnCargoFuncaoSalario').innerHTML = '';\n";

    Sessao::write("inQuantDiasAfastamentoTemporario", $inDias);
    $stJs .= "f.inQuantidadeDias.value = '".$inDias."';\n";
    $stJs .= gerarSpanLicencaPremio($request);

    return $stJs;
}

function gerarSpanCargoFuncaoSalario($arDados = "")
{
    global $request;

    $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao();
    $obRPessoalRegime = new RPessoalRegime();
    $obRPessoalServidor = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();

    $stHtml = "";
    $stEval = "";
    
    //-------------------------------------------
    //---------------CARGO-----------------------
    //-------------------------------------------
    //INFORMAÇÕES DO CARGO
    //Selecão da regime
    $obTxtCodRegime = new TextBox;
    $obTxtCodRegime->setRotulo                  ( "Regime"                              );
    $obTxtCodRegime->setName                    ( "inCodRegime"                         );
    $obTxtCodRegime->setTitle                   ( "Informe o regime de trabalho."       );
    $obTxtCodRegime->setSize                    ( 10                                    );
    $obTxtCodRegime->setMaxLength               ( 8                                     );
    $obTxtCodRegime->setInteiro                 ( true                                  );
    $obTxtCodRegime->setNull                    ( true                                  );
    $obTxtCodRegime->obEvento->setOnChange      ( "buscaValor('preencheSubDivisao');preencheCampo( this, document.frm.inCodRegimeFuncao);preencheCampo( this, document.frm.stRegimeFuncao);"    );
    
    $obRPessoalRegime->listarRegime( $rsRegime, "", $boTransacao );

    $obCmbCodRegime = new Select;
    $obCmbCodRegime->setName                    ( "stRegime"                            );
    $obCmbCodRegime->setRotulo                  ( "Regime"                              );
    $obCmbCodRegime->setTitle                   ( "Selecione o regime."                 );
    $obCmbCodRegime->setNull                    ( false                                 );
    $obCmbCodRegime->setCampoId                 ( "[cod_regime]"                        );
    $obCmbCodRegime->setCampoDesc               ( "descricao"                           );
    $obCmbCodRegime->addOption                  ( "", "Selecione"                       );
    $obCmbCodRegime->preencheCombo              ( $rsRegime                             );
    $obCmbCodRegime->obEvento->setOnChange      ( "buscaValor('preencheSubDivisao');preencheCampo( this, document.frm.inCodRegimeFuncao);preencheCampo( this, document.frm.stRegimeFuncao);"    );
    
    //Selecão da Sub-divisao
    $obTxtCodSubDivisao = new TextBox;
    $obTxtCodSubDivisao->setRotulo              ( "Subdivisão"                          );
    $obTxtCodSubDivisao->setName                ( "inCodSubDivisao"                     );
    $obTxtCodSubDivisao->setTitle               ( "Selecione a subdivisão do regime."   );
    $obTxtCodSubDivisao->setSize                ( 10                                    );
    $obTxtCodSubDivisao->setMaxLength           ( 8                                     );
    $obTxtCodSubDivisao->setInteiro             ( true                                  );
    $obTxtCodSubDivisao->setNull                ( true                                  );
    $obTxtCodSubDivisao->obEvento->setOnChange ( "buscaValor('preencheCargo');preencheCampo( this, document.frm.inCodSubDivisaoFuncao);preencheCampo( this, document.frm.stSubDivisaoFuncao);" );
    
    $obCmbCodSubDivisao = new Select;
    $obCmbCodSubDivisao->setName                ( "stSubDivisao"                        );
    $obCmbCodSubDivisao->setRotulo              ( "Subdivisão"                          );
    $obCmbCodSubDivisao->setTitle               ( "Selecione a subdivisão."             );
    $obCmbCodSubDivisao->setNull                ( false                                 );
    $obCmbCodSubDivisao->setCampoId             ( "[cod_sub_divisao]"                   );
    $obCmbCodSubDivisao->setCampoDesc           ( "descricao"                           );
    $obCmbCodSubDivisao->addOption              ( "", "Selecione"                       );
    $obCmbCodSubDivisao->obEvento->setOnChange ( "buscaValor('preencheCargo');preencheCampo( this, document.frm.inCodSubDivisaoFuncao);preencheCampo( this, document.frm.stSubDivisaoFuncao);"      );
    
    $obTxtCargo = new TextBox;
    $obTxtCargo->setRotulo                      ( "Cargo"                               );
    $obTxtCargo->setName                        ( "inCodCargo"                          );
    $obTxtCargo->setTitle                       ( "Selecione o cargo do servidor."      );
    $obTxtCargo->setSize                        ( 10                                    );
    $obTxtCargo->setMaxLength                   ( 10                                    );
    $obTxtCargo->setInteiro                     ( true                                  );
    $obTxtCargo->setNull                        ( true                                  );
    $obTxtCargo->obEvento->setOnChange          ( "buscaValor('preencheEspecialidade');preencheCampo( this, document.frm.inCodFuncao);preencheCampo( this, document.frm.stFuncao);" );
    
    $obCmbCargo = new Select;
    $obCmbCargo->setName                        ( "stCargo"                             );
    $obCmbCargo->setRotulo                      ( "Cargo"                               );
    $obCmbCargo->setTitle                       ( "Selecione o cargo do servidor."      );
    $obCmbCargo->setNull                        ( false                                 );
    $obCmbCargo->addOption                      ( "", "Selecione"                       );
    $obCmbCargo->setCampoId                     ( "[cod_cargo]"                         );
    $obCmbCargo->setCampoDesc                   ( "descricao"                           );
    $obCmbCargo->obEvento->setOnChange          ( "buscaValor('preencheEspecialidade');
                                                   preencheCampo( this, document.frm.inCodFuncao);
                                                   preencheCampo( this, document.frm.stFuncao);" );
   
    //Selecão da Especialidade Cargo
    $obTxtCodEspecialidadeCargo = new TextBox;
    $obTxtCodEspecialidadeCargo->setRotulo      ( "Especialidade"                          );
    $obTxtCodEspecialidadeCargo->setName        ( "inCodEspecialidadeCargo"                );
    $obTxtCodEspecialidadeCargo->setTitle       ( "Selecione a especialidade do servidor." );
    $obTxtCodEspecialidadeCargo->setSize        ( 10                                       );
    $obTxtCodEspecialidadeCargo->setMaxLength   ( 10                                       );
    $obTxtCodEspecialidadeCargo->setInteiro     ( true                                     );
    $obTxtCodEspecialidadeCargo->setNull        ( true                                     );
    $obTxtCodEspecialidadeCargo->obEvento->setOnChange( "buscaValor('preenchePreEspecialidadeFuncao');" );
    
    $obCmbCodEspecialidadeCargo = new Select;
    $obCmbCodEspecialidadeCargo->setName        ( "stEspecialidadeCargo"                   );
    $obCmbCodEspecialidadeCargo->setRotulo      ( "Função"                                 );
    $obCmbCodEspecialidadeCargo->setTitle       ( "Selecione a especialidade do servidor." );
    $obCmbCodEspecialidadeCargo->setNull        ( true                                     );
    $obCmbCodEspecialidadeCargo->setCampoId     ( "[cod_especialidade]"                    );
    $obCmbCodEspecialidadeCargo->setCampoDesc   ( "descricao_especialidade"                );
    $obCmbCodEspecialidadeCargo->addOption      ( "", "Selecione"                          );
    $obCmbCodEspecialidadeCargo->obEvento->setOnChange( "buscaValor('preenchePreEspecialidadeFuncao');" );
    //FIM INFORMAÇÕES DO CARGO
    
    //-------------------------------------------
    //---------------FUNÇÃO-----------------------
    //-------------------------------------------
    //INFORMAÇÕES DA FUNÇÃO
    $obTxtCodRegimeFuncao = new TextBox;
    $obTxtCodRegimeFuncao->setRotulo             ( "Regime"                             );
    $obTxtCodRegimeFuncao->setName               ( "inCodRegimeFuncao"                  );
    $obTxtCodRegimeFuncao->setTitle              ( "Informe o regime de trabalho."      );
    $obTxtCodRegimeFuncao->setSize               ( 10                                   );
    $obTxtCodRegimeFuncao->setMaxLength          ( 8                                    );
    $obTxtCodRegimeFuncao->setInteiro            ( true                                 );
    $obTxtCodRegimeFuncao->setNull               ( true                                 );
    $obTxtCodRegimeFuncao->obEvento->setOnChange ( "buscaValor('preencheSubDivisaoFuncao');" );
    
    $obRPessoalRegime->listarRegime( $rsRegime, "", $boTransacao );

    $obCmbCodRegimeFuncao = new Select;
    $obCmbCodRegimeFuncao->setName               ( "stRegimeFuncao"                     );
    $obCmbCodRegimeFuncao->setRotulo             ( "Regime"                             );
    $obCmbCodRegimeFuncao->setTitle              ( "Selecione o regime."                );
    $obCmbCodRegimeFuncao->setNull               ( false                                );
    $obCmbCodRegimeFuncao->setCampoId            ( "[cod_regime]"                       );
    $obCmbCodRegimeFuncao->setCampoDesc          ( "descricao"                          );
    $obCmbCodRegimeFuncao->addOption             ( "", "Selecione"                      );
    $obCmbCodRegimeFuncao->preencheCombo         ( $rsRegime                            );
    $obCmbCodRegimeFuncao->obEvento->setOnChange ( "buscaValor('preencheSubDivisaoFuncao');" );
    
    $obTxtCodSubDivisaoFuncao = new TextBox;
    $obTxtCodSubDivisaoFuncao->setRotulo        ( "Subdivisão"                          );
    $obTxtCodSubDivisaoFuncao->setName          ( "inCodSubDivisaoFuncao"               );
    $obTxtCodSubDivisaoFuncao->setTitle         ( "Selecione a subdivisão do regime."   );
    $obTxtCodSubDivisaoFuncao->setSize          ( 10                                    );
    $obTxtCodSubDivisaoFuncao->setMaxLength     ( 8                                     );
    $obTxtCodSubDivisaoFuncao->setInteiro       ( true                                  );
    $obTxtCodSubDivisaoFuncao->setNull          ( true                                  );
    $obTxtCodSubDivisaoFuncao->obEvento->setOnChange ( "buscaValor('preencheFuncao');"  );
    
    $obCmbCodSubDivisaoFuncao = new Select;
    $obCmbCodSubDivisaoFuncao->setName          ( "stSubDivisaoFuncao"                  );
    $obCmbCodSubDivisaoFuncao->setRotulo        ( "Subdivisão"                          );
    $obCmbCodSubDivisaoFuncao->setTitle         ( "Selecione a subdivisão."             );
    $obCmbCodSubDivisaoFuncao->setNull          ( false                                 );
    $obCmbCodSubDivisaoFuncao->setCampoId       ( "[cod_sub_divisao]"                   );
    $obCmbCodSubDivisaoFuncao->setCampoDesc     ( "descricao"                           );
    $obCmbCodSubDivisaoFuncao->addOption        ( "", "Selecione"                       );
    $obCmbCodSubDivisaoFuncao->obEvento->setOnChange ( "buscaValor('preencheFuncao');"  );
    
    //Selecão da funcao
    $obTxtCodFuncao = new TextBox;
    $obTxtCodFuncao->setRotulo                  ( "Função"                              );
    $obTxtCodFuncao->setName                    ( "inCodFuncao"                         );
    $obTxtCodFuncao->setTitle                   ( "Selecione a função do servidor."     );
    $obTxtCodFuncao->setSize                    ( 10                                    );
    $obTxtCodFuncao->setMaxLength               ( 10                                    );
    $obTxtCodFuncao->setInteiro                 ( true                                  );
    $obTxtCodFuncao->setNull                    ( false                                 );
    $obTxtCodFuncao->obEvento->setOnChange      ( " buscaValor('preencheEspecialidadeFuncao');" );
    
    $obCmbCodFuncao = new Select;
    $obCmbCodFuncao->setName                    ( "stFuncao"                            );
    $obCmbCodFuncao->setRotulo                  ( "Função"                              );
    $obCmbCodFuncao->setTitle                   ( "Selecione a função do servidor."     );
    $obCmbCodFuncao->setNull                    ( false                                 );
    $obCmbCodFuncao->setCampoId                 ( "[cod_cargo]"                         );
    $obCmbCodFuncao->setCampoDesc               ( "descricao"                           );
    $obCmbCodFuncao->addOption                  ( "", "Selecione"                       );
    $obCmbCodFuncao->obEvento->setOnChange      ( "buscaValor('preencheEspecialidadeFuncao');" );
    
    //Selecão da Especialidade Funcao
    $obTxtCodEspecialidadeFuncao = new TextBox;
    $obTxtCodEspecialidadeFuncao->setRotulo     ( "Especialidade"                          );
    $obTxtCodEspecialidadeFuncao->setName       ( "inCodEspecialidadeFuncao"               );
    $obTxtCodEspecialidadeFuncao->setTitle      ( "Selecione a especialidade do servidor." );
    $obTxtCodEspecialidadeFuncao->setSize       ( 10                                       );
    $obTxtCodEspecialidadeFuncao->setMaxLength  ( 10                                       );
    $obTxtCodEspecialidadeFuncao->setInteiro    ( true                                     );
    $obTxtCodEspecialidadeFuncao->setNull       ( true                                     );
    $obTxtCodEspecialidadeFuncao->obEvento->setOnChange( "buscaValor('preencheInformacoesSalariais');" );
    
    $obCmbCodEspecialidadeFuncao = new Select;
    $obCmbCodEspecialidadeFuncao->setName       ( "stEspecialidadeFuncao"                 );
    $obCmbCodEspecialidadeFuncao->setRotulo     ( "Especialidade"                         );
    $obCmbCodEspecialidadeFuncao->setTitle      ( "Selecione a especialidade do servidor." );
    $obCmbCodEspecialidadeFuncao->setNull       ( true                                    );
    $obCmbCodEspecialidadeFuncao->setCampoId    ( "[cod_especialidade]"                   );
    $obCmbCodEspecialidadeFuncao->setCampoDesc  ( "descricao_especialidade"               );
    $obCmbCodEspecialidadeFuncao->addOption     ( "", "Selecione"                         );
    $obCmbCodEspecialidadeFuncao->obEvento->setOnChange( "buscaValor('preencheInformacoesSalariais');" );
    
    $obDataAlteracaoFuncao = new Data;
    $obDataAlteracaoFuncao->setRotulo           ( "Data da Alteração da Função"         );
    $obDataAlteracaoFuncao->setTitle            ( "Data da alteração da função."         );
    $obDataAlteracaoFuncao->setName             ( "dtDataAlteracaoFuncao"               );
    $obDataAlteracaoFuncao->setId               ( 'dtDataAlteracaoFuncao'               );
    $obDataAlteracaoFuncao->setSize             ( 10                                    );
    $obDataAlteracaoFuncao->setMaxLength        ( 10                                    );
    $obDataAlteracaoFuncao->setNull             ( false                                 );
    $obDataAlteracaoFuncao->setInteiro          ( false                                 );
    $obDataAlteracaoFuncao->setReadOnly         ( true                                  );
    $obDataAlteracaoFuncao->setStyle            ( "color: #888888"                      );
    
    $obHdnDataAlteracaoFuncao = new Hidden;
    $obHdnDataAlteracaoFuncao->setName          ( "hdnDataAlteracaoFuncao"              );
    
    //FIM INFORMAÇÕES DA FUNÇÃO

    //-------------------------------------------
    //---------------SALARIAIS-------------------
    //-------------------------------------------
    //seleção de padrao
    $obTxtCodPadrao = new TextBox;
    $obTxtCodPadrao->setRotulo             ( "Padrão"     );
    $obTxtCodPadrao->setName               ( "inCodPadrao" );
    
    $obTxtCodPadrao->setTitle              ( "Informe o padrão." );
    $obTxtCodPadrao->setSize               ( 10    );
    $obTxtCodPadrao->setMaxLength          ( 10    );
    $obTxtCodPadrao->setInteiro            ( true );
    $obTxtCodPadrao->setNull               ( true );
    $obTxtCodPadrao->obEvento->setOnChange    ( "buscaValor('preencheProgressao');" );
    
    $obRFolhaPagamentoPadrao->listarPadraoPorContratosInativos( $rsPadrao, $boTransacao,"");
    
    $obCmbCodPadrao = new Select;
    $obCmbCodPadrao->setName                  ( "stPadrao"            );
    
    $obCmbCodPadrao->setRotulo                ( "Padrao"              );
    $obCmbCodPadrao->setTitle                 ( "Selecione o padrão." );
    $obCmbCodPadrao->setNull                  ( true                  );
    $obCmbCodPadrao->setCampoId               ( "[cod_padrao]" );
    $obCmbCodPadrao->setCampoDesc             ( "[descricao] - [valor]" );
    $obCmbCodPadrao->addOption                ( "", "Selecione"       );
    $obCmbCodPadrao->preencheCombo            ( $rsPadrao             );
    $obCmbCodPadrao->obEvento->setOnChange    ( "buscaValor('preencheProgressao');" );
    
    $obHdnProgressao =  new Hidden;
    $obHdnProgressao->setName   ( "inCodProgressao" );
    
    //Label da progressao
    $obLblProgressao = new Label;
    $obLblProgressao->setRotulo ( 'Progressão'    );
    $obLblProgressao->setName   ( 'stlblProgressao' );
    $obLblProgressao->setId     ( 'stlblProgressao' );
    
    $obTxtHorasMensais = new TextBox;
    $obTxtHorasMensais->setRotulo           ( "Horas Mensais"                            );
    $obTxtHorasMensais->setName             ( "stHorasMensais"                           );
    
    $obTxtHorasMensais->setTitle            ( "Informe a quantidade de horas mensais."   );
    $obTxtHorasMensais->setNull             ( false                                      );
    $obTxtHorasMensais->setSize             ( 6                                          );
    $obTxtHorasMensais->setMaxLength        ( 6                                          );
    $obTxtHorasMensais->setFloat            ( true                                       );
    $obTxtHorasMensais->obEvento->setOnChange      ( "buscaValor('calculaSalario');"     );
    
    $obTxtHorasSemanais = new TextBox;
    $obTxtHorasSemanais->setRotulo           ( "Horas Semanais"                           );
    $obTxtHorasSemanais->setName             ( "stHorasSemanais"                          );
    $obTxtHorasSemanais->setTitle            ( "Informe a quantidade de horas semanais."  );
    $obTxtHorasSemanais->setNull             ( false                                      );
    $obTxtHorasSemanais->setSize             ( 6                                          );
    $obTxtHorasSemanais->setMaxLength        ( 6                                          );
    $obTxtHorasSemanais->setFloat            ( true                                       );

    //Valor do salario salarial
    $obTxtSalario = new Moeda;
    $obTxtSalario->setRotulo    ( "Salário");
    $obTxtSalario->setTitle     ( "Informe o salário do servidor.");
    $obTxtSalario->setName      ( "inSalario" );
    $obTxtSalario->setMaxLength ( 14  );
    $obTxtSalario->setSize      ( 15  );
    $obTxtSalario->setNull      ( false );

    //Vigência
    $obDtVigenciaSalario = new Data;
    $obDtVigenciaSalario->setName               ( "dtVigenciaSalario"            );
    $obDtVigenciaSalario->setTitle              ("Informe a vigência do salário.");
    $obDtVigenciaSalario->setNull               ( false                          );
    $obDtVigenciaSalario->setRotulo             ( "Vigência do Salário"          );
    $obDtVigenciaSalario->obEvento->setOnChange ( "buscaValor('validarVigenciaSalario');" );
    //FIM SALARIAIS

    $inCodContrato = $request->get('inCodContrato');
    if ( empty($inCodContrato) )
        $inCodContrato = $arDados["inCodContrato"];

    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $inCodContrato );
    $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor( $rsContrato,$boTransacao );
    $dtDataProgressao = $rsContrato->getCampo("dt_inicio_progressao");
    
    $obTxtDataProgressao = new Hidden();
    $obTxtDataProgressao->setName   ( "dtDataProgressao" );
    $obTxtDataProgressao->obEvento->setOnChange( "buscaValor('preencheProgressao');");

    //Setando os valores caso for alteracao
    if (isset($arDados)) {
        $obTxtCodRegime->setValue              ( $arDados['inCodRegime']              );
        $obCmbCodRegime->setValue              ( $arDados['inCodRegime']              );
        $request->set("inCodRegime"           , $arDados['inCodRegime']);
        $request->set("inCodSubDivisao"       , $arDados['inCodSubDivisao']);
        $request->set("inCodCargo"            , $arDados['inCodCargo']);
        $request->set("inCodSubDivisaoFuncao" , $arDados['inCodSubDivisaoFuncao']);
        $request->set("inCodFuncao"           , $arDados['inCodFuncao']);
        $obTxtCodRegimeFuncao->setValue        ( $arDados['inCodRegimeFuncao']        );
        $obCmbCodRegimeFuncao->setValue        ( $arDados['inCodRegimeFuncao']        );
        $obTxtCodEspecialidadeFuncao->setValue ( $arDados['inCodEspecialidadeFuncao'] );
        $obCmbCodEspecialidadeFuncao->setValue ( $arDados['inCodEspecialidadeFuncao'] );
        $obDataAlteracaoFuncao->setValue       ( $arDados['dtDataAlteracaoFuncao']    );
        $obHdnDataAlteracaoFuncao->setValue    ( $arDados['dtDataAlteracaoFuncao']    );
        $obTxtCodPadrao->setValue              ( $arDados['inCodPadrao']              );
        $obCmbCodPadrao->setValue              ( $arDados['inCodPadrao']              );
        $obHdnProgressao->setValue             ( $arDados['inCodProgressao']          );
        $obLblProgressao->setValue             ( $arDados['stlblProgressao']          );
        $obTxtHorasMensais->setValue           ( $arDados['stHorasMensais']           );
        $obTxtHorasSemanais->setValue          ( $arDados['stHorasSemanais']          );
        $obTxtSalario->setValue                ( $arDados['inSalario']                );
        $obDtVigenciaSalario->setValue         ( $arDados['dtVigenciaSalario']        );
        $obTxtDataProgressao->setValue         ( $dtDataProgressao                    );

        Sessao::write('arDados',$arDados);
    }

    $obFormulario = new Formulario();
    $obFormulario->addTitulo            ( "Informações do Cargo"                                            );
    $obFormulario->addComponenteComposto( $obTxtCodRegime,$obCmbCodRegime                                   );
    $obFormulario->addComponenteComposto( $obTxtCodSubDivisao,$obCmbCodSubDivisao                           );
    $obFormulario->addComponenteComposto( $obTxtCargo,$obCmbCargo                                           );
    $obFormulario->addComponenteComposto( $obTxtCodEspecialidadeCargo, $obCmbCodEspecialidadeCargo          );
    $obFormulario->addTitulo            ( "Informações da Função"                                           );
    $obFormulario->addComponenteComposto( $obTxtCodRegimeFuncao,$obCmbCodRegimeFuncao                       );
    $obFormulario->addComponenteComposto( $obTxtCodSubDivisaoFuncao,$obCmbCodSubDivisaoFuncao               );
    $obFormulario->addComponenteComposto( $obTxtCodFuncao,$obCmbCodFuncao                                   );
    $obFormulario->addComponenteComposto( $obTxtCodEspecialidadeFuncao, $obCmbCodEspecialidadeFuncao        );
    $obFormulario->addComponente        ( $obDataAlteracaoFuncao                                            );
    $obFormulario->addHidden            ( $obHdnDataAlteracaoFuncao                                         );
    $obFormulario->addTitulo            ( "Informações Salariais"                                           );
    $obFormulario->addComponente        ( $obTxtHorasMensais                                                );
    $obFormulario->addComponente        ( $obTxtHorasSemanais                                               );
    $obFormulario->addComponenteComposto( $obTxtCodPadrao,$obCmbCodPadrao                                   );
    $obFormulario->addHidden            ( $obHdnProgressao                                                  );
    $obFormulario->addHidden            ( $obTxtDataProgressao                                              );
    $obFormulario->addComponente        ( $obLblProgressao                                                  );
    $obFormulario->addComponente        ( $obTxtSalario                                                     );
    $obFormulario->addComponente        ( $obDtVigenciaSalario                                              );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs = "d.getElementById('spnCargoFuncaoSalario').innerHTML = '".$stHtml."';\n";
    $stJs .= preencheSubDivisao();
    $stJs .= preencheCargo();
    $stJs .= preencheEspecialidade();

    return $stJs;
}

function carregaDados(Request $request)
{       

    $obRPessoalServidor = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();

    if ( $request->get('inCodMatricula')) 
        $inRegistro = $request->get('inCodMatricula');
    else
        $inRegistro = $request->get('inRegistro');

    $inCodContrato = SistemaLegado::pegaDado("cod_contrato","pessoal.contrato","WHERE registro = ".$inRegistro);

    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $inCodContrato );
    $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor( $rsContrato,$boTransacao );
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia($rsContrato->getCampo("cod_ocorrencia"));
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->listarOcorrencia( $rsOcorrencia,$boTransacao );
    $obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorSubDivisaoFuncao( $rsContratoServidorSubDivisaoFuncao, $boTransacao );
    $obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorRegimeFuncao( $rsContratoServidorRegimeFuncao, $boTransacao );

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    list($stDia, $stMes, $stAno) = explode("/", $rsPeriodoMovimentacao->getCampo("dt_final"));
    $stVigencia = $stAno."-".$stMes."-".$stDia;

    $rsContrato->addFormatacao('salario','NUMERIC_BR');

    $inContrato                            = $rsContrato->getCampo("cod_contrato");
    $arDados['inCodContrato']              = $rsContrato->getCampo("cod_contrato");
    $inRegistro                            = $rsContrato->getCampo("registro");
    $arDados['inRegistro']                 = $rsContrato->getCampo("registro");
    //Informações do cargo
    $arDados['inCodRegime']                = $rsContrato->getCampo("cod_regime");
    $inCodRegime                           = $rsContrato->getCampo("cod_regime");
    $arDados['inCodSubDivisao']            = $rsContrato->getCampo("cod_sub_divisao");
    $inCodSubDivisao                       = $rsContrato->getCampo("cod_sub_divisao");
    $arDados['inCodCargo']                 = $rsContrato->getCampo("cod_cargo");
    $inCodCargo                            = $rsContrato->getCampo("cod_cargo");
    $arDados['inCodEspecialidadeCargo']    = $rsContrato->getCampo("cod_especialidade_cargo");
    $inCodEspecialidadeCargo               = $rsContrato->getCampo("cod_especialidade_cargo");
    //Informações da função
    $arDados['inCodRegimeFuncao']          = $rsContratoServidorRegimeFuncao->getCampo("cod_regime");
    $inCodRegimeFuncao                     = $rsContratoServidorRegimeFuncao->getCampo("cod_regime");
    $arDados['inCodSubDivisaoFuncao']      = $rsContratoServidorSubDivisaoFuncao->getCampo("cod_sub_divisao");
    $inCodSubDivisaoFuncao                 = $rsContratoServidorSubDivisaoFuncao->getCampo("cod_sub_divisao");
    $arDados['inCodFuncao']                = $rsContrato->getCampo("cod_funcao");
    $arDados['inCodEspecialidadeFuncao']   = ($rsContrato->getCampo("cod_especialidade_funcao") != "") ? $rsContrato->getCampo("cod_especialidade_funcao") : $rsContrato->getCampo("cod_especialidade_cargo");
    $inCodEspecialidadeFuncao              = ($rsContrato->getCampo("cod_especialidade_funcao") != "") ? $rsContrato->getCampo("cod_especialidade_funcao") : $rsContrato->getCampo("cod_especialidade_cargo");
    $arDados['dtDataAlteracaoFuncao']      = $rsContrato->getCampo("ultima_vigencia");
        
    //Informações salariais
    $rsContrato->addFormatacao("horas_mensais", "NUMERIC_BR");
    $rsContrato->addFormatacao("horas_semanais", "NUMERIC_BR");
    $arDados['stHorasMensais']             = $rsContrato->getCampo("horas_mensais");
    $stHorasMensais                        = $rsContrato->getCampo("horas_mensais");
    $arDados['stHorasSemanais']            = $rsContrato->getCampo("horas_semanais");
    $arDados['dtVigenciaSalario']          = $rsContrato->getCampo("vigencia");
    Sessao::write('dtVigenciaSalario',$dtVigenciaSalario);
    $arDados['dtDataProgressao']           = $rsContrato->getCampo("dt_inicio_progressao");
    $dtDataProgressao                      = $rsContrato->getCampo("dt_inicio_progressao");
    $arDados['inCodPadrao']                = $rsContrato->getCampo("cod_padrao");
    $inCodPadrao                           = $rsContrato->getCampo("cod_padrao");
    $arDados['inCodProgressao']            = $rsContrato->getCampo("cod_nivel_padrao");
    $arDados['inSalario']                  = $rsContrato->getCampo("salario");
    
    return $arDados;

}

function gerarSpanLicencaPremio(Request $request)
{
    $stHtml = "";
    $stEval = "";
    $rsAssentamento  = new RecordSet();
    $rsClassificacao = new RecordSet();
    $inCodClassificacao = $request->get('inCodClassificacao');
    $inCodClassificacao = ( !empty($inCodClassificacao) ) ? $inCodClassificacao : Sessao::read('inCodClassificacao');

    $inCodAssentamento = $request->get('inCodAssentamento');

    if ( !empty($inCodAssentamento) ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
        $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
        $stFiltro  = " AND assentamento_assentamento.cod_assentamento = ".$inCodAssentamento;
        $stFiltro .= " AND assentamento_assentamento.cod_motivo = 9";
        $obTPessoalAssentamentoAssentamento->recuperaAssentamento($rsAssentamento,$stFiltro);
    }

    if ( !empty($inCodClassificacao) ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php");
        $obTPessoalClassificacaoAssentamento = new TPessoalClassificacaoAssentamento();
        $stFiltro  = " AND ca.cod_classificacao = ".$inCodClassificacao;
        $stFiltro .= " AND ca.cod_tipo = 2";
        $obTPessoalClassificacaoAssentamento->recuperaRelacionamento($rsClassificacao,$stFiltro);        
    }

    if ($rsAssentamento->getNumLinhas() == 1 AND $rsClassificacao->getNumLinhas() == 1) {
        $obDtInicial = new Data();
        $obDtInicial->setRotulo("Período Aquisitivo Licença Prêmio");
        $obDtInicial->setName("dtInicial");
        $obDtInicial->setTitle("Informe o período aquisitivo da licença prêmio, ver relatório controle de licenças prêmio.");
        $obDtInicial->setNull(false);
        $obDtInicial->setValue($request->get("dtInicial"));

        $obLabelAte = new Label;
        $obLabelAte->setRotulo ( "Período Aquisitivo Licença Prêmio" );
        $obLabelAte->setValue  ( "até"                               );
        $obLabelAte->setTitle  ( "Informe o período aquisitivo da licença prêmio, ver relatório controle de licenças prêmio." );

        $obDtFinal = new Data();
        $obDtFinal->setRotulo("Período Aquisitivo Licença Prêmio");
        $obDtFinal->setName("dtFinal");
        $obDtFinal->setTitle("Informe o período aquisitivo da licença prêmio, ver relatório controle de licenças prêmio.");
        $obDtFinal->setNull(false);
        $obDtFinal->setValue($request->get("dtFinal"));

        $obFormulario = new Formulario();
        $obFormulario->agrupaComponentes( array($obDtInicial, $obLabelAte, $obDtFinal) );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }

    $stJs  = "d.getElementById('spnLicencaPremio').innerHTML = '".$stHtml."';\n";
    $stJs .= "LiberaFrames();\n";

    return $stJs;
}

function MontaNorma($stSelecionado = "")
{
    global $request;

    $stCombo  = "inCodNorma";
    $stFiltro = "inCodTipoNorma";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
    $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";
    $inCodTipoNorma = $request->get($stFiltro, '');
    $inCodTipoNorma = (trim($inCodTipoNorma) != "") ? trim($inCodTipoNorma) : Sessao::read("inCodTipoNorma");
    if ($inCodTipoNorma != "") {
        include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );
        $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
        $obRFolhaPagamentoPadrao->obRNorma->listar( $rsCombo );
        $inCount = 0;

        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("cod_norma");
            $stDesc = $rsCombo->getCampo("nom_norma");
            $stNumNormaExercicio = $rsCombo->getCampo("num_norma_exercicio");
            if( $stSelecionado == $inId )
                $stSelected = 'selected';
            else
                $stSelected = '';
            $stJs .= "f.".$stCombo.".options[".$inCount."] = new Option('".$stNumNormaExercicio." - ".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }

    return $stJs;
}

function incluirNorma()
{
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php";

    $obErro = new Erro();
    $obTNorma = new TNorma();
    $obTTipoNorma = new TTipoNorma();
    $obTNormaDataTermino = new TNormaDataTermino();

    global $request;

    if ($request->get('hdnCodTipoNorma', '') == "" || $request->get('stCodNorma', '') == "") {
        $obErro->setDescricao("Informe o Tipo de Norma e a Norma!");
    } else {
        $arCodNorma = explode("/",$request->get("stCodNorma"));
        $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$request->get('hdnCodTipoNorma');

        $stFiltroNorma  = " WHERE cod_tipo_norma = ".$request->get('hdnCodTipoNorma');
        $stFiltroNorma .= "   AND num_norma = '".(int) $arCodNorma[0]."'";

        $obTNorma->recuperaNormas($rsRecordSetNorma, $stFiltroNorma);
        $obTTipoNorma->recuperaTodos($rsRecordSetTipoNorma, $stFiltroTipoNorma);

        $stFiltroDataTermino = " WHERE cod_norma = ".$rsRecordSetNorma->getCampo('cod_norma');
        $obTNormaDataTermino->recuperaTodos($rsRecordSetDataTermino, $stFiltroDataTermino);
        $arNormas = Sessao::read('arNormas');

        $arNorma = array();
        $arNorma['stNomTipoNorma']          =   $rsRecordSetTipoNorma->getCampo('nom_tipo_norma');
        $arNorma['stNorma']                 =   $rsRecordSetNorma->getCampo('num_norma_exercicio')." - ".$rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['dtAssinatura']            =   $rsRecordSetNorma->getCampo('dt_assinatura_formatado');
        $arNorma['dtTermino']               =   $rsRecordSetDataTermino->getCampo('dt_termino');
        $arNorma['dtPublicacao']            =   $rsRecordSetNorma->getCampo('dt_publicacao');
        $arNorma['inCodNorma']              =   $rsRecordSetNorma->getCampo('cod_norma');
        $arNorma['inCodTipoNorma']          =   $rsRecordSetNorma->getCampo('cod_tipo_norma');
        $arNorma['stNomNorma']              =   $rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['stDescricao']             =   $rsRecordSetNorma->getCampo('descricao');
        $arNorma['stExercicio']             =   $rsRecordSetNorma->getCampo('exercicio');
        $arNorma['inNumNorma']              =   $rsRecordSetNorma->getCampo('num_norma');
        $arNorma['inId']                    =   count($arNormas);

        if ($arNormas != "") {
            foreach ($arNormas as $arrNorma) {
                if ($arrNorma['stTipoNorma'] == $arNorma['stTipoNorma'] && $arrNorma['stNorma'] == $arNorma['stNorma']) {
                    $obErro->setDescricao("Esta norma já está na lista!");
                }
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arNormas[] = $arNorma;
        Sessao::write('arNormas',$arNormas);
        $stJs .= montaListaNorma();
    }

    $stJs .= "f.hdnCodTipoNorma.value               = '';\n";
    $stJs .= "f.stCodNorma.value                    = '';\n";
    $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";

    return $stJs;
}

function buscaNormas()
{
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php";

    $obErro = new Erro();
    $obTNorma = new TNorma();
    $obTTipoNorma = new TTipoNorma();
    $obTNormaDataTermino = new TNormaDataTermino();

    $arCodNormas = Sessao::read('arCodNorma');

    if ($arCodNormas != "") {
        foreach ($arCodNormas as $norma) {
            if ($norma['inCodTipoNorma'] == "" || $norma['inCodNorma'] == "") {
                $obErro->setDescricao("Informe o Tipo de Norma e a Norma!");
        }

        $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$norma['inCodTipoNorma'];

        $stFiltroNorma  = " WHERE cod_tipo_norma = ".$norma['inCodTipoNorma'];
        $stFiltroNorma .= "   AND cod_norma = ".$norma['inCodNorma'];

        $stFiltroDataTermino = " WHERE cod_norma = ".$norma['inCodNorma'];

        $obTNorma->recuperaNormas($rsRecordSetNorma, $stFiltroNorma);
        $obTTipoNorma->recuperaTodos($rsRecordSetTipoNorma, $stFiltroTipoNorma);
        $obTNormaDataTermino->recuperaTodos($rsRecordSetDataTermino, $stFiltroDataTermino);

        $arNorma = array();
        $arNorma['stNomTipoNorma']          =   $rsRecordSetTipoNorma->getCampo('nom_tipo_norma');
        $arNorma['stNorma']                 =   $rsRecordSetNorma->getCampo('num_norma_exercicio')." - ".$rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['dtAssinatura']            =   $rsRecordSetNorma->getCampo('dt_assinatura_formatado');
        $arNorma['dtTermino']               =   $rsRecordSetDataTermino->getCampo('dt_termino');
        $arNorma['dtPublicacao']            =   $rsRecordSetNorma->getCampo('dt_publicacao');
        $arNorma['inCodNorma']              =   $rsRecordSetNorma->getCampo('cod_norma');
        $arNorma['inCodTipoNorma']          =   $rsRecordSetNorma->getCampo('cod_tipo_norma');
        $arNorma['stNomNorma']              =   $rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['stDescricao']             =   $rsRecordSetNorma->getCampo('descricao');
        $arNorma['stExercicio']             =   $rsRecordSetNorma->getCampo('exercicio');
        $arNorma['inNumNorma']              =   $rsRecordSetNorma->getCampo('num_norma');
        $arNorma['inId']                    =   count($arNormas);

        $arNormas[] = $arNorma;
    }
        Sessao::write('arNormas',$arNormas);
    }
}

function montaListaNorma()
{
    global $request;

    $stAcao = $request->get('stAcao');

    $rsRecordSet = new RecordSet();
    if (Sessao::read('arNormas') != "") {
        $rsRecordSet->preenche(Sessao::read('arNormas'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Normas/Fundamentação Legal" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo Norma" );
    $obLista->ultimoCabecalho->setWidth( 17 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Norma" );
    $obLista->ultimoCabecalho->setWidth( 37 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Assinatura" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Publicação" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Término" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    if ($stAcao == 'alterar' || $stAcao == 'incluir') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 4 );
        $obLista->commitCabecalho();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomTipoNorma" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNorma" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtAssinatura" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtPublicacao" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtTermino" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    if ($stAcao == 'alterar' || $stAcao == 'incluir') {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirNorma');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnFundamentacaoLegal').innerHTML = '".$stHtml."';";

    return $stJs;
}

function excluirNorma($inId)
{
    $arTemp       = array();
    $arNormas     = Sessao::read('arNormas');

    foreach ($arNormas as $arNorma) {
        if ($arNorma['inId'] != $inId) {
            $arTemp[] = $arNorma;
        }
    }
    $arNormas = $arTemp;
    Sessao::write('arNormas', $arNormas);
    $stJs .= montaListaNorma();

    return $stJs;
}

function preencheClassificacao($inCod, $comboType)
{
    //Para montar o combo Classificao
    $obRPessoalClassificacaoAssentamento = new RPessoalClassificacaoAssentamento();
    $obRPessoalClassificacaoAssentamento->listarClassificacaoGeracaoAssentamento( $rsClassificacao, $inCod, $comboType );

    $dado    = null;
    for ($i=0; $i < count($rsClassificacao->arElementos); $i++ ) {
        $dado[$i]['cod_classificacao'] =  $rsClassificacao->arElementos[$i]['cod_classificacao'];
        $dado[$i]['descricao'] = $rsClassificacao->arElementos[$i]['descricao'];
        $dado[$i]['cod_tipo'] = $rsClassificacao->arElementos[$i]['cod_tipo'];
        $dado[$i]['descricao_tipo'] = $rsClassificacao->arElementos[$i]['descricao_tipo'];
    }

    return json_encode($dado);
}

function preencheSubDivisao()
{
    global $request;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get("inCodContrato") );
    $js .= "limpaSelect(f.stSubDivisao,0); \n";
    $js .= "f.stSubDivisao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodSubDivisao.value = ''; \n";

    $js .= "f.inCodCargo.value = ''; \n";
    $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);\n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodSubDivisaoFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";

    if ($request->get("inCodRegime")) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->roPessoalRegime->setCodRegime( $request->get('inCodRegime') );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            $arAcao = explode("_",$request->get('stAcao'));
            if ($inCodSubDivisao == $request->get("inCodSubDivisao")) {
                $stSelected = "selected";
                $js .= "f.inCodSubDivisao.value = '".$request->get("inCodSubDivisao")."'; \n";
            } else {
                $stSelected = "";
            }
            $js .= "f.stSubDivisao.options[$inContador] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $request->set("inCodRegimeFuncao", $request->get('inCodRegime'));
        $js .= preencheSubDivisaoFuncao();
    }
    $stJs .= $js;

    return $stJs;
}

function preencheSubDivisaoFuncao()
{
    global $request;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get("inCodContrato") );

    $js .= "f.inCodSubDivisaoFuncao.value = '';                                                 \n";
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);                                                \n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');                   \n";
    $js .= "f.inCodFuncao.value = '';                                                           \n";
    $js .= "limpaSelect(f.stFuncao,0);                                                          \n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');                             \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";

    $stFiltro = " AND pr.cod_regime = ".$request->get("inCodRegimeFuncao");
    if ($request->get("inCodRegimeFuncao")) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $stSelected       = "";
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            $arAcao = explode("_",$request->get('stAcao'));
            if ($inCodSubDivisao == $request->get("inCodSubDivisaoFuncao")) {
                $stSelected = "selected";
                $js .= "f.inCodSubDivisaoFuncao.value = '".$request->get("inCodSubDivisaoFuncao")."'; \n";
            }
            $js .= "f.stSubDivisaoFuncao.options[".$inContador."] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
    }
    $stJs .= limpaInformacoesSalariais();
    $stJs .= $js;
       
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
    $stJs .= "f.dtDataAlteracaoFuncao.readOnly = false;                                 \n";
    $stJs .= "f.dtDataAlteracaoFuncao.style.color = '#000000';                          \n";
    $stJs .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';  \n";

    return $stJs;
}

function preencheCargo()
{
    global $request;

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get("inCodContrato") );
    $js .= "f.inCodCargo.value = ''; \n";
    $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função
    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    if ($request->get("inCodSubDivisao")) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($request->get('inCodSubDivisao'));

        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo);

        $arAcao = explode("_",$request->get('stAcao'));
        if ($arAcao[0] != "alterar") {
            $js .= "f.inCodCargo.value = ''; \n";
            $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
            $js .= "f.stCargo[0] = new Option('Selecione','','selected');\n";
        }
        $js .= "f.inCodEspecialidadeCargo.value = '';\n";
        $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        while ( !$rsCargo->eof() ) {
            $inCodCargo = $rsCargo->getCampo( "cod_cargo" );
            $stCargo    = $rsCargo->getCampo( "descricao" );
            $arAcao = explode("_",$request->get('stAcao'));
            if ($inCodCargo == $request->get("inCodCargo")) {
                $stSelected = "selected";
                $js .= "f.inCodCargo.value = '".$request->get("inCodCargo")."'; \n";
            } else {
                $stSelected = "";
            }
            $js .= "f.stCargo.options[".$inContador."] = new Option('".$stCargo."','".$inCodCargo."','".$stSelected."'); \n";
            $inContador++;
            $rsCargo->proximo();
        }
        $request->set("inCodSubDivisaoFuncao", $request->get('inCodSubDivisao'));
        $js .= preencheFuncao();
    }
    $stJs .= $js;

    return $stJs;
}

function preencheFuncao()
{
    global $request;

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get("inRegistro") );
    $js .= "f.inCodFuncao.value = '';\n";
    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','','selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0); \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";

    if ($request->get("inCodSubDivisaoFuncao")) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($request->get('inCodSubDivisaoFuncao'));
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao);        
        $js .= "f.inCodFuncao.value = '';\n";
        $js .= "limpaSelect(f.stFuncao,0);\n";
        $js .= "f.stFuncao[0] = new Option('Selecione','','selected');\n";
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
        $js .= "limpaSelect(f.stEspecialidadeFuncao,0); f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        while ( !$rsFuncao->eof() ) {
            $inCodFuncao = $rsFuncao->getCampo( "cod_cargo" );
            $stFuncao    = $rsFuncao->getCampo( "descricao" );
            if ($inCodFuncao == $request->get("inCodFuncao")) {
                $stSelected = "selected";
                $js .= "f.inCodFuncao.value = '".$request->get("inCodFuncao")."'; \n";
            } else {
                $stSelected = "";
            }
            $js .= "f.stFuncao.options[".$inContador."] = new Option('".$stFuncao."','".$inCodFuncao."','".$stSelected."'); \n";
            $inContador++;
            $rsFuncao->proximo();
        }
    }
    $js .= limpaInformacoesSalariais();
    $js .= preencheEspecialidadeFuncao();
    $stJs .= $js;
    if ($request->get('stAcao') == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';  \n";
    }

    return $stJs;
}

function preencheEspecialidade()
{
    global $request;

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();

    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";

    $js .= limpaInformacoesSalariais();

    if ($request->get("inCodCargo")) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $request->get("inCodSubDivisao") );
        $inCodCargo = ($request->get("inCodCargo",'')<>'') ? $request->get("inCodCargo") : $request->get("inHdnCodCargo");
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodCargo );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->consultaCargoPadrao( $rsPadrao );
        $rsPadrao->addFormatacao( "valor", 'NUMERIC_BR' );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );

        $js .= "f.inCodPadrao.value = '".$rsPadrao->getCampo('cod_padrao')."'; \n";
        $js .= "f.stPadrao.value = '".$rsPadrao->getCampo('cod_padrao')."'; \n";
        $js .= "f.stHorasMensais.value = '".$rsPadrao->getCampo('horas_mensais')."'; \n";
        $js .= "f.stHorasSemanais.value = '".$rsPadrao->getCampo('horas_semanais')."'; \n";
        $js .= "limpaSelect(f.stEspecialidadeCargo,0); \n";
        $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
        $js .= "f.stEspecialidadeCargo[0] = new Option('Selecione','','selected');\n";
        $js .= "limpaSelect(f.stEspecialidadeFuncao,0); \n";
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
        $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','','selected');\n";
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            $js .= "f.stEspecialidadeCargo.options[".$inContador."] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $js .= "f.stEspecialidadeFuncao.options[".$inContador."] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
        $js .= preencheInformacoesSalariais( $request->get("inCodCargo") );
    }
    if ($request->get('stAcao') == "alterar") {
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';\n";
    }

    return $js;
}

function preencheEspecialidadeFuncao()
{
    global $request;

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";

    if ($request->get("inCodFuncao")) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $request->get("inCodSubDivisaoFuncao") );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $request->get("inCodFuncao") );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            $js .= "f.stEspecialidadeFuncao.options[".$inContador."] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
    }
    if ($request->get('stAcao') == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';\n";
    }

    $js .= preencheInformacoesSalariais();

    if ($request->get('stAcao') == "alterar") {
        $js .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';  \n";
    }

    return $js;
}

function preenchePreEspecialidadeFuncao()
{
    global $request;

    if ($request->get('inCodFuncao') == $request->get('inCodCargo')) {
        $stJs .= "f.inCodEspecialidadeFuncao.value = f.inCodEspecialidadeCargo.value; \n";
        $stJs .= "f.stEspecialidadeFuncao.value    = f.inCodEspecialidadeCargo.value; \n";
    }
    $stJs .= preencheInformacoesSalariais("", $request->get('inCodEspecialidadeCargo'), "");

    return $stJs;
}

function preencheInformacoesSalariais($inCodFuncao = "", $inCodEspecialidadeFuncao = "", $stDataProgressao = "")
{
    include_once CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php";

    global $request;

    $inCodFuncao              = $inCodFuncao              ? $inCodFuncao              : $request->get("stFuncao");
    $inCodEspecialidadeFuncao = $inCodEspecialidadeFuncao ? $inCodEspecialidadeFuncao : $request->get("stEspecialidadeFuncao");
    $stDataProgressao         = $stDataProgressao         ? $stDataProgressao         : $request->get("dtDataProgressao");
    $stHorasMensais           = "";
    $stHorasSemanais          = "";
    $inCodPadrao              = "";
    $inCodProgressao          = "";
    $nuSalario                = "";

    $js = limpaInformacoesSalariais();

    //Se posssui CodFuncao pode-se buscar pelo padrão e calcular o salário.
    if ($inCodFuncao) {
        $obRPessoalCargo = new RPessoalCargo;

        // Para ver se o cargo possui especialidades
        $obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalCargo->setCodCargo( $inCodFuncao );
        $obRPessoalCargo->addEspecialidade();
        $obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidades );

        //Se Cargo da função tem especialidade
        if ( $rsEspecialidades->getNumLinhas() > 0 ) {
            //Se está setado o cod da especialidade
            if ($inCodEspecialidadeFuncao) {
                $obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $inCodEspecialidadeFuncao );
                $obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsPadraoEspecialidade );
                $rsPadraoEspecialidade->addFormatacao( "horas_mensais" , 'NUMERIC_BR' );
                $rsPadraoEspecialidade->addFormatacao( "horas_semanais", 'NUMERIC_BR' );
                $stHorasMensais           = $rsPadraoEspecialidade->getCampo("horas_mensais");
                $stHorasSemanais          = $rsPadraoEspecialidade->getCampo("horas_semanais");
                $inCodPadrao              = $rsPadraoEspecialidade->getCampo("cod_padrao");
            }
        } else {
            //Cargo da função não tem especialidade
            $obRPessoalCargo->consultaCargoPadrao( $rsPadraoCargo, $boTransacao );                                    
            $rsPadraoCargo->addFormatacao( "horas_mensais" , 'NUMERIC_BR' );
            $rsPadraoCargo->addFormatacao( "horas_semanais", 'NUMERIC_BR' );
            $stHorasMensais           = $rsPadraoCargo->getCampo("horas_mensais");
            $stHorasSemanais          = $rsPadraoCargo->getCampo("horas_semanais");
            $inCodPadrao              = $rsPadraoCargo->getCampo("cod_padrao");
        }

        //Este if garante que se o cargo tem especialidade o código dele esteja setado ou não tenha especialidade
        if ( !( $rsEspecialidades->getNumLinhas() > 0) || ( ($rsEspecialidades->getNumLinhas() > 0) && $inCodEspecialidadeFuncao ) ) {
            //O valor aqui setado será usado na função calculaSalario,
            // quando não for passado nenhum parametro de horas mensais a ela            
            $request->set("stHorasMensais", $stHorasMensais);
            $js .= preencheProgressao($inCodPadrao);

            $js .= "f.stHorasMensais.value = '".$stHorasMensais."'; \n";
            $js .= "f.stHorasSemanais.value = '".$stHorasSemanais."'; \n";
            $js .= "f.inCodPadrao.value = '".$inCodPadrao."'; \n";
            $js .= "f.stPadrao.value = '".$inCodPadrao."'; \n";
        }
    }
    if ($request->get('stAcao') == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $js .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';  \n";
    }

    return $js;
}


function preencheProgressao($inCodPadrao)
{
    include_once CAM_GRH_PES_MAPEAMENTO.'FRecuperaQuantidadeMesesProgressaoAfastamento.class.php';
    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php";

    global $request;

    $inCodProgressao  = "";
    $stLblProgressao  = "&nbsp;";
    $stDataProgressao = $request->get('dtDataProgressao');
    if ($inCodPadrao != "" and $stDataProgressao != "") {
        //calcula diferença de meses entre datas
        $stDataProgressao    = explode('/',$stDataProgressao);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        $dtDataAtual = explode('/',$rsPeriodoMovimentacao->getCampo("dt_final"));

        $inCodContrato = $request->get('inCodContrato');

        if ( is_null($inCodContrato) ){
            $inCodContrato = 0;
        }

        $rsQtdMeses = new RecordSet;
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento = new FPessoalRecuperaQuantidadeMesesProgressaoAfastamento;
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('cod_contrato', $inCodContrato);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_inicial', $stDataProgressao[2]."-".$stDataProgressao[1]."-".$stDataProgressao[0]);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_final', $dtDataAtual[2]."-".$dtDataAtual[1]."-".$dtDataAtual[0]);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->recuperaMesesProgressaoAfastamento($rsQtdMeses);

        $arQtdMeses = $rsQtdMeses->arElementos;
        //Lista as progressões, a última progressão do rsProgressao é a progressão do padrão para esta data de início de progressão
        $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
        $obRFolhaPagamentoPadrao->addNivelPadrao();
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses( $arQtdMeses[0]['retorno'] );
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
        $rsProgressao->setUltimoElemento();
        if ( $rsProgressao->getNumLinhas() > 0 ) {
            $stLblProgressao = $rsProgressao->getCampo('cod_nivel_padrao')." - ".$rsProgressao->getCampo('descricao');
            $inCodProgressao = $rsProgressao->getCampo('cod_nivel_padrao');
        }
    }
    $stJs .= calculaSalario( $inCodPadrao, $inCodProgressao );

    $stJs .= "d.getElementById('stlblProgressao').innerHTML = '".$stLblProgressao."'; \n";
    $stJs .= "f.inCodProgressao.value = '".$inCodProgressao."'; \n";

    return $stJs;
}

function calculaSalario($inCodPadrao = "", $inCodProgressao = "", $inHorasMensais = "")
{
    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php";

    global $request;

    //O valor do $_REQUEST["stHorasMensais"] é setado na função preencheInformacoesSalariais
    $inHorasMensais = $inHorasMensais != ""   ? $inHorasMensais   : $request->get("stHorasMensais");
    //Para quando o calculaSalario é chamado sem ter passado pelo preencheInformacoesSalariais
    $inHorasMensais = $inHorasMensais   ? $inHorasMensais   : $request->get("stHorasMensais");
    $nuSalario = "";

    if ($inCodPadrao != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodo);

        $obTFolhaPagamentoPadrao = new TFolhaPagamentoPadrao();
        $stFiltro = " AND FPP.cod_padrao = ".$inCodPadrao;
        $stFiltro .= " AND FPP.vigencia <= to_date('".$rsPeriodo->getCampo("dt_final")."','dd/mm/yyyy')";
        $obTFolhaPagamentoPadrao->recuperaRelacionamento($rsPadrao,$stFiltro);

        $inHorasMensaisPadrao = ($rsPadrao->getCampo('horas_mensais') > 0.00) ? $rsPadrao->getCampo('horas_mensais') : 1;
        if ($inCodProgressao != "") {
            $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
            $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
            $obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $inCodProgressao);
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
            $nuSalarioPadrao      = $rsProgressao->getCampo('valor');
            $nuSalarioHoraPadrao  = $nuSalarioPadrao / $inHorasMensaisPadrao;
            $nuSalario            = $nuSalarioHoraPadrao * $inHorasMensais;
        } else {
            $nuSalarioPadrao      = $rsPadrao->getCampo('valor');
            $nuSalarioHoraPadrao  = $nuSalarioPadrao / $inHorasMensaisPadrao;
            $nuSalario            = $nuSalarioHoraPadrao * $inHorasMensais;
        }
    }

    if ($nuSalario != '') {
        $nuSalario = number_format($nuSalario, 2, ',','.');
        $stJs .= "f.inSalario.value = '".$nuSalario."'; \n";

        return $stJs;
    }
}

function validarVigenciaSalario()
{
    global $request;

    $stValida = comparaComDataNascimento("dtVigenciaSalario","Vigência do Salário");
    if ($stValida != "") {
        $stJs .= $stValida;
    } else {
        if ( sistemaLegado::comparaDatas(Sessao::read('dtVigenciaSalario'),$request->get('dtVigenciaSalario')) ) {
            $stMensagem = "A vigência deve ser posterior a ".Sessao::read('dtVigenciaSalario');
            $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');       \n";
            $stJs .= "f.dtVigenciaSalario.value = '".Sessao::read('dtVigenciaSalario')."';";
        }
    }

    return $stJs;
}

function limpaInformacoesSalariais()
{
    $js .= "f.inCodPadrao.value = '';      \n";
    $js .= "f.stPadrao.value = '';         \n";
    $js .= "f.stHorasMensais.value = '';   \n";
    $js .= "f.stHorasSemanais.value = '';  \n";
    $js .= "f.inSalario.value = '';        \n";
    $js .= "d.getElementById('stlblProgressao').innerHTML = '&nbsp;'; \n";
    $js .= "f.inCodProgressao.value = ''; \n";

    return $js;
}


function preencheSubDivisaoAlterar()
{
    global $request;

    $arDados = Sessao::read('arDados');
    $inCodRegime = $arDados['inCodRegime'];
    $inCodSubDivisao = $arDados['inCodSubDivisao'];
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get('inContrato') );
    $stFiltro = " AND pr.cod_regime = ".$inCodRegime;        

    if ($inCodRegime) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            if ($inCodSubDivisao == $rsSubDivisao->getCampo( "cod_sub_divisao" )) {
                $stSelected = "selected";
                $stJs .= "f.inCodSubDivisao.value = '".$inCodSubDivisao."'; \n";
            } else {
                $stSelected = "";
            }
            $stJs .= "f.stSubDivisao.options[".$inContador."] = new Option('".$rsSubDivisao->getCampo( "nom_sub_divisao" )."','".$rsSubDivisao->getCampo( "cod_sub_divisao" )."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $stJs .= "f.stSubDivisao.value = '".$inCodSubDivisao."'; \n";
    }

    return $stJs;
}

function preencheCargoAlterar()
{
    global $request;

    $arDados = Sessao::read('arDados');
    $inCodSubDivisao = $arDados['inCodSubDivisao'];
    $inCodCargo = $arDados['inCodCargo'];

    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get("inContrato") );
    if ($inCodSubDivisao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($inCodSubDivisao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodCargo);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(true);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo);
        $inContador = 1;
        $boDesbloqueia = false;
        while ( !$rsCargo->eof() ) {
            if ($inCodCargo == $rsCargo->getCampo( "cod_cargo" )) {
                $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
                $boComparaDatas = SistemaLegado::comparaDatas($rsUltimaMovimentacao->getCampo('dt_final'), $rsCargo->getCampo('dt_publicacao'), true);

                if ($boComparaDatas === false || ($rsCargo->getCampo('dt_termino') !== null && $rsCargo->getCampo('dt_termino') >= $rsUltimaMovimentacao->getCampo('dt_inicial'))) {
                    $boDesbloqueia = true;
                }

                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $stJs .= "f.stCargo.options[".$inContador."] = new Option('".$rsCargo->getCampo( "descricao" )."','".$rsCargo->getCampo( "cod_cargo" )."','".$stSelected."'); \n";
            $inContador++;
            $rsCargo->proximo();
        }
        $stJs .= "f.inCodCargo.value = '".$inCodCargo."'; \n";
        $stJs .= "f.stCargo.value = '".$inCodCargo."'; \n";

    }

    return $stJs;
}

function preencheEspecialidadeAlterar()
{
    $arDados = Sessao::read('arDados');
    $inCodSubDivisao = $arDados['inCodSubDivisao'];
    $inCodCargo = $arDados['inCodCargo'];
    $inCodEspecialidadeCargo = $arDados['inCodEspecialidadeCargo'];

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    if ($inCodSubDivisao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisao );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodCargo );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            if ($inCodEspecialidade == $inCodEspecialidadeCargo) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $js .= "f.stEspecialidadeCargo.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."','".$stSelected."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
    } else {
        sistemaLegado::exibeAviso("Deve ser selecionada uma subdivisão."," "," ");
    }
    $stJs .= $js;

    return $stJs;
}

function preencheSubDivisaoFuncaoAlterar()
{
    global $request;

    $arDados = Sessao::read('arDados');
    $inCodRegimeFuncao = $arDados['inCodRegimeFuncao'];
    $inCodSubDivisaoFuncao = $arDados['inCodSubDivisaoFuncao'];
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get("inContrato") );

    $js .= "f.inCodSubDivisaoFuncao.value = '';                                                 \n";
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);                                                \n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');                   \n";
    $js .= "f.inCodFuncao.value = '';                                                           \n";
    $js .= "limpaSelect(f.stFuncao,0);                                                          \n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');                             \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";
    $stFiltro = " AND pr.cod_regime = ".$inCodRegimeFuncao;
    if ($inCodRegimeFuncao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            if ($inCodSubDivisao == $inCodSubDivisaoFuncao) {
                $stSelected = "selected";
                $js .= "f.inCodSubDivisaoFuncao.value = '".$inCodSubDivisaoFuncao."'; \n";

            } else {
                $stSelected = "";
            }
            $js .= "f.stSubDivisaoFuncao.options[".$inContador."] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $js .= "f.stSubDivisaoFuncao.value = '".$inCodSubDivisaoFuncao."'; \n";
    }
    $stJs .= $js;

    return $stJs;
}

function preencheFuncaoAlterar()
{
    global $request;

    $arDados = Sessao::read('arDados');    
    $inCodSubDivisaoFuncao = $arDados['inCodSubDivisaoFuncao'];
    $inCodFuncao  = $arDados['inCodFuncao'];

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get("inContrato") );
    if ($inCodSubDivisaoFuncao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($inCodSubDivisaoFuncao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodFuncao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(true);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao);
        $inContador = 1;
        while ( !$rsFuncao->eof() ) {
            if ($inCodFuncao == $rsFuncao->getCampo( "cod_cargo" )) {
                $stSelectedFuncao = "selected";
            } else {
                $stSelectedFuncao = "";
            }
            $stJs .= "f.stFuncao.options[".$inContador."] = new Option('".$rsFuncao->getCampo( "descricao" )."','".$rsFuncao->getCampo( "cod_cargo" )."','".$stSelectedFuncao."'); \n";
            $inContador++;
            $rsFuncao->proximo();
        }
        $stJs .= "f.inCodFuncao.value = '".$inCodFuncao."'; \n";
        $stJs .= "f.stFuncao.value = '".$inCodFuncao."'; \n";
    }

    return $stJs;
}

function preencheEspecialidadeFuncaoAlterar()
{
    $arDados = Sessao::read('arDados');
    $inCodFuncao  = $arDados['inCodFuncao'];
    $inCodSubDivisaoFuncao = $arDados['inCodSubDivisaoFuncao'];
    $inCodEspecialidadeFuncao = $arDados['inCodEspecialidadeFuncao'];

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisaoFuncao );
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodFuncao );
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
    $inContador = 1;
    if ( $rsEspecialidade->getNumLinhas() > 0 ) {
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            if ($inCodEspecialidade == $inCodEspecialidadeFuncao) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $js .= "f.stEspecialidadeFuncao.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."','".$stSelected."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
        $js .= "f.inCodEspecialidadeFuncao.value = '".$inCodEspecialidadeFuncao."'; \n";
    } else {
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    }
    $stJs .= $js;

    return $stJs;
}

function preencheProgressaoAlterar()
{
    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php";

    global $request;

    $arDados = Sessao::read('arDados');
    $dtDataProgressao = $arDados['dtDataProgressao'];
    $inCodPadrao      = $arDados['inCodPadrao'];

    $inCodProgressao  = "";
    $stLblProgressao  = "&nbsp;";
    $stDataProgressao = ( $stDataProgressao != "" ) ? $stDataProgressao : $dtDataProgressao;
    if ($inCodPadrao && $stDataProgressao) {
        //calcula diferença de meses entre datas -- INICIO
        $stDataProgressao = explode('/',$stDataProgressao);

        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO.'FRecuperaQuantidadeMesesProgressaoAfastamento.class.php';        
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        $dtDataAtual = explode('/',$rsPeriodoMovimentacao->getCampo("dt_final"));

        //calcula diferença de meses entre datas -- FIM
        $rsQtdMeses = new RecordSet;
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento = new FPessoalRecuperaQuantidadeMesesProgressaoAfastamento;
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('cod_contrato', $request->get('inContrato'));
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_inicial', $stDataProgressao[2]."-".$stDataProgressao[1]."-".$stDataProgressao[0]);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_final', $dtDataAtual[2]."-".$dtDataAtual[1]."-".$dtDataAtual[0]);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->recuperaMesesProgressaoAfastamento($rsQtdMeses);

        $arQtdMeses = $rsQtdMeses->arElementos;
        //Lista as progressões, a última progressão do rsProgressao é a progressão do padrão para esta data de início de progressão
        $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
        $obRFolhaPagamentoPadrao->addNivelPadrao();

        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses( $arQtdMeses[0]['retorno'] );
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
        $rsProgressao->setUltimoElemento();
        if ( $rsProgressao->getNumLinhas() > 0 ) {
            $stLblProgressao = $rsProgressao->getCampo('cod_nivel_padrao')." - ".$rsProgressao->getCampo('descricao');
            $inCodProgressao = $rsProgressao->getCampo('cod_nivel_padrao');
        }
    }
    
    $stJs .= "d.getElementById('stlblProgressao').innerHTML = '".$stLblProgressao."'; \n";
    $stJs .= "f.inCodProgressao.value = '".$inCodProgressao."'; \n";

    return $stJs;
}

function buscaContrato($codMatricula = '', $boCarregaAssentamento = true){
    global $request;

    $codContrato = "";
    $inRegistro = "";
    $stJs = "";

    if($codMatricula!=''){
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->setRegistro( $codMatricula );
        $obRPessoalServidor->roUltimoContratoServidor->listarContratos($rsContrato);
        while (!$rsContrato->eof()) {
            $codContrato = $rsContrato->getCampo("cod_contrato");
            $inRegistro = $rsContrato->getCampo("registro");

            $request->set('inCodContrato', $codContrato);
            $request->set('inCodMatricula', $inRegistro);
            $request->set('inContrato', $codMatricula);
            $rsContrato->proximo();
        }
    }

    if($boCarregaAssentamento){
        $stJs .= "f.inCodContrato.value = '".$codContrato."';";
        $stJs .= "f.inCodMatricula.value = '".$inRegistro."';";
        $stJs .= processarQuantDiasAssentamento($request);
    }

    return $stJs;
}

function limparArqDigital()
{
    $stJs = "f.stArqDigital.value = ''; \n";

    return $stJs;
}

function preencheListaArqDigital($stHtml = ''){
    $stJs = "d.getElementById('spnListaArqDigital').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaListaArqDigital($stModoGeracao, $inChave, $inCodClassificacao, $inCodAssentamento, $stDataInicial, $stDataFinal)
{
    global $request;

    $stAcao = $request->get('stAcao');
    $rsRecordSet = new Recordset;
    $arArquivosDigitais = ( is_array( Sessao::read('arArquivosDigitais') ) ) ? Sessao::read('arArquivosDigitais') : array();
    $arArqDoc = array();

    foreach($arArquivosDigitais AS $chave => $arquivo){
        if(   $arquivo['stModoGeracao']      == $stModoGeracao
           && $arquivo['inChave']            == $inChave
           && $arquivo['inCodClassificacao'] == $inCodClassificacao
           && $arquivo['inCodAssentamento']  == $inCodAssentamento
           && $arquivo['stDataInicial']      == $stDataInicial
           && $arquivo['stDataFinal']        == $stDataFinal
        ){
            if($arquivo['boExcluido']=='FALSE')
                $arArqDoc[] = $arquivo;
        }
    }

    $rsRecordSet->preenche( $arArqDoc );
    $stHtml = "";
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Arquivos digitais do Assentamento" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Arquivo" );
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "name" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "VISUALIZAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('abrirArqDigital');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        if($stAcao != 'excluir' && $stAcao != 'consultar'){
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao(true );
            $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirArqDigital');" );
            $obLista->ultimaAcao->addCampo("1","inId");
            $obLista->commitAcao();
        }

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs  = preencheListaArqDigital($stHtml);

    return $stJs;
}
function excluirArqDigital($inId){
    $stErro = null;

    $arTemp = array();
    $arArquivosDigitais = Sessao::read("arArquivosDigitais");
    foreach($arArquivosDigitais AS $chave => $arquivo){
        if($arquivo['inId'] != $inId){
            $arTemp[] = $arquivo;
        }else{
            if($arquivo['boCopiado']=='TRUE'){
                $arquivo['boExcluido'] = 'TRUE';
                $arTemp[] = $arquivo;
            }else{
                $stArquivo = $arquivo['tmp_name'];

                if (file_exists($stArquivo)) {
                    if(!unlink($stArquivo)){
                        $stErro = $arquivo['name']." não excluído!";
                        break;
                    }
                }
            }
        }
    }

    if (!$stErro)
        Sessao::write("arArquivosDigitais",$arTemp);

    return $stErro;
}

function limparArquivosAssentamentoAtual(){
    $stJs = "";
    
    $inId = Sessao::read('inId');

    if( is_null($inId) ){
        $arAssentamentoAtual = ( is_array( Sessao::read('arAssentamentoAtual') ) ) ? Sessao::read('arAssentamentoAtual') : array();
        $inIdAssentamento    = $arAssentamentoAtual['inIdAssentamento'];

        if( !empty($inIdAssentamento) || $inIdAssentamento === 0 ){
            $arArquivosTemp     = array();
            $arArquivosDigitais = ( is_array( Sessao::read('arArquivosDigitais') ) ) ? Sessao::read('arArquivosDigitais') : array();

            foreach($arArquivosDigitais AS $chave => $arquivo){
                if( $arquivo['inIdAssentamento'] == $inIdAssentamento ){
                    $stErro = excluirArqDigital($arquivo['inId']);

                    if ($stErro) {
                        $stJs .= "alertaAviso('".urlencode("Erro ao Excluir Arquivo Digital: ".$stErro)."','unica','erro','".Sessao::getId()."');";
                    }
                }else
                    $arArquivosTemp[] = $arquivo;
            }

            $arArquivosDigitais = $arArquivosTemp;

            Sessao::write('arArquivosDigitais', $arArquivosDigitais);
        }

        Sessao::write("arAssentamentoAtual", array());
    }

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "incluirNorma":
        $stJs .= incluirNorma();
    break;
    case "excluirNorma":
        $stJs .= excluirNorma($request->get('inId'));
    break;
    case "gerarAssentamento":
        $stJs .= gerarAssentamento();
    break;
    case "gerarAssentamentoFiltro":
        $stJs .= gerarAssentamento(false,"Filtro");
    break;
    case "preencherEspecialidade":
        $stJs .= preencherEspecialidade();
    break;
    case "preencherAssentamento":
        $stJs .= preencherAssentamento(false, $request);
    break;
    case "preencherLotacao":
        $stJs .= preencherLotacao();
    break;
    case "incluirAssentamento":
        $stJs .= incluirAssentamento($boExecuta,$request);
    break;
    case "alterarAssentamento":
        $stJs .= alterarAssentamento($boExecuta,$request);
    break;
    case "excluirAssentamento":
        $stJs .= excluirAssentamento();
    break;
    case "limparAssentamento":
        $stJs .= limparArquivosAssentamentoAtual();
        $stJs .= limparAssentamento();
    break;
    case "montaAlterarAssentamento":
        $stJs .= montaAlterarAssentamento(false, $request);
    break;
    case "calcularDataFinal":
        $stJs .= calcularDataFinal();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
    case "ajustarQuantidadeDias":
        $stJs .= ajustarQuantidadeDias();
    break;
    case "processarTriadi1":
        Sessao::remove('stDataInicial');
        Sessao::remove('stDataFinal');
        $stJs .= processarTriadi(1);
        break;
    case "processarTriadi2":
        Sessao::remove('stDataInicial');
        Sessao::remove('stDataFinal');
        $stJs .= processarTriadi(2);
        break;
    case "processarTriadi3":
        Sessao::remove('stDataInicial');
        Sessao::remove('stDataFinal');
        $stJs .= processarTriadi(3);
        break;
    case "processarQuantDiasAssentamento":
        $stJs = processarQuantDiasAssentamento($request);
        break;
    case "MontaNorma":
        $stJs .= MontaNorma();
        break;
    case "preencheClassificacao":
        $stAjaxReturn = preencheClassificacao($request->get('inCod'), $request->get('combo_type'));
        break;
    case "buscaContrato":
        $stJs .= buscaContrato($request->get('inContrato'));
    break;
    case "preencheSubDivisao":
        $stJs .= preencheSubDivisao();
        break;
    case "preencheSubDivisaoFuncao":
        $stJs .= preencheSubDivisaoFuncao();
        break;
    case "preencheCargo":
        $stJs .= preencheCargo();
        break;
    case "preencheFuncao":
        $stJs .= preencheFuncao();
        break;
    case "preencheEspecialidade":
        $stJs .= preencheEspecialidade();
        break;
    case "preencheEspecialidadeFuncao":
        $stJs .= preencheEspecialidadeFuncao();
        break;
    case "preenchePreEspecialidadeFuncao":
        $stJs .= preenchePreEspecialidadeFuncao();
        break;
    case "preencheInformacoesSalariais":
        $stJs .= preencheInformacoesSalariais();
        break;
    case "preencheProgressao":
        $stJs .= preencheProgressao($request->get('inCodPadrao'));
        break;
    case "calculaSalario":
        $stJs .= calculaSalario($request->get('inCodPadrao'),$request->get('inCodProgressao'));
        break;
    case "validarVigenciaSalario":
        $stJs .= validarVigenciaSalario();
        break;
    case "incluirArqDigital":
        $arArquivosDigitais = ( is_array( Sessao::read('arArquivosDigitais') ) ) ? Sessao::read('arArquivosDigitais') : array();
        $arAssentamentoAtual = array();

        $inIdAssentamento = Sessao::read('inId');
        $arAssentamentoAlterar = ( is_array( Sessao::read('arAssentamentoAtual') ) ) ? Sessao::read('arAssentamentoAtual') : array();

        #Alterando Assentamento, busca informações salvas no assentamento.
        if( count($arAssentamentoAlterar) > 0 ){
            $teste = $request->get($arAssentamentoAlterar['stNomeChave']);
            if(!empty($teste) || $request->get('stAcao') == 'alterar')
                $request->set($arAssentamentoAlterar['stNomeChave'], $arAssentamentoAlterar['inChave']);

            $teste = $request->get('inCodClassificacao');
            if(!empty($teste))
                $request->set('inCodClassificacao', $arAssentamentoAlterar['inCodClassificacao']);

            $teste = $request->get('inCodAssentamento');
            if(!empty($teste))
                $request->set('inCodAssentamento', $arAssentamentoAlterar['inCodAssentamento']);

            $teste = $request->get('stDataInicial');
            if(!empty($teste))
                $request->set('stDataInicial', $arAssentamentoAlterar['stDataInicial']);

            $request->set('stDataFinal', $arAssentamentoAlterar['stDataFinal']);
        }

        $stErro = null;

        $stModoGeracao = $request->get('stModoGeracao');
        $hdnModoGeracao = $request->get('hdnModoGeracao');
        if(empty($stModoGeracao) && !empty($hdnModoGeracao))
            $stModoGeracao = $hdnModoGeracao;

        $stNameArq = "";
        $stName = $_FILES["stArqDigital"]["name"];

        switch ( $stModoGeracao ){
            case 'contrato':
                $inChave = $request->get('inCodContrato');
                $stNomeChave = 'inCodContrato';
                if(empty($inChave))
                    $stErro = "Informe o campo Matrícula";
            break;
            case 'cgm/contrato':
                $inNumCGM = $request->get('inNumCGM');
                $inChave = $request->get('inContrato');
                $stNomeChave = 'inContrato';
                if(empty($inChave))
                    $stErro = "Informe o campo Matrícula";
                if(empty($inNumCGM))
                    $stErro = "Informe o campo CGM";
            break;
            case 'cargo':
                $inChave = $request->get('inCodCargo');
                $stNomeChave = 'inCodCargo';
                if(empty($inChave))
                    $stErro = "Informe o campo Cargo";
            break;
            case 'lotacao':
                $inChave = $request->get('HdninCodLotacao');
                $stNomeChave = 'HdninCodLotacao';
                if(empty($inChave))
                    $stErro = "Informe o campo Lotação";
            break;
        }

        if(!$stErro){
            if(empty($inChave))
                $stErro = "Tipo de Geração de Assentamento não informado";
        }

        if(!$stErro){
            $arAssentamentoAtual['stModoGeracao'] = $stModoGeracao;
            $arAssentamentoAtual['inChave'] = $inChave;
            $arAssentamentoAtual['stNomeChave'] = $stNomeChave;

            $stNameArq .= $inChave;
            $stNameArq .= Sessao::getEntidade();

            $inCodClassificacao = $request->get('inCodClassificacao');
            $inCodAssentamento = $request->get('inCodAssentamento');

            if(empty($inCodAssentamento))
                $stErro = "Informe o campo Assentamento";
            if(empty($inCodClassificacao))
                $stErro = "Informe o campo Classificação";

            if(!$stErro){
                $arAssentamentoAtual['inCodClassificacao'] = $inCodClassificacao;
                $arAssentamentoAtual['inCodAssentamento'] = $inCodAssentamento;

                $stNameArq .= '_'.$inCodClassificacao;
                $stNameArq .= '_'.$inCodAssentamento;
            }
        }

        if(!$stErro){
            $stDataInicial = $request->get('stDataInicial');
            $stDataFinal = $request->get('stDataFinal');

            if(empty($stDataInicial))
                $stErro = "Informe o campo Período";

            if(!$stErro){
                $arAssentamentoAtual['stDataInicial'] = $stDataInicial;
                $arAssentamentoAtual['stDataFinal'] = $stDataFinal;

                $arDataInicial = explode('/', $stDataInicial);
                $stNameArq .= '_'.$arDataInicial[0].'_'.$arDataInicial[1].'_'.$arDataInicial[2];

                if(!empty($stDataFinal)){
                    $arDataFinal = explode('/', $stDataFinal);
                    $stNameArq .= '_'.$arDataFinal[0].'_'.$arDataFinal[1].'_'.$arDataFinal[2];
                }
            }
        }

        $inId = 0;
        if(!$stErro){
            $stName = str_replace(' ','_',$stName);
            $stNameArq .= '_'.$stName;

            foreach($arArquivosDigitais AS $chave => $arquivo){
                $arDataInicial = explode('/', $arquivo['stDataInicial']);
                $arDataFinal   = explode('/', $arquivo['stDataFinal']);

                $stChaveArq  = $arquivo['stModoGeracao'].'_'.$arquivo['inChave'].Sessao::getEntidade();
                $stChaveArq .= '_'.$arquivo['inCodClassificacao'];
                $stChaveArq .= '_'.$arquivo['inCodAssentamento'];
                $stChaveArq .= '_'.$arDataInicial[0].'_'.$arDataInicial[1].'_'.$arDataInicial[2];
                if(!empty($arquivo['stDataFinal']))
                    $stChaveArq .= '_'.$arDataFinal[0].'_'.$arDataFinal[1].'_'.$arDataFinal[2];
                $stChaveArq .= '_'.$arquivo['name'];

                if($stChaveArq == $stModoGeracao.'_'.$stNameArq){
                    $stErro = $stName." já vinculado ao assentamento!";
                    break;
                }

                if( $arquivo['inId'] >= $inId )
                    $inId = $arquivo['inId']+1;
            }
        }

        if(!$stErro){
            if(empty($stName))
                $stErro = "Selecione o Arquivo Digital!";
        }

        if(!$stErro){
            if ($_FILES["stArqDigital"]["error"] > 0) {
                if ($_FILES["stArqDigital"]["error"] == 1 )
                    $stErro = "Arquivo ultrapassa o valor maxímo de ".ini_get("upload_max_filesize");
                else
                    $stErro = "Erro no upload do arquivo.";
            }
        }

        $stDirTMP = CAM_GRH_PESSOAL."tmp/";
        $stDirANEXO = CAM_GRH_PESSOAL."anexos/";

        if(!$stErro){
            if (!is_writable($stDirTMP)) {
                $stErro = " O diretório ".CAM_GRH_PESSOAL."tmp não possui permissão de escrita!";
            }
        }

        if(!$stErro){
            switch($_FILES['stArqDigital']['type']){
                #DOC
                case 'application/msword':
                #DOCX
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                #ODT
                case 'application/vnd.oasis.opendocument.text':
                #PDF
                case 'application/pdf':
                #PNG
                case 'image/png':
                #JPG/JPEG
                case 'image/jpeg':
                #GIF
                case 'image/gif':
                    $boType = 'TRUE';
                break;
                default:
                    $stErro = 'Tipo de Arquivo Inválido!';
                break;
            }
        }

        if(!$stErro){
            $stArquivoTMP = $stDirTMP.$stNameArq;
            $stArquivoANEXO = $stDirANEXO.$stNameArq;

            if(!move_uploaded_file($_FILES["stArqDigital"]["tmp_name"],$stArquivoTMP))
                $stErro = "Erro no upload do arquivo.";
        }

        if ($stErro) {
            $stJs  = "alertaAviso('".urlencode("Erro ao Incluir Arquivo Digital: ".$stErro)."','unica','erro','".Sessao::getId()."');";
        } else {
            chmod($stArquivoTMP,0777);

            if( is_null($inIdAssentamento) )
                $inIdAssentamento = (is_array(Sessao::read('arAssentamentos'))) ? count(Sessao::read('arAssentamentos')) : 0;
            $arAssentamentoAtual['inIdAssentamento'] = $inIdAssentamento;

            $arArquivosUpload['inIdAssentamento']    = $inIdAssentamento;
            $arArquivosUpload['stModoGeracao']       = $stModoGeracao;
            $arArquivosUpload['stArquivo']           = $stArquivoANEXO;
            $arArquivosUpload['arquivo_digital']     = $stNameArq;
            $arArquivosUpload['name']                = $stName;
            $arArquivosUpload['inId']                = $inId;
            $arArquivosUpload['boCopiado']           = 'FALSE';
            $arArquivosUpload['tmp_name']            = $stArquivoTMP;
            $arArquivosUpload['inChave']             = $inChave;
            $arArquivosUpload['inCodClassificacao']  = $inCodClassificacao;
            $arArquivosUpload['inCodAssentamento']   = $inCodAssentamento;
            $arArquivosUpload['stDataInicial']       = $stDataInicial;
            $arArquivosUpload['stDataFinal']         = $stDataFinal;
            $arArquivosUpload['boExcluido']          = 'FALSE';

            $arArquivosDigitais[] = $arArquivosUpload;

            Sessao::write("arArquivosDigitais", $arArquivosDigitais);
            Sessao::write("arAssentamentoAtual", $arAssentamentoAtual);

            $stJs  = montaListaArqDigital($stModoGeracao, $inChave, $inCodClassificacao, $inCodAssentamento, $stDataInicial, $stDataFinal);
            $stJs .= limparArqDigital();
        }

        $stJs .= "LiberaFrames();\n";
    break;
    case "excluirArqDigital":
        $stErro = excluirArqDigital($request->get('inId'));

        if ($stErro) {
            $stJs  = "alertaAviso('".urlencode("Erro ao Excluir Arquivo Digital: ".$stErro)."','unica','erro','".Sessao::getId()."');";
        }else{
            $inId                = Sessao::read('inId');
            $stModoGeracao       = $request->get('stModoGeracao', $request->get('hdnModoGeracao'));
            $arAssentamentoAtual = ( is_array( Sessao::read('arAssentamentoAtual') ) ) ? Sessao::read('arAssentamentoAtual') : array();

            if( !is_null($inId) ){
                $inChave            = $arAssentamentoAtual['inChave'];
                $inCodClassificacao = $arAssentamentoAtual['inCodClassificacao'];
                $inCodAssentamento  = $arAssentamentoAtual['inCodAssentamento'];
                $stDataInicial      = $arAssentamentoAtual['stDataInicial'];
                $stDataFinal        = $arAssentamentoAtual['stDataFinal'];
            }else{
                switch ( $stModoGeracao ){
                    case 'contrato':
                        $inChave = $request->get('inCodContrato');
                    break;
                    case 'cgm/contrato':
                        $inChave = $request->get('inContrato');
                    break;
                    case 'cargo':
                        $inChave = $request->get('inCodCargo');
                    break;
                    case 'lotacao':
                        $inChave = $request->get('HdninCodLotacao');
                    break;
                }
                $inCodClassificacao = $request->get('inCodClassificacao');
                $inCodAssentamento  = $request->get('inCodAssentamento');
                $stDataInicial      = $request->get('stDataInicial');
                $stDataFinal        = $request->get('stDataFinal');
            }

            $stJs = montaListaArqDigital($stModoGeracao, $inChave, $inCodClassificacao, $inCodAssentamento, $stDataInicial, $stDataFinal);
        }
    break;
    case "abrirArqDigital":
        $arArquivosDigitais = Sessao::read("arArquivosDigitais");

        foreach($arArquivosDigitais AS $chave => $arquivo){
            if($arquivo['inId'] == $request->get('inId')){
                if($arquivo['boCopiado'] == 'FALSE'){
                    $stArquivo = $arquivo['tmp_name'];
                    $stNomArq = $arquivo['name'];
                }else{
                    $stArquivo = $arquivo['stArquivo'];
                    $stNomArq = $arquivo['name'];
                }

                break;
            }
        }

        if(is_readable($stArquivo)){
            $stLink = "../../../exportacao/instancias/processamento/download.php";

            $stJs  = "
                    function abrirArqDigital(stArq, stNom){
                        var stAction = f.action;
                        var stTarget = f.target;
                        f.action = '".$stLink."?boCompletaDir=false&arq='+stArq+'&label='+stNom;
                        f.target = 'oculto'
                        f.submit();
                        f.action = stAction; 
                        f.target = stTarget;
                    }
            ";
            $stJs .= " abrirArqDigital('".$stArquivo."','".$stNomArq."');";
        }else
            $stJs = "alertaAviso('Erro ao abrir o Arquivo Digital!','unica','erro','".Sessao::getId()."');";
    break;

    case 'limparFormulario':
        $arAssentamentos = ( is_array( Sessao::read('arAssentamentos') ) ) ? Sessao::read('arAssentamentos') : array();
        $inCountAssentamentos = (count($arAssentamentos) > 0 ) ? count($arAssentamentos) : 1;

        $stJs  = limparArquivosAssentamentoAtual();
        $stJs .= limparAssentamento();

        $request->set('inId',0);
        for($i=0;$i<$inCountAssentamentos;$i++){
            $stJs .= excluirAssentamento();
        }
    break;

    case "limparArquivosAssentamentoAtual":
        $stJs = limparArquivosAssentamentoAtual();
    break;
}

if (isset($stJs)) {
    sistemaLegado::executaFrameOculto($stJs);
}

if (isset($stAjaxReturn)) {
    echo $stAjaxReturn; exit;
}

?>
