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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 23/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: melo $
    $Date: 2008-03-13 16:31:49 -0300 (Qui, 13 Mar 2008) $

    * Casos de uso : uc-02.03.09
*/

/*
$Log$
Revision 1.6  2006/08/09 18:13:11  jose.eduardo
Bug #6737#

Revision 1.5  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPPagEst.class.php"  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"            );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );

$obROrcamentoDespesa        = new ROrcamentoDespesa;
$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

$obRRelatorio               = new RRelatorio;
$obREmpenhoRPPagEst         = new REmpenhoRelatorioRPPagEst;
$obROrcamentoRecurso                 = new ROrcamentoRecurso;
$obROrcamentoClassificacaoDespesa    = new ROrcamentoClassificacaoDespesa;

$arFiltro = Sessao::read('filtroRelatorio');
//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidade .= $valor.",";
        $inCount++;
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
} else {
    $stEntidade .= $arFiltro['stTodasEntidades'];
}

if ( $rsTotalEntidades->getNumLinhas() == $inCount ) {
   $arFiltro['relatorio'] = "Consolidado";
} else {
   $arFiltro['relatorio'] = "";
}

switch ($_REQUEST['stCtrl']) {
    case "MontaOrgao":
        if ($_REQUEST["inExercicio"]) {
            if ($_REQUEST["inExercicio"] > '2004') {

                $obTxtOrgao = new TextBox;
                $obTxtOrgao->setRotulo              ( "Órgão"                      );
                $obTxtOrgao->setTitle               ( "Informe o órgão para filtro");
                $obTxtOrgao->setName                ( "inCodOrgaoTxt"              );
                $obTxtOrgao->setValue               ( ""                           );
                $obTxtOrgao->setSize                ( 6                            );
                $obTxtOrgao->setMaxLength           ( 3                            );
                $obTxtOrgao->setInteiro             ( true                         );
                $obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");

                $obCmbOrgao = new Select;
                $obCmbOrgao->setRotulo              ( "Órgão"                       );
                $obCmbOrgao->setName                ( "inCodOrgao"                  );
                $obCmbOrgao->setValue               ( ""                            );
                $obCmbOrgao->setStyle               ( "width: 200px"                );
                $obCmbOrgao->setCampoID             ( "num_orgao"                   );
                $obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
                $obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );

                $obTxtUnidade = new TextBox;
                $obTxtUnidade->setRotulo              ( "Unidade"                       );
                $obTxtUnidade->setTitle               ( "Informe a unidade para filtro" );
                $obTxtUnidade->setName                ( "inCodUnidadeTxt"               );
                $obTxtUnidade->setValue               ( ""                              );
                $obTxtUnidade->setSize                ( 6                               );
                $obTxtUnidade->setMaxLength           ( 3                               );
                $obTxtUnidade->setInteiro             ( true                            );

                $obCmbUnidade= new Select;
                $obCmbUnidade->setRotulo              ( "Unidade"                       );
                $obCmbUnidade->setName                ( "inCodUnidade"                  );
                $obCmbUnidade->setValue               ( ""                              );
                $obCmbUnidade->setStyle               ( "width: 200px"                  );
                $obCmbUnidade->setCampoID             ( "num_unidade"                   );
                $obCmbUnidade->setCampoDesc           ( "descricao"                     );

                $obFormulario = new Formulario;
                $obFormulario->addComponenteComposto ( $obTxtOrgao,$obCmbOrgao     );
                $obFormulario->addComponenteComposto ( $obTxtUnidade,$obCmbUnidade );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                $stJs = "d.getElementById('spnOrgaoUnidade').innerHTML = '".$stHTML."';";

                $stCombo            = "inCodOrgao";
                $stComboTxt         = "inCodOrgaoTxt";
                $stComboUnidade     = "inCodUnidade";
                $stComboUnidadeTxt  = "inCodUnidadeTxt";
                $stJs .= "limpaSelect(f.$stCombo,0); \n";
                $stJs .= "f.$stComboTxt.value=''; \n";
                $stJs .= "limpaSelect(f.$stComboUnidade,0); \n";
                $stJs .= "f.$stComboUnidadeTxt.value=''; \n";
                $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
                $stJs .= "f.$stComboUnidade.options[0] = new Option('Selecione','', 'selected');\n";

                $obREmpenhoRPPagEst->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio($_REQUEST["inExercicio"]);
                $obREmpenhoRPPagEst->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsCombo );

                $inCount = 0;
                while (!$rsCombo->eof()) {
                    $inCount++;
                    $inId   = $rsCombo->getCampo("num_orgao");
                    $stDesc = $rsCombo->getCampo("nom_orgao");
                    if( $stSelecionado == $inId )
                        $stSelected = 'selected';
                    else
                        $stSelected = '';
                    $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                    $rsCombo->proximo();
                }
            } else {
                $obTxtOrgao = new TextBox;
                $obTxtOrgao->setRotulo              ( "Órgão"                      );
                $obTxtOrgao->setTitle               ( "Informe o órgão para filtro");
                $obTxtOrgao->setName                ( "inCodOrgao"                 );
                $obTxtOrgao->setValue               ( ""                           );
                $obTxtOrgao->setSize                ( 6                            );
                $obTxtOrgao->setMaxLength           ( 3                            );
                $obTxtOrgao->setInteiro             ( true                         );

                $obTxtUnidade = new TextBox;
                $obTxtUnidade->setRotulo              ( "Unidade"                       );
                $obTxtUnidade->setTitle               ( "Informe a unidade para filtro" );
                $obTxtUnidade->setName                ( "inCodUnidade"                  );
                $obTxtUnidade->setValue               ( ""                              );
                $obTxtUnidade->setSize                ( 6                               );
                $obTxtUnidade->setMaxLength           ( 3                               );
                $obTxtUnidade->setInteiro             ( true                            );

                $obFormulario = new Formulario;
                $obFormulario->addComponente ( $obTxtOrgao   );
                $obFormulario->addComponente ( $obTxtUnidade );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                $stJs = "d.getElementById('spnOrgaoUnidade').innerHTML = '".$stHTML."';";
            }
        } else {
            $stJs = "d.getElementById('spnOrgaoUnidade').innerHTML = '';";
        }

    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "MontaUnidade":
        if ($_REQUEST["inCodOrgao"]) {
            $stCombo  = "inCodUnidade";
            $stComboTxt  = "inCodUnidadeTxt";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            $obREmpenhoRPPagEst->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgao"]);
            $obREmpenhoRPPagEst->obROrcamentoUnidadeOrcamentaria->setExercicio($_REQUEST["inExercicio"]);
            $obREmpenhoRPPagEst->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }

    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

   case "mascaraClassificacao":
        if ($_POST['inCodDespesa']) {
            //monta mascara da RUBRICA DE DESPESA
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodDespesa'] );
            $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

            //busca DESCRICAO DA RUBRICA DE DESPESA
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
            if ($stDescricao != "") {
                $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
            } else {
                $null = "&nbsp;";
                $js .= 'f.inCodDespesa.value = "";';
                $js .= 'f.inCodDespesa.focus();';
                $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
            }
            SistemaLegado::executaFrameOculto( $js );
        }
    break;

    case 'buscaFornecedor':
        if ($_POST["inCodFornecedor"] != "") {
            $obRCGM = new RCGM;
            $obRCGM->setNumCGM( $_REQUEST["inCodFornecedor"] );
            $obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCodFornecedor.value = "";';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;

    default:

        $stFiltro = "";
        $obREmpenhoRPPagEst->setFiltro      ( $stFiltro 							);
        $obREmpenhoRPPagEst->setCodEntidade ( $stEntidade 							);
        $obREmpenhoRPPagEst->setExercicio   ( $arFiltro['inExercicio']		        );
        $obREmpenhoRPPagEst->setDataInicial ( $arFiltro['stDataInicial'] 	    	);
        $obREmpenhoRPPagEst->setDataFinal   ( $arFiltro['stDataFinal'] 		        );
        $obREmpenhoRPPagEst->setCodCredor	( $arFiltro['inCodFornecedor'] 	        );
        $obREmpenhoRPPagEst->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arFiltro['inCodOrgao'] );
        $obREmpenhoRPPagEst->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade  ( $arFiltro['inCodUnidade'] );
        $obREmpenhoRPPagEst->obROrcamentoClassificacaoDespesa->setCodEstrutural ( $arFiltro['inCodDespesa'] );
        $obREmpenhoRPPagEst->obROrcamentoRecurso->setCodRecurso					( $arFiltro['inCodRecurso'] );

        if ($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao']) {
            $obREmpenhoRPPagEst->obROrcamentoRecurso->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
        }

        $obREmpenhoRPPagEst->obROrcamentoRecurso->setCodDetalhamento ( $arFiltro['inCodDetalhamento'] );
        $obREmpenhoRPPagEst->setSituacao    ( $arFiltro['inSituacao'] );
        $obREmpenhoRPPagEst->geraRecordSet	( $rsEmpenhoRPPagEst			);
        Sessao::write('filtroRelatorio', $arFiltro);
        Sessao::write('rsRecordSet', $rsEmpenhoRPPagEst);
        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmpenhoRPPagEst.php" );

    break;
}

?>
