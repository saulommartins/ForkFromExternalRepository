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
    * Página Oculto do relatório Demonstrativo de Restos a Pagar
    * Data de Criação   : 25/08/2011

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * Casos de uso : uc-02.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"    );

$stPrograma = 'DemonstrativoRestosPagar';
$pgFiltro   = 'FL'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgJs       = 'JS'.$stPrograma.'.js';
$pgList     = 'LS'.$stPrograma.'.php';
$pgGera     = 'OCGera'.$stPrograma.'.php';

$obREmpenhoEmpenho = new REmpenhoEmpenho;

$obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');
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

            $js .= "f.inCodOrgao.options[0] = new Option('Selecione','','selected'); \n";
            $js .= "f.inCodOrgaoTxt.value = ''; \n";
            $js .= "f.inCodUnidade.options[0] = new Option('Selecione','','selected'); \n";
            $js .= "f.inCodUnidadeTxt.value = ''; \n";
            $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio($_REQUEST["inExercicio"]);
            $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );
            while ( !$rsOrgao->eof() ) {
                $arFiltroNomFiltro['orgao'][$rsOrgao->getCampo( 'num_orgao' )] = $rsOrgao->getCampo( 'nom_orgao' );
                $rsOrgao->proximo();
            }
            $rsOrgao->setPrimeiroElemento();

            $inContador = 1;
            while ( !$rsOrgao->eof() ) {
                $inCodOrgao = $rsOrgao->getCampo( "num_orgao" );
                $stOrgao    = $rsOrgao->getCampo( "nom_orgao" );
                $js .= "f.inCodOrgao.options[$inContador] = new Option('".$stOrgao."','".$inCodOrgao."'); \n";
                $inContador++;
                $rsOrgao->proximo();
            }
        } else {
            $stJs = "d.getElementById('spnOrgaoUnidade').innerHTML = '';";
        }

    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "MontaUnidade":
     $arFiltroNom = array();
     $js  = "limpaSelect(f.inCodUnidade,0) \n";
     $js .= "f.inCodUnidadeTxt.value = ''; \n";
     $js .= "f.inCodUnidade.options[0] = new Option('Selecione','','selected'); \n";
     if ($_REQUEST["inCodOrgao"]) {
        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setExercicio( $_REQUEST["inExercicio"] );
        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_REQUEST["inCodOrgao"] );

        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->listar( $rsUnidade, "","", $boTransacao );
        while ( !$rsUnidade->eof() ) {
            $arFiltroNom['unidade'][$rsUnidade->getCampo( 'num_unidade' )] = $rsUnidade->getCampo( 'nom_unidade' );
            $rsUnidade->proximo();
        }
        $rsUnidade->setPrimeiroElemento();

        $inContador = 1;
        while ( !$rsUnidade->eof() ) {
            $inCodUnidade  = $rsUnidade->getCampo( "num_unidade" );
            $stUnidade     = $rsUnidade->getCampo( "nom_unidade" );
            $js .= "f.inCodUnidade.options[$inContador] = new Option('".$stUnidade."','".$inCodUnidade."'); \n";
            $inContador++;
            $rsUnidade->proximo();
        }
        Sessao::write('filtroNomRelatorio', $arFiltroNom);
    }
    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

   case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['stElementoDespesa'] );
        $js .= "f.stElementoDespesa.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.stElementoDespesa.value = "";';
            $js .= 'f.stElementoDespesa.focus();';
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'buscaFornecedor':
        if ($_POST["inCGM"] != "") {
            $obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( $_POST["inCGM"] );
            $obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCGM.value = "";';
                $js .= 'f.inCGM.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;
}

?>
