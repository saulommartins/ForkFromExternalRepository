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
    * Classe Oculta da Configuração Anexo I
    * Data de Criação   : 25/07/2011

    * @author Desenvolvedor: Davi Ritter Aroldi
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "ManterRGFAnexo1";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

function preencheListaDespesas()
{
    $arTMP = Sessao::read('arListaDespesa');

    if (count($arTMP) > 0) {
        if ($_REQUEST['inId']) {
            foreach ($arTMP as $despesa) {
                if ($despesa['inId'] == $_REQUEST['inId']) {
                    return "alertaAvisoTelaPrincipal('Essa despesa já está na lista!','form','erro','".Sessao::getId()."');";
                }
            }
        } else {
            foreach ($arTMP as $despesa) {
                if ($despesa['stDescricao'] == $_REQUEST['stDescricao']) {
                    return "alertaAvisoTelaPrincipal('Essa despesa já está na lista!','form','erro','".Sessao::getId()."');";
                }
            }
        }
    }
    if (!$_REQUEST['stDescricao']) {
        return "alertaAvisoTelaPrincipal('Campo descrição em branco!','form','erro','".Sessao::getId()."');";
    }
    $arTMP2 = array(
                'inId' => $_REQUEST['inId'],
                'stDescricao' => $_REQUEST['stDescricao'],
                'nuQuadrimestreValor1' => $_REQUEST['nuQuadrimestreValor1'] ? $_REQUEST['nuQuadrimestreValor1'] : 0.00,
                'nuQuadrimestreValor2' => $_REQUEST['nuQuadrimestreValor2'] ? $_REQUEST['nuQuadrimestreValor2'] : 0.00,
                'nuQuadrimestreValor3' => $_REQUEST['nuQuadrimestreValor3'] ? $_REQUEST['nuQuadrimestreValor3'] : 0.00,
                'stExercicio' => $_REQUEST['stExercicio'],
             );
    $arTMP[] = $arTMP2;

    Sessao::write('arListaDespesa', $arTMP);
}

function montaListaDespesas()
{
    $stJs = '';

    $rsLista = new RecordSet;
    $rsLista->preenche(Sessao::read('arListaDespesa'));

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quadrimestre 1");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quadrimestre 2");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quadrimestre 3");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stDescricao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuQuadrimestreValor1" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuQuadrimestreValor2" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuQuadrimestreValor3" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('exlcuirDespesa');" );
    $obLista->ultimaAcao->addCampo("","&inId=[inId]&stDescricao=[stDescricao]");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\'",$stHTML );
    $stHTML = str_replace( "\\\'","\'",$stHTML );

    $stJs = "d.getElementById('spnListaDespesas').innerHTML = '".$stHTML."';";

    return $stJs;
}

function limparDespesa()
{
    $stJs = '';
    $stJs .= "d.getElementById('stDescricao').value = '';";
    $stJs .= "d.getElementById('nuQuadrimestreValor1').value = '';";
    $stJs .= "d.getElementById('nuQuadrimestreValor2').value = '';";
    $stJs .= "d.getElementById('nuQuadrimestreValor3').value = '';";

    return $stJs;
}

function removeDespesa()
{
    $arTMP = Sessao::read('arListaDespesa');
    $arTMP2 = array();

    if ($_REQUEST['inId'] && count($arTMP) > 0) {
        foreach ($arTMP as $despesa) {
            if ($despesa['inId'] != $_REQUEST['inId']) {
                $arTMP2[] = $despesa;
            }
        }
    } elseif (count($arTMP) > 0) {
        foreach ($arTMP as $despesa) {
            if ($despesa['stDescricao'] != $_REQUEST['stDescricao']) {
                $arTMP2[] = $despesa;
            }
        }
    }

    Sessao::write('arListaDespesa', $arTMP2);
}

$stJs = '';

switch ($_REQUEST['stCtrl']) {
    case 'incluirDespesa':
        if (preencheListaDespesas()) {
            $stJs .= preencheListaDespesas();
        } else {
            $stJs .= limparDespesa();
            $stJs .= montaListaDespesas();
        }
        break;
    case 'limparDespesa':
        $stJs .= limparDespesa();
        break;
    case 'exlcuirDespesa':
        removeDespesa();
        $stJs .= montaListaDespesas();
        break;
    case 'montaListaDespesas':
        $stJs .= limparDespesa();
        $stJs .= montaListaDespesas();
        break;

}

echo $stJs;
