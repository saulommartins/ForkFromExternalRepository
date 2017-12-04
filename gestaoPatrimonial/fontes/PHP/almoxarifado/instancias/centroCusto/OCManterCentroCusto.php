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
    * Página de Oculto de Centro de Custo
    * Data de Criação   : 22/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * Casos de uso: uc-03.03.07

    $Id: OCManterCentroCusto.php 60951 2014-11-26 11:56:53Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCentroDeCustos.class.php"    );
include_once( CAM_GF_ORC_NEGOCIO . "ROrcamentoDespesa.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCentroCusto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RAlmoxarifadoCentroDeCustos;
$obROrcamentoDespesa = new ROrcamentoDespesa;

function montaListaDotacoes($arRecordSet , $boExecuta = true)
{
        if (count($arRecordSet) == 0) {
                $arRecordSet = array();
        }

        $rsDotacoes = new RecordSet;
        $rsDotacoes->preenche( $arRecordSet );

        $obLista = new Lista;
        $obLista->setTitulo('');
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsDotacoes );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Dotação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição");
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_despesa" );
        $obLista->ultimoDado->setTitle( "dotacao." );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "descricao" );
        $obLista->ultimoDado->setTitle( "dotacao." );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirDotacao();" );
        $obLista->ultimaAcao->addCampo("1","id");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto("d.getElementById('spnListaDotacoes').innerHTML = '".$stHTML."';d.getElementById('inCodDotacao').value = '';d.getElementById('stNomDotacao').innerHTML = '&nbsp;'  ");
        } else {
            return $stHTML;
        }
}
switch ($stCtrl) {
    case 'incluirDotacao':
        
        if(count(Sessao::read('arDotacoes')) > 0){
          foreach ( Sessao::read('arDotacoes') as $arTEMP ) {
            if ($arTEMP['cod_despesa'] == $_POST['inCodDotacao']) {
                $boDotacaoRepetida = true ;
                break;
            }
          }        
        }

        if (!$boDotacaoRepetida) {
                $arTemp = array();
            $obROrcamentoDespesa->setCodDespesa( $_POST['inCodDotacao'] );
            $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obROrcamentoDespesa->listarDespesa($rsDespesa);
            $stDescricao = $rsDespesa->getCampo('descricao');
            $stDotacao   = $rsDespesa->getCampo('dotacao');

            $arDotacoes = Sessao::read('arDotacoes');
            $inCount = count($arDotacoes);

            $arTemp['id']          = $inCount+1;
            $arTemp['cod_despesa'] = $_POST['inCodDotacao'];
            $arTemp['descricao']   = $stDescricao;
            $arTemp['dotacao']     = $stDotacao;

            $arDotacoes[] = $arTemp;

            Sessao::write('arDotacoes', $arDotacoes);
            montaListaDotacoes( Sessao::read('arDotacoes') );

        }
    break;
    case 'excluirDotacao':
        $arTEMP  = array();
        $inCount = 0;
        foreach ( Sessao::read('arDotacoes') as $key => $value ) {
            if ( ($key+1) != $_REQUEST['id'] ) {
                $arTEMP[$inCount]['id']          = $inCount+1;
                $arTEMP[$inCount]['cod_despesa'] = $value['cod_despesa'];
                $arTEMP[$inCount]['descricao']   = $value['descricao'];
                $arTEMP[$inCount]['dotacao']     = $value['dotacao'];
                $inCount++;
            }
        }
        Sessao::write('arDotacoes', $arTEMP);
        montaListaDotacoes( Sessao::read('arDotacoes') );
    break;
    case 'montaListaDotacoes':
        montaListaDotacoes( Sessao::read('arDotacoes') );
    break;
 case 'buscaDespesa':

    if ($_POST["inCodDespesa"] != "" and $_POST["inCodEntidade"] != "") {

        $obROrcamentoDespesa->setCodDespesa( $_POST["inCodDespesa"] );
        $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_POST["inCodEntidade"] );
        $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obROrcamentoDespesa->listar( $rsDespesa );

        $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        if (!$stNomDespesa) {
            $js .= 'f.inCodDespesa.value = "";';
            $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodDespesa.focus();';
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodDespesa"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "'.$stNomDespesa.'";';
        }
    } else $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;
}

if ( $stJs )
    SistemaLegado::executaFrameOculto($stJs);

?>
