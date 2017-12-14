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
    * Página de Formulario para inclusao de Tablela de Conversão
    * Data de Criacao: 11/09/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Vitor Hugo
    * @ignore

    * $Id: OCManterTabela.php 60881 2014-11-20 16:38:15Z franver $

* Casos de uso: uc-05.03.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRTabelaConversao.class.php" );

$obErro = new Erro;

//Define o nome dos arquivos PHP
$stPrograma = "DetalhamentoReceitas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function montaListaConversaoValores(&$rsListaConversaoValores)
{
    
    $rsListaConversaoValores->setPrimeiroElemento();

    if ( !$rsListaConversaoValores->eof() ) {
        if ($rsListaConversaoValores->getCampo("parametro_1") == "") $rsListaConversaoValores->setCampo("parametro_1", "&nbsp;");
        if ($rsListaConversaoValores->getCampo("parametro_2") == "") $rsListaConversaoValores->setCampo("parametro_2", "&nbsp;");
        if ($rsListaConversaoValores->getCampo("parametro_3") == "") $rsListaConversaoValores->setCampo("parametro_3", "&nbsp;");
        if ($rsListaConversaoValores->getCampo("parametro_4") == "") $rsListaConversaoValores->setCampo("parametro_4", "&nbsp;");        

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo ("Listas de Valores");
        $obLista->setRecordset( $rsListaConversaoValores );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Condicao 1");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Condicao 2" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Condicao 3" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Condicao 4");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "parametro_1"  );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "parametro_2"  );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "parametro_3"  );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "parametro_4"  );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "DIREITA"  );
        $obLista->ultimoDado->setCampo( "valor"  );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo( "1","linha" );        
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alterarConversaoValores');  " );
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo( "1","linha" );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirConversaoValores'); " );
        $obLista->commitAcao();
        
        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);

        $stJs  = "parent.frames['telaPrincipal'].document.frm.parametro_1.focus(); ";        
        $stJs .= "d.getElementById('spnListaValores').innerHTML = '".$stHTML."';";
        echo $stJs;
    }
}

function limparCondicao()
{
    $stJs = "parent.frames['telaPrincipal'].document.frm.parametro_1.value=''; ";
    $stJs .= "parent.frames['telaPrincipal'].document.frm.parametro_2.value=''; ";
    $stJs .= "parent.frames['telaPrincipal'].document.frm.parametro_3.value=''; ";
    $stJs .= "parent.frames['telaPrincipal'].document.frm.parametro_4.value=''; ";
    $stJs .= "parent.frames['telaPrincipal'].document.frm.valor.value=''; ";
    echo $stJs;
}

