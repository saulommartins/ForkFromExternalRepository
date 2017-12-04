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
/*
    * Página do Oculto
    * Data de Criação   : 05/02/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: OCManterNotasFiscais.php 62431 2015-05-07 20:45:28Z arthur $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscalEmpenhoLiquidacao.class.php";

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

function montaNumSerie($ent = true, $value = "")
{
    $obTxtNumSerie = new TextBox;
    $obTxtNumSerie->setName      ( "inNumSerie"                         );
    $obTxtNumSerie->setId        ( "inNumSerie"                         );
    $obTxtNumSerie->setValue     ( $value                               );
    $obTxtNumSerie->setRotulo    ( "Série do Docto Fiscal"              );
    $obTxtNumSerie->setTitle     ( "Informe a série do Docto Fiscal."   );
    $obTxtNumSerie->setNull      ( $ent                                 );
    $obTxtNumSerie->setInteiro   ( false                                );
    $obTxtNumSerie->setSize      ( 8                                    );
    $obTxtNumSerie->setMaxLength ( 8                                    );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obTxtNumSerie );
    $obFormulario->montaInnerHTML();

    $stJs = "jQuery('#spnSerie').html('".$obFormulario->getHTML()."');";

    return $stJs;
}

function montaNumeroNF($ent = true, $value = "")
{
    $obTxtNumNF = new TextBox;
    $obTxtNumNF->setName      ( "inNumeroNF"                         );
    $obTxtNumNF->setId        ( "inNumeroNF"                         );
    $obTxtNumNF->setValue     ( $value                               );
    $obTxtNumNF->setRotulo    ( "Número do Docto Fiscal"             );
    $obTxtNumNF->setTitle     ( "Informe o número do Docto Fiscal."  );
    $obTxtNumNF->setNull      ( $ent                                 );
    $obTxtNumNF->setInteiro   ( true                                 );
    $obTxtNumNF->setSize      ( 20                                   );
    $obTxtNumNF->setMaxLength ( 20                                   );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obTxtNumNF );
    $obFormulario->montaInnerHTML();

    $stJs = "jQuery('#spnNumero').html('".$obFormulario->getHTML()."');";

    return $stJs;
}

//Se $stMunicipal = false Tipo de Chave Estadual, Se True = Tipo Municipal.
function montaChaveAcesso($boMunicipal = false, $value = "")
{
    if ($boMunicipal == true) {
        $stNome = "Municipal";
        $Size = 60;
        $boMunicipal = false; //campo deve ser de preenchimento obrigatório
    } else {
        $stNome = "";
        $Size = 44;
    }
    
    $stTitulo = ($stNome == "Municipal") ? " ".$stNome."." : ".";
        
    $obTxtChave = new TextBox;
    $obTxtChave->setName      ( "inChave".$stNome                     );
    $obTxtChave->setId        ( "inChave".$stNome                     );
    $obTxtChave->setValue     ( $value                                );
    $obTxtChave->setRotulo    ( "Chave de Acesso ".$stNome            );
    $obTxtChave->setTitle     ( "Informe a Chave de Acesso".$stTitulo );
    $obTxtChave->setNull      ( $boMunicipal                          );
    $obTxtChave->setInteiro   ( false                                 );
    $obTxtChave->setSize      ( $Size                                 );
    $obTxtChave->setMaxLength ( $Size                                 );
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obTxtChave );
    $obFormulario->montaInnerHTML();

    $stJs.= "jQuery('#spnChave').html('".$obFormulario->getHTML()."');";

    return $stJs;
}

switch ($stCtrl) {
    
    case "montaSpan":

        $inCodTipoNota = $_REQUEST['inCodTipoNota'];
        
        if ($inCodTipoNota == 1 || $inCodTipoNota == 4) {
            $stJs.= montaChaveAcesso();

            if ($inCodTipoNota == 1) {
                $stJs.= montaNumeroNF();
                $stJs.= montaNumSerie();
            } else {
                $stJs.= "jQuery('#spnNumero').html('');";
                $stJs.= "jQuery('#spnSerie').html('');";
            }
            
        } elseif ($inCodTipoNota == "") {
            
            $stJs.= "jQuery('#spnChave').html('');";
            $stJs.= "jQuery('#spnSerie').html('');";
            $stJs.= "jQuery('#spnNumero').html('');";
            
        } else {
            
            $stJs.= montaNumeroNF($ent = false);
            $stJs.= montaNumSerie($ent = false);
            
            if ($inCodTipoNota == 2) {
                $stJs.= montaChaveAcesso(true); //true = Tipo de chave Municipal
            } else {
                $stJs.= "jQuery('#spnChave').html('');";
            }
            
        }
        echo $stJs;

    break;

    case "carregaDados":
        if ($_REQUEST['inCodNota']) {

            include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscal.class.php");
            $obTTCEMGNotaFiscal = new TTCEMGNotaFiscal;
            $obTTCEMGNotaFiscal->setDado( 'cod_nota'    , $_REQUEST['inCodNota']    );
            $obTTCEMGNotaFiscal->setDado( 'exercicio'   , $_REQUEST['stExercicio']  );
            $obTTCEMGNotaFiscal->setDado( 'cod_entidade', $_REQUEST['cod_entidade'] );

            if ($_REQUEST['stExercicio']) {
                $arFiltro[] = " NF.exercicio = '". $_REQUEST['stExercicio'] ."'";
            }
            if ($_REQUEST['inCodNota']) {
                $arFiltro[] = " NF.cod_nota = ".$_REQUEST['inCodNota'];
            }
            if ($_REQUEST['cod_entidade']) {
                $arFiltro[] = " NF.cod_entidade = ".$_REQUEST['cod_entidade'];
            }

            if ( count( $arFiltro ) > 0 ) {
                $stFiltro = " WHERE " .implode ( ' AND ' , $arFiltro );
                unset($arFiltro);
            }

            $stOrdem = "";

            $obTTCEMGNotaFiscal->recuperaNotasFiscais($rsNotaFiscal, $stFiltro, $stOrdem, $boTransacao);

            $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;

            if ($_REQUEST['stExercicio']) {
                $arFiltro[] = " exercicio = '". $_REQUEST['stExercicio'] ."'";
            }
            if ($_REQUEST['inCodNota']) {
                $arFiltro[] = " cod_nota = ".$_REQUEST['inCodNota'];
            }
            if ($_REQUEST['cod_entidade']) {
                $arFiltro[] = " cod_entidade = ".$_REQUEST['cod_entidade'];
            }

            if ( count( $arFiltro ) > 0 ) {
                $stFiltro = " WHERE " .implode ( ' AND ' , $arFiltro );
                unset($arFiltro);
            }

            $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota'    , $_REQUEST['inCodNota']    );
            $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio'   , $_REQUEST['stExercicio']  );
            $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_entidade', $_REQUEST['cod_entidade'] );
            $obTTCEMGNotaFiscalEmpenho->recuperaTodos($rsNotaFiscalEmpenho, $stFiltro);

            $arEmpenhos = array();
            $inCount = 0;
            $nuVlAssociadoTotal = 0;

            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            while ( !$rsNotaFiscalEmpenho->eof()) {
                $stFiltro  = "   AND e.exercicio    = '".$rsNotaFiscalEmpenho->getCampo('exercicio_empenho')."'";
                $stFiltro .= "   AND e.cod_entidade =  ".$rsNotaFiscalEmpenho->getCampo('cod_entidade');
                $stFiltro .= "   AND e.cod_empenho  =  ".$rsNotaFiscalEmpenho->getCampo('cod_empenho');
                $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsEmpenho, $stFiltro);

                $arEmpenhos[$inCount]['cod_entidade']  = $rsEmpenho->getCampo('cod_entidade');
                $arEmpenhos[$inCount]['cod_empenho']   = $rsEmpenho->getCampo('cod_empenho' );
                $arEmpenhos[$inCount]['exercicio']     = $rsEmpenho->getCampo('exercicio'   );
                $arEmpenhos[$inCount]['nom_cgm']       = $rsEmpenho->getCampo('credor'      );
                $arEmpenhos[$inCount]['nuVlAssociado'] 			 = number_format($rsNotaFiscalEmpenho->getCampo('vl_associado'),2,',','.'	);
                $arEmpenhos[$inCount]['cod_nota_liquidacao']     = $rsNotaFiscalEmpenho->getCampo('cod_nota_liquidacao'                      );
                $arEmpenhos[$inCount]['exercicio_liquidacao']    = $rsNotaFiscalEmpenho->getCampo('exercicio_liquidacao'                     );
                $arEmpenhos[$inCount]['valor_liquidacao']        = number_format($rsNotaFiscalEmpenho->getCampo('vl_liquidacao'),2,',','.'   );

                $nuVlAssociadoTotal += $rsNotaFiscalEmpenho->getCampo('vl_associado');
                $inCount++;
                $rsNotaFiscalEmpenho->proximo();
            }

            $stJs .= "f.inCodTipoNota.value           = '".$rsNotaFiscal->getCampo('cod_tipo')                 ."';\n";
            $stJs .= "f.stTipoDocto.value             = '".$rsNotaFiscal->getCampo('cod_tipo')                 ."';\n";
            $stJs .= "f.inCodNota.value               = '".$rsNotaFiscal->getCampo('cod_nota')                 ."';\n";
            $stJs .= "f.cod_entidade.value            = '".$arEmpenhos[0]['cod_entidade']                      ."';\n";
            $stJs .= "f.inCodEntidade.value           = '".$arEmpenhos[0]['cod_entidade']                      ."';\n";
            $stJs .= "f.stAIDF.value                  = '".$rsNotaFiscal->getCampo('aidf')                     ."';\n";
            $stJs .= "f.dtEmissao.value               = '".$rsNotaFiscal->getCampo('data_emissao')             ."';\n";
            $stJs .= "f.inNumInscricaoMunicipal.value = '".$rsNotaFiscal->getCampo('inscricao_municipal')      ."';\n";
            $stJs .= "f.inNumInscricaoEstadual.value  = '".$rsNotaFiscal->getCampo('inscricao_estadual')       ."';\n";
            $stJs .= "f.hdnVlAssociadoTotal.value     = '".number_format($nuVlAssociadoTotal, 2, '.', '')     ."';\n";

            $inCodTipoNota = $rsNotaFiscal->getCampo('cod_tipo');
            
            if ($inCodTipoNota == 1 || $inCodTipoNota == 4) {
                $stJs.= montaChaveAcesso(false, $rsNotaFiscal->getCampo('chave_acesso'));

                if ($inCodTipoNota == 1) {
                    $stJs.= montaNumeroNF(true, $rsNotaFiscal->getCampo('nro_nota'));
                    $stJs.= montaNumSerie(true, addslashes($rsNotaFiscal->getCampo('nro_serie')));
                } else {
                    $stJs.= "jQuery('#spnNumero').html('');";
                    $stJs.= "jQuery('#spnSerie').html('');";
                }
            } elseif ($inCodTipoNota == "") {
                $stJs.= "jQuery('#spnChave').html('');";
                $stJs.= "jQuery('#spnSerie').html('');";
                $stJs.= "jQuery('#spnNumero').html('');";
            } else {
                $stJs.= montaNumeroNF($ent=false, $rsNotaFiscal->getCampo('nro_nota'));
                $stJs.= montaNumSerie($ent=false, addslashes($rsNotaFiscal->getCampo('nro_serie')));
                if ($inCodTipoNota == 2) {
                    $stJs.= montaChaveAcesso(true, $rsNotaFiscal->getCampo('chave_acesso'));//true = Tipo de chave Municipal
                } else {
                    $stJs.= "jQuery('#spnChave').html('');";
                }
            }

            $stJs .= "f.cod_entidade.disabled         = true;                                                      \n";
            $stJs .= "f.stNomEntidade.disabled        = true;                                                      \n";
            $stJs .= "document.getElementById('inCodEntidade').readOnly = true;                                    \n";
            $stJs .= "document.getElementById('stExercicio').readOnly = true;                                      \n";

            Sessao::write('arEmpenhos', $arEmpenhos);
            $stJs .= montaListaEmpenhos();

        }
        echo $stJs;

    break;

    case "incluirEmpenhoLista":

        $arRegistro = array();
        $arEmpenhos = array();
        $numEmpenho = $_REQUEST['numEmpenho'];
        $boIncluir  = true;

        $arEmpenhos = Sessao::read('arEmpenhos');

        if ($_REQUEST['nuTotalNf'] != '' && $_REQUEST['nuVlDesconto'] != '' && $_REQUEST['stExercicioEmpenho'] and $numEmpenho != "" and $_REQUEST['nuVlAssociado']) {

            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'cod_empenho' , $numEmpenho                      );
            $obTEmpenhoEmpenho->setDado( 'exercicio'   , $_REQUEST['stExercicioEmpenho']  );

            $arNotaLiquidacao = explode('||', $_REQUEST['cmbLiquidacao']);
            $inCodNotaLiquidacao   = $arNotaLiquidacao[0];
            $numEmpenho .= $inCodNotaLiquidacao;
            $stExercicioLiquidacao = $arNotaLiquidacao[1];
            $valorLiquidacao = $arNotaLiquidacao[5];

            $obTEmpenhoEmpenho->recuperaEmpenhoNotaFiscal($rsRecordSet);

            $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;

            if ($_REQUEST['numEmpenho']) {
                $arFiltro[] = "cod_empenho = ". $_REQUEST['numEmpenho'];
            }
            if ($_REQUEST['stExercicioEmpenho']) {
                $arFiltro[] = "exercicio_empenho = '".$_REQUEST['stExercicioEmpenho']."'";
            }
            if ($_REQUEST['inCodEntidade']) {
                $arFiltro[] = "cod_entidade = ".$_REQUEST['inCodEntidade'];
            }
            $arFiltro[] = "cod_nota_liquidacao = ".$inCodNotaLiquidacao;
            $arFiltro[] = "exercicio_liquidacao = '".$stExercicioLiquidacao."'";
            if ($_REQUEST['inCodNota']) {
                $arFiltro[] = "cod_nota <> ".$_REQUEST['inCodNota'];
            }

            if ( count( $arFiltro ) > 0 ) {
                $stFiltro = " WHERE " .implode ( ' AND ' , $arFiltro );
                unset($arFiltro);
            }

            $obTTCEMGNotaFiscalEmpenho->recuperaTodos($rsNotaFiscalEmpenho, $stFiltro);

            $nuValorTotal = 0;
            while ( !$rsNotaFiscalEmpenho->eof()) {
                $nuValorTotal += $rsNotaFiscalEmpenho->getCampo('vl_associado');
                $rsNotaFiscalEmpenho->proximo();
            }

            if ( $rsRecordSet->getNumLinhas() > 0 ) {

                if ( count( $arEmpenhos ) > 0 ) {
                    foreach ($arEmpenhos as $key => $array) {
                        $stCod = $array['cod_empenho'];
                        $stCod .= $array['cod_nota_liquidacao'];

                        if ($numEmpenho == $stCod) {
                            $boIncluir = false;
                            $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                            break;
                        }
                    }
                }
                if ($boIncluir) {
                    $nuVlAssociado = str_replace('.', '' , $_REQUEST['nuVlAssociado']);
                    $nuVlAssociado = str_replace(',', '.', $nuVlAssociado);

                    $nuTotalNf = str_replace('.', '' , $_REQUEST['nuTotalNf']);
                    $nuTotalNf = str_replace(',', '.', $nuTotalNf);

                    $nuVlDesconto = str_replace('.', '' , $_REQUEST['nuVlDesconto']);
                    $nuVlDesconto = str_replace(',', '.', $nuVlDesconto);

                    $nuVlAssociadoTotal = $_REQUEST['hdnVlAssociadoTotal'];

                    $nuTotalLiquidNf = (float)$nuTotalNf - (float)$nuVlDesconto;
                    if (((float)$nuVlAssociadoTotal + (float)$nuVlAssociado) <= (float)$nuTotalLiquidNf) {
                        if ((float)$valorLiquidacao >= ((float)$nuVlAssociado + (float)$nuValorTotal)) {
                            if ((float)$valorLiquidacao >= (float)$nuVlAssociado) {
                                $arRegistro['cod_entidade'        ]    = $rsRecordSet->getCampo('cod_entidade');
                                $arRegistro['cod_empenho'         ]    = $rsRecordSet->getCampo('cod_empenho' );
                                $arRegistro['data_empenho'        ]    = $rsRecordSet->getCampo('dt_empenho'  );
                                $arRegistro['nom_cgm'             ]    = $rsRecordSet->getCampo('credor'      );
                                $arRegistro['exercicio'           ]    = $rsRecordSet->getCampo('exercicio'   );
                                $arRegistro['cod_nota_liquidacao' ]    = $inCodNotaLiquidacao;
                                $arRegistro['exercicio_liquidacao']    = $stExercicioLiquidacao;
                                $arRegistro['valor_liquidacao']        = number_format($valorLiquidacao,2,',','.');
                                $arRegistro['nuVlAssociado']           = $_REQUEST['nuVlAssociado'];
                                $arEmpenhos[] = $arRegistro ;

                                Sessao::write('arEmpenhos', $arEmpenhos);
                                $stJs .= "f.cod_entidade.disabled = true; ";
                                $stJs .= "f.stNomEntidade.disabled = true; ";
                                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                                $stJs .= 'd.getElementById("nuTotalLiquidacao").innerHTML = "&nbsp;";';
                                $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
                                $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
                                $stJs .= "f.stEmpenho.value = '';";
                                $stJs .= "f.numEmpenho.value = '';";
                                $stJs .= "f.nuVlAssociado.value = '';";
                                $hdnVlAssociadoTotal = (float)$nuVlAssociadoTotal+(float)$nuVlAssociado;
                                $stJs .= "jQuery('#hdnVlAssociadoTotal').val('".number_format($hdnVlAssociadoTotal, 2, '.', '')."');";
                                $stJs .= montaListaEmpenhos();
                            } else {
                                $stJs .= "alertaAviso('Valor total maior que o valor da liquidação.','form','erro','".Sessao::getId()."');";
                            }
                        } else {
                            $nuValorDisponivel = (float)$valorLiquidacao - (float)$nuValorTotal;
                            $stJs .= "alertaAviso('Valor informado para o Docto Fiscal ultrapassa o valor disponível para a liquidação selecionada. Valor disponível: ".number_format($nuValorDisponivel,2,',','.')."','form','erro','".Sessao::getId()."');";
                        }
                    } else {
                        $nuTotalDisponivel = (float)$nuTotalLiquidNf - (float)$nuVlAssociadoTotal;
                        $stJs .= "alertaAviso('Valor total associado é maior que o valor líquido do documento fiscal. Valor disponível: ".number_format($nuTotalDisponivel,2,',','.')."','form','erro','".Sessao::getId()."');";
                    }
                }
            } else {
                $stJs .= "alertaAviso('Empenho informado inválido.','form','erro','".Sessao::getId()."');";
            }
        } else {
            if (!$_REQUEST['stExercicioEmpenho']) {
                $stJs .= "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');";
            }
            if (!$numEmpenho) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.nuVlAssociado.value = '';";
                $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
                $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
            }
            if (!$_REQUEST['nuVlAssociado']) {
                $stJs .= "alertaAviso('Informe o valor associado.','form','erro','".Sessao::getId()."');";
            }
            if (!$_REQUEST['nuVlDesconto']) {
                $stJs  = "alertaAviso('Informe o valor de desconto do documento fiscal.','form','erro','".Sessao::getId()."');";
                $stJs .= "jQuery('#nuVlDesconto').focus();";
            }
            if (!$_REQUEST['nuTotalNf']) {
                $stJs  = "alertaAviso('Informe o valor total do documento fiscal.','form','erro','".Sessao::getId()."');";
                $stJs .= "jQuery('#nuTotalNf').focus();";
            }            
        }
        echo $stJs;
    break;

    case "excluirEmpenhoLista":

        $arTempEmp = array();
        $arEmpenhos = Sessao::read('arEmpenhos');

        foreach ($arEmpenhos as $registro) {
            $stChaveRequest = $_REQUEST['codEmpenho'].$_REQUEST['codNotaLiquidacao'].$_REQUEST['codEntidade'].$_REQUEST['stExercicio'];
            $stChaveRegistro = $registro['cod_empenho'].$registro['cod_nota_liquidacao'].$registro['cod_entidade'].$registro['exercicio'];

            if ($stChaveRegistro != $stChaveRequest) {
                $arTempEmp[] = $registro;
            }
        }

        if (count($arTempEmp) <= 0) {

            $stJs  = "f.inCodEntidade.disabled = false; ";
            $stJs .= "f.stNomEntidade.disabled = false; ";

        }

        Sessao::write('arEmpenhos', $arTempEmp);
        $stJs .= montaListaEmpenhos();

        echo $stJs;
    break;

    case "limpar":

             $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
             $stJs .= 'd.getElementById("nuTotalLiquidacao").innerHTML = "&nbsp;";';
             $stJs .= "f.numEmpenho.value = '';";
             $stJs .= "f.nuVlAssociado.value = '';";
             $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
             $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";

        echo $stJs;
    break;

    case "preencheInner":

        $numEmpenho = $_REQUEST['numEmpenho'];

        if ($_REQUEST['inCodEntidade'] and $_REQUEST['stExercicioEmpenho'] and $numEmpenho) {
            $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;

            $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_empenho'        , $_REQUEST['numEmpenho']         );
            $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_empenho'  , $_REQUEST['stExercicioEmpenho'] );
            $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_entidade'       , $_REQUEST['inCodEntidade']      );
            $obTTCEMGNotaFiscalEmpenho-> setCampoCod('');

            if ($_REQUEST['numEmpenho']) {
                $arFiltro[] = "cod_empenho=". $_REQUEST['numEmpenho'];
            }
            if ($_REQUEST['stExercicioEmpenho']) {
                $arFiltro[] = "exercicio_empenho='".$_REQUEST['stExercicioEmpenho']."'";
            }
            if ($_REQUEST['inCodEntidade']) {
                $arFiltro[] = "cod_entidade=".$_REQUEST['inCodEntidade'];
            }

            if ( count( $arFiltro ) > 0 ) {
                $stFiltro = " WHERE " .implode ( ' AND ' , $arFiltro );
                unset($arFiltro);
            }

            $obTTCEMGNotaFiscalEmpenho->recuperaTodos($rsNotaFiscalEmpenho, $stFiltro);

            $stFiltro="";
            $inCount=0;
            while ( !$rsNotaFiscalEmpenho->eof()) {
                $FiltroLiquidacao[$inCount]['cod_nota_liquidacao' ] = $rsNotaFiscalEmpenho->getCampo('cod_nota_liquidacao'  );
                $FiltroLiquidacao['cod_entidade' ] = $rsNotaFiscalEmpenho->getCampo('cod_entidade'  );
                $inCount++;
                $rsNotaFiscalEmpenho->proximo();
            }

            if ($inCount>0) {
                $stFiltro .=" AND empenho.cod_entidade=".$FiltroLiquidacao['cod_entidade' ];
            }

            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'cod_empenho' , $numEmpenho                      );
            $obTEmpenhoEmpenho->setDado( 'exercicio'   , $_REQUEST['stExercicioEmpenho']  );

            $obTEmpenhoEmpenho->recuperaEmpenhoLiquidacaoNotaFiscal($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "'.$rsRecordSet->getCampo('credor').'";';

                $stJs .= buscaLiquidacoes($stFiltro);
            } else {
                if ($inCount == 0) {
                    $stJs  = "alertaAviso('Não há liquidações para vinculo com a NF.','form','erro','".Sessao::getId()."');\n";
                }
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
            }
        } else {
            if (!$_REQUEST['inCodEntidade']) {
                $stJs  = "alertaAviso('Informe a entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.inCodEntidade.focus();\n";
            }
            if (!$_REQUEST['stExercicioEmpenho']) {
                $stJs  = "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.stExercicioEmpenho.focus();\n";
            }
            if (!$numEmpenho) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.nuVlAssociado.value = '';";
                $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
                $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
            }
            $stJs .= "f.numEmpenho.value = '';";

        }

        echo $stJs;

    break;

    case "buscaEmpenho":

        $numEmpenho = $_REQUEST['numEmpenho'];

        if (!empty($numEmpenho)) {
            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            if ($request->get('stExercicio') != '') {
                $stExercicio = $request->get('stExercicio');
            } else {
                $stExercicio = $request->get('stExercicioEmpenho');
            }
            $obTEmpenhoEmpenho->setDado( 'exercicio'   , $stExercicio  );
            $obTEmpenhoEmpenho->setDado( 'cod_empenho' , $numEmpenho               );

            $obTEmpenhoEmpenho->recuperaEmpenhoBuscaInner($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {

                $stJs = "jQuery('#stEmpenho').html('".$rsRecordSet->getCampo('credor')."');";

            } else {
                $stJs  = "alertaAviso('Não há liquidações para vinculo com a NF.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "jQuery('#stEmpenho').html('&nbsp');";
                $stJs .= "jQuery('#numEmpenho').val('');";
            }
        } else {
            $stJs  = "jQuery('#numEmpenho').val('');";
            $stJs .= "jQuery('#stEmpenho').html('&nbsp');";
        }
        echo $stJs;

    break;

    case "montaLiquidacao":
        $cmbLiquidacao = explode("||", $_REQUEST['cmbLiquidacao']);
        $inCodLiquid = $cmbLiquidacao[0];
        $stExercicioLiquid = $cmbLiquidacao[1];
        $dtDataLiquidacao = $cmbLiquidacao[2];
        $inCodigoEmpenho = $cmbLiquidacao[3];
        $stExercicioEmpenho = $cmbLiquidacao[4];
        $ValorNota = $cmbLiquidacao[5];
        $flValorNota = number_format($cmbLiquidacao[5],2,',','.');

        $stJs  = 'd.getElementById("nuTotalLiquidacao").innerHTML = "'.$flValorNota.'";';
        $stJs .= 'd.getElementById("nuTotalLiquidacao").value = "'.$ValorNota.'";';

        echo $stJs;
    break;

    case "atualizaValorLiquido":
        if ($_REQUEST['nuTotalNf'] != '' && $_REQUEST['nuVlDesconto'] != '') {
            $nuTotalNf = str_replace('.', '' , $_REQUEST['nuTotalNf']);
            $nuTotalNf = str_replace(',', '.', $nuTotalNf);
            $nuVlDesconto = str_replace('.', '' , $_REQUEST['nuVlDesconto']);
            $nuVlDesconto = str_replace(',', '.', $nuVlDesconto);

            $nuTotalLiquidNf = (float)$nuTotalNf - (float)$nuVlDesconto;

            $stJs = "jQuery('#nuTotalLiquidNf').val('".number_format($nuTotalLiquidNf,2,',','.')."');";
            $stJs = "jQuery('#nuTotalLiquidNf').html('".number_format($nuTotalLiquidNf,2,',','.')."');";
        } else {
            $stJs = "jQuery('#nuTotalLiquidNf').val('');";
        }

        echo $stJs;

    break;
}

function buscaLiquidacoes($stFiltro="")
{
    $numEmpenho = $_REQUEST['numEmpenho'];;
    include_once CAM_GF_EMP_NEGOCIO.'REmpenhoOrdemPagamento.class.php';
    $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
    $stJs .= "f = parent.frames['telaPrincipal'].document.frm;\n";
    $stJs .= "d = parent.frames['telaPrincipal'].document;\n";
    $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
    $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
    if ($numEmpenho && $_REQUEST["inCodEntidade"]) {
        include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $obTEmpenhoEmpenho->setDado('cod_empenho' , $numEmpenho);
        $obTEmpenhoEmpenho->setDado('exercicio'   , $_REQUEST['stExercicioEmpenho']);
        $obTEmpenhoEmpenho->setDado('cod_entidade', $_REQUEST['inCodEntidade']);

        $obTEmpenhoEmpenho->recuperaLiquidacoesNotaFiscal($rsLiquidacoes, $stFiltro);

        $inContador = 1;
        while ( !$rsLiquidacoes->eof() ) {
            if ( $rsLiquidacoes->getCampo("cod_empenho") == $numEmpenho) {

                $flValorNota        = $rsLiquidacoes->getCampo( "vl_nota" );
                $flValorNotaTMP     = str_replace( '.','',$flValorNota );
                $flValorNotaTMP     = str_replace( ',','.',$flValorNotaTMP );
                if ($flValorNotaTMP > 0) {
                    $inCodigoLiquidacao = $rsLiquidacoes->getCampo('cod_nota');
                    $exercicioNota      = $rsLiquidacoes->getCampo('exercicio_nota');
                    $dtDataLiquidacao   = $rsLiquidacoes->getCampo('dt_liquidacao');
                    $inCodigoEmpenho    = $rsLiquidacoes->getCampo('cod_empenho');
                    $exercicioEmpenho   = $rsLiquidacoes->getCampo('exercicio_empenho');

                    $mixCombo = $inCodigoLiquidacao." - ".$dtDataLiquidacao;
                    $mixComboValor = $inCodigoLiquidacao."||".$exercicioNota."||".$dtDataLiquidacao."||".$inCodigoEmpenho."||".$exercicioEmpenho."||".$rsLiquidacoes->getCampo( "vl_nota" );
                    $stJs .= "f.cmbLiquidacao.options[$inContador] = new Option('".$mixCombo."','".$mixComboValor."'); \n";
                    $inContador++;
                }
            }
            $rsLiquidacoes->proximo();
        }

        if ($rsLiquidacoes->inNumLinhas < 1) {
                $stJs .= "alertaAvisoTelaPrincipal('Número do Empenho é inválido (".$numEmpenho.").','form','erro','" . Sessao::getId() . "', '../');";
                $stJs .= "f.numEmpenho.value='';";
                $stJs .= "d.getElementById('stEmpenho').innerHTML='&nbsp;';";

        }

        $rsLiquidacoes->anterior();
        $stFornecedor = ( $rsLiquidacoes->getCampo('credor')) ? str_replace("'","\'",$rsLiquidacoes->getCampo('credor')): '&nbsp;';
        $stJs .= "d.getElementById('stEmpenho').innerHTML='".$stFornecedor."';";
    } else {
        $stJs .= "f.numEmpenho.value='';";
        $stjs .= "d.getElementById('stEmpenho').innerHTML='&nbsp;';";
        if ($_REQUEST["inCodEntidade"] == "") {
            $stMensagem = "Digite um Número do Empenho para a Entidade Selecionada.";
            $stJs .= "alertaAvisoTelaPrincipal('".$stMensagem."','form','erro','" . Sessao::getId() . "', '../');";
        }
    }

    return $stJs;
}

function montaListaEmpenhos()
{
    $obLista = new Lista;
    $rsLista = new RecordSet;
    $rsLista->preenche ( Sessao::read('arEmpenhos') );
    Sessao::remove('arLiquidacoes');

    $cont=0;
    $arrayLiquidacoes = array();
    while (!$rsLista->eof()) {
        $arrayLiquidacoes[$cont] = $rsLista->arElementos[$cont];

        $rsLista->proximo();
        $cont++;
    }

    $rsLista->setPrimeiroElemento();

    $obLista->setRecordset( $rsLista );
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( 'Lista de empenhos' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 7);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Liquidação");
    $obLista->ultimoCabecalho->setWidth( 7);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome do Credor");
    $obLista->ultimoCabecalho->setWidth( 41 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Liquidação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Associado");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_nota_liquidacao]/[exercicio_liquidacao]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor_liquidacao" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlAssociado" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirEmpenhoLista');" );

    $obLista->ultimaAcao->addCampo("","&codEmpenho=[cod_empenho]&codNotaLiquidacao=[cod_nota_liquidacao]&codEntidade=[cod_entidade]&stExercicio=[exercicio]");

    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";

    Sessao::write('arLiquidacoes',$arrayLiquidacoes);

    return $stJs;

}

?>
