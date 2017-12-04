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
    * Página de Formulario de Vinculo entre a marca do URBEM e a do SIGA
    * Data de Criação: 18/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: OCManterTipoCombustivel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once( CAM_GPC_TCMBA_MAPEAMENTO ."TTBATipoCombustivelVinculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoVeiculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaLista($arVeiculos)
{
    if ( !is_array($arVeiculos) ) {
        $arVeiculos = array();
    }

    $rsVeiculos = new RecordSet();
    $rsVeiculos->preenche( $arVeiculos );

    $obTable = new Table();
    $obTable->setRecordset( $rsVeiculos );
    $obTable->setSummary( 'Lista de Vinculos' );

    $obTable->Head->addCabecalho( 'Tipo de Combustível - TCM', 45 );
    $obTable->Head->addCabecalho( 'Tipo de Combustível - Sistema', 45 );

    $obTable->Body->addCampo( '[cod_tipo_tcm] - [nom_tipo_tcm]', 'L' );
    $obTable->Body->addCampo( '[cod_tipo_sw] - [nom_tipo_sw]', 'L' );

    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GPC_TCMBA_INSTANCIAS."configuracao/OCManterTipoCombustivel.php?".Sessao::getId()."&inCodTipoSw=%s&inCodTipoTcm=%s', 'excluirTipoCombustivel' );", array( 'cod_tipo_sw','cod_tipo_tcm' ) );

    $obTable->montaHTML( true );

    return "$('spnLista').innerHTML = '".$obTable->getHtml()."';";
}

switch ($stCtrl) {
    case 'incluirTipoCombustivel':
        //recupera os dados da sessao
        $arTipoCombustivel = Sessao::read('arTipoCombustivel');
        $inCount = count($arTipoCombustivel);

        //Faz as verificacoes de regra
        if ($_REQUEST['inCodTipoCombustivelTcm'] == '') {
            $stMensagem = 'Selecione o Tipo TCM.';
        } elseif (!is_array($_REQUEST['inCodTipoCombustivelSwSelecionados'])) {
            $stMensagem = 'Selecione ao menos um Tipo Urbem.';
        }
        //Verifica se os dados incluidos nao existe na lista
        if ( is_array($arTipoCombustivel) ) {
            foreach ($arTipoCombustivel as $arTipoCombustivelAux) {
                if( in_array($arTipoCombustivelAux['cod_tipo_sw'], $_REQUEST['inCodTipoCombustivelSwSelecionados'] )
                 && $arTipoCombustivelAux['cod_tipo_tcm'] == $_REQUEST['inCodTipoCombustivelTcm'] ){
                    $stMensagem = 'Já existe o vínculo selecionado.';
                }
            }
        }

        //Se nao houe erros, inclui na lista
        if (!$stMensagem) {

            foreach ($_REQUEST['inCodTipoCombustivelSwSelecionados'] as $arTipoCombustivelSw) {
                $arTipoCombustivel[$inCount]['cod_tipo_tcm'] = $_REQUEST['inCodTipoCombustivelTcm'];
                $arTipoCombustivel[$inCount]['nom_tipo_tcm'] = SistemaLegado::pegaDado('descricao','tcmba.tipo_combustivel'," WHERE cod_tipo_tcm = ".$_REQUEST['inCodTipoCombustivelTcm']);
                $arTipoCombustivel[$inCount]['cod_tipo_sw'] = $arTipoCombustivelSw;
                $arTipoCombustivel[$inCount]['nom_tipo_sw'] = SistemaLegado::pegaDado('nom_combustivel','frota.combustivel',' WHERE cod_combustivel = '.$arTipoCombustivelSw);
                $inCount++;
            }

            //Grava na sessao os dados
            Sessao::write('arTipoCombustivel',$arTipoCombustivel);
            //Monta a lista com os dados gravados
            $stJs .= montaLista($arTipoCombustivel);
            //Limpa o formulario de tipo de veiculos
            $stJs .= "passaItem(document.frm.inCodTipoCombustivelSwSelecionados,document.frm.inCodTipoCombustivelSwDisponivel, 'tudo');";
        } else {
            $stJs .=  "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }

        break;

    case 'excluirTipoCombustivel':
        //recupera os dados da sessao
        $arTipoCombustivel = Sessao::read('arTipoCombustivel');

        //Percorre o array de tipos de combustivel procurando pelo dado a ser excluido
        foreach ($arTipoCombustivel as $arTipoCombustivelAux) {
            if ( !(($arTipoCombustivelAux['cod_tipo_sw'] == $_REQUEST['inCodTipoSw']) AND ($arTipoCombustivelAux['cod_tipo_tcm'] == $_REQUEST['inCodTipoTcm'])) ) {
                $arTipoCombustivelNaoExcluir[] = $arTipoCombustivelAux;
            }
        }

        //Grava os dados sem o tipo excluido
        Sessao::write('arTipoCombustivel',$arTipoCombustivelNaoExcluir);

        //Monta a lista com os dados gravados
        $stJs .= montaLista($arTipoCombustivelNaoExcluir);

        break;

    case 'montaLista':
        //Recupera os dados ja incluidos na base
        $obTTipoCombustivelVinculo = new TTBATipoCombustivelVinculo();
        $obTTipoCombustivelVinculo->recuperaTipoCombustivelVinculo($rsTipoCombustivelVinculo);
        //Preenche a sessao com os dados vindos do banco
        $inCount = 0;
        while ( !$rsTipoCombustivelVinculo->eof() ) {
            $arTipoCombustivel[$inCount]['cod_tipo_tcm'] = $rsTipoCombustivelVinculo->getCampo('cod_tipo_tcm');
            $arTipoCombustivel[$inCount]['nom_tipo_tcm'] = $rsTipoCombustivelVinculo->getCampo('nom_tipo_tcm');
            $arTipoCombustivel[$inCount]['cod_tipo_sw'] = $rsTipoCombustivelVinculo->getCampo('cod_tipo_sw');
            $arTipoCombustivel[$inCount]['nom_tipo_sw'] = $rsTipoCombustivelVinculo->getCampo('nom_tipo_sw');
            $inCount++;
            $rsTipoCombustivelVinculo->proximo();
        }

        //Grava os dados na sessao
        Sessao::write('arTipoCombustivel',$arTipoCombustivel);

        //Monta a lista
        $stJs = montaLista($arTipoCombustivel);

        break;

    case 'limparFormulario':
        Sessao::remove('arTipoCombustivel');

        break;
}

echo $stJs;

?>