switch ($stCtrl) {
    case "preencheTabela":
        $stJs = "limpaSelect( f.cmbTabelas, 1 ); \n";
        $stJs .= "f.cmbTabelas[0] = new Option('Selecione', '', 'selected');\n";
        if ($_GET["inExercicio"]) {
            $stFiltro = " WHERE exercicio = '".$_GET["inExercicio"]."'";
            $obTARRTabelaConversao = new TARRTabelaConversao;
            $obTARRTabelaConversao->recuperaListaTabelaConversao( $rsListaTabelas, $stFiltro );
            $inContador = 1;
            if ( !$rsListaTabelas->Eof() ) {
                $stJs .= "f.cmbTabelas.options[$inContador] = new Option('Todos','-666'); \n";
                $inContador++;
            }

            while ( !$rsListaTabelas->eof() ) {
                $stJs .= "f.cmbTabelas.options[$inContador] = new Option('".$rsListaTabelas->getCampo("cod_tabela")."-".$rsListaTabelas->getCampo("nome_tabela")."','".$rsListaTabelas->getCampo("cod_tabela")."'); \n";
                $rsListaTabelas->proximo();
                $inContador++;
            }
        }

        echo $stJs;
        break;

    case "incluirConversaoValores":
        
        if ($_REQUEST['valor'] == '') {
            $stMensagem = 'O campo Valor deve ser informado';
            $stJs .= "alertaAviso('".$stMensagem."!','frm','erro','".Sessao::getId()."'); \n";
            echo $stJs;
        }else{
            if (!$_REQUEST['parametro_1']) $_REQUEST['parametro_1'] = "&nbsp;";
            if (!$_REQUEST['parametro_2']) $_REQUEST['parametro_2'] = "&nbsp;";
            if (!$_REQUEST['parametro_3']) $_REQUEST['parametro_3'] = "&nbsp;";
            if (!$_REQUEST['parametro_4']) $_REQUEST['parametro_4'] = "&nbsp;";
    
            $arConvVal5 = Sessao::read( 'convval5' );
            $arConvVal5[] = array( "stDescricao"    => $_REQUEST['stDescricao'],
                                    "stExercicio"   => $_REQUEST['stExercicio'],
                                    "parametro_1"   => $_REQUEST['parametro_1'],
                                    "parametro_2"   => $_REQUEST['parametro_2'],
                                    "parametro_3"   => $_REQUEST['parametro_3'],
                                    "parametro_4"   => $_REQUEST['parametro_4'],
                                    "valor"         => $_REQUEST['valor'],
                                    "linha"         => count( $arConvVal5 )
                            );
    
            Sessao::write( 'convval5', $arConvVal5 );
            $rsListaConversaoValores = new RecordSet;
            $rsListaConversaoValores->preenche( $arConvVal5 );
    
            $stJs .= limparCondicao();
            $stJs .= montaListaConversaoValores( $rsListaConversaoValores );
            }
        return $stJs;
    break;

    case "excluirConversaoValores":
        $arNovaListaConversaoValores = array();
        $inContLinha = 0;

        $arConvVal5 = Sessao::read( 'convval5' );
        foreach ($arConvVal5 as $inChave => $arValores) {
            if ($inChave != $_GET['linha']) {
                $arValores["linha"] = $inContLinha;
                $arNovaListaConversaoValores[] = $arValores;
                $inContLinha++;
            }
        }

        Sessao::write( 'convval5', $arNovaListaConversaoValores );
        $rsListaConversaoValores = new RecordSet;
        $rsListaConversaoValores->preenche( $arNovaListaConversaoValores );

        if ($rsListaConversaoValores->eof())
        echo "d.getElementById('spnListaValores').innerHTML = '';";

        $stJs = montaListaConversaoValores( $rsListaConversaoValores );
        echo $stJs;
    break;

    case "alterarConversaoValores":
        $arConvVal5 = Sessao::read( 'convval5' );
        foreach ($arConvVal5 as $inChave => $arValores) {            
            if ( $arValores['linha'] == $_REQUEST['linha']) {           
                $stJs  = " jQuery('input[name=\"parametro_1\"]').val('".str_replace("&nbsp;", "", $arValores['parametro_1'])."'); ";
                $stJs .= " jQuery('input[name=\"parametro_2\"]').val('".str_replace("&nbsp;", "", $arValores['parametro_2'])."'); ";
                $stJs .= " jQuery('input[name=\"parametro_3\"]').val('".str_replace("&nbsp;", "", $arValores['parametro_3'])."'); ";
                $stJs .= " jQuery('input[name=\"parametro_4\"]').val('".str_replace("&nbsp;", "", $arValores['parametro_4'])."'); ";
                $stJs .= " jQuery('input[name=\"valor\"]').val('".str_replace("&nbsp;", "", $arValores['valor'])."'); ";
            }
        }
        //Passando o numero da linha para verificar com o registro do array que esta salvo na sessao
        $stJs .= " jQuery('#frm').append('<input type=\"hidden\" name=\"hdnLinha\" value=\"".$_REQUEST['linha']."\" />'); ";
        $stJs .= " jQuery(':button[name=\"stIncluirCondicao\"]').val('Alterar'); ";        
        $stJs .= " jQuery(':button[name=\"stIncluirCondicao\"]').attr(\"onclick\",\"JavaScript:montaParametrosGET( 'alterarLista','');\" ); ";
        
        echo $stJs;
    
    break;

    case 'alterarLista':

        if (empty($_REQUEST['valor'])) {
            echo "alertaAviso('O campo Valor deve ser informado.','n_incluir','erro','&iURLRandomica=20141112165504.515');";
        } else {

            if (!$_REQUEST['parametro_1']) $_REQUEST['parametro_1'] = "&nbsp;";
            if (!$_REQUEST['parametro_2']) $_REQUEST['parametro_2'] = "&nbsp;";
            if (!$_REQUEST['parametro_3']) $_REQUEST['parametro_3'] = "&nbsp;";
            if (!$_REQUEST['parametro_4']) $_REQUEST['parametro_4'] = "&nbsp;";


            $arConvVal5 = Sessao::read( 'convval5' );
            $arConvVal5[$_REQUEST['hdnLinha']] = array( "stDescricao"   => $_REQUEST['stDescricao'],
                                                        "stExercicio"   => $_REQUEST['stExercicio'],
                                                        "parametro_1"   => $_REQUEST['parametro_1'],
                                                        "parametro_2"   => $_REQUEST['parametro_2'],
                                                        "parametro_3"   => $_REQUEST['parametro_3'],
                                                        "parametro_4"   => $_REQUEST['parametro_4'],
                                                        "valor"         => $_REQUEST['valor'],
                                                        "linha"         => $_REQUEST['hdnLinha']
                                                    );

            Sessao::write( 'convval5', $arConvVal5 );
            $rsListaConversaoValores = new RecordSet;
            $rsListaConversaoValores->preenche( $arConvVal5 );

            echo " jQuery(':button[name=\"stIncluirCondicao\"]').val('Incluir'); ";        
            echo " jQuery(':button[name=\"stIncluirCondicao\"]').attr(\"onclick\",\"JavaScript:montaParametrosGET( 'incluirConversaoValores','');\" ); ";

            $stJs .= limparCondicao();
            $stJs .= montaListaConversaoValores( $rsListaConversaoValores );
        }
        
        return $stJs;        

    break;

    case "montaListaConversaoValores":

         $rsListaConversaoValores = new RecordSet;
         $arNovaListaConversaoValores = array();
         $inContLinha = 0;

         $arConvVal5 = Sessao::read( 'convval5' );
         foreach ($arConvVal5 as $inChave => $arValores) {
            $arValores["linha"] = $inContLinha;
            $arNovaListaConversaoValores[] = $arValores;
            $inContLinha++;
         }

         Sessao::write( 'convval5', $arNovaListaConversaoValores );
         $rsListaConversaoValores->preenche( $arNovaListaConversaoValores );

         $rsListaConversaoValores->setPrimeiroElemento();

         if ( !$rsListaConversaoValores->eof() ) {
         if ($rsListaConversaoValores->getCampo("parametro_1") == "") $rsListaConversaoValores->setCampo("parametro_1", "&nbsp;");
         if ($rsListaConversaoValores->getCampo("parametro_2") == "") $rsListaConversaoValores->setCampo("parametro_2", "&nbsp;");
         if ($rsListaConversaoValores->getCampo("parametro_3") == "") $rsListaConversaoValores->setCampo("parametro_3", "&nbsp;");
         if ($rsListaConversaoValores->getCampo("parametro_4") == "") $rsListaConversaoValores->setCampo("parametro_4", "&nbsp;");
         }
         $stJs = montaListaConversaoValores( $rsListaConversaoValores );
         echo $stJs;

    break;

    case "limparCondicao":

         $stJs .= limparCondicao();

         return $stJs;

    break;

    case "limparListaValores":
        Sessao::write( 'convval5', array() );
    break;
}
?>
