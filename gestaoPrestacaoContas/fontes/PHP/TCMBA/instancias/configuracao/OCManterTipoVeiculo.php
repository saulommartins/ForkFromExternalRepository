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

    * $Id: OCManterTipoVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once( CAM_GPC_TCMBA_MAPEAMENTO ."TTBATipoVeiculoVinculo.class.php" );

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

    $obTable->Head->addCabecalho( 'Tipo de Veículo - TCM', 45 );
    $obTable->Head->addCabecalho( 'Tipo de Veículo - Sistema', 45 );

    $obTable->Body->addCampo( '[cod_tipo_tcm] - [nom_tipo_tcm]', 'L' );
    $obTable->Body->addCampo( '[cod_tipo_sw] - [nom_tipo_sw]', 'L' );

    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GPC_TCMBA_INSTANCIAS."configuracao/OCManterTipoVeiculo.php?".Sessao::getId()."&inCodTipoSw=%s&inCodTipoTcm=%s', 'excluirTipoVeiculo' );", array( 'cod_tipo_sw','cod_tipo_tcm' ) );

    $obTable->montaHTML( true );

    return "$('spnLista').innerHTML = '".$obTable->getHtml()."';";
}

switch ($stCtrl) {
    case 'incluirTipoVeiculo':
        //recupera os dados da sessao
        $arTipoVeiculo = Sessao::read('arTipoVeiculo');
        $inCount = count($arTipoVeiculo);

        //Faz as verificacoes de regra
        if ($_REQUEST['inCodTipoVeiculoTcm'] == '') {
            $stMensagem = 'Selecione o Tipo TCM.';
        } elseif (!is_array($_REQUEST['inCodTipoVeiculoSwSelecionados'])) {
            $stMensagem = 'Selecione ao menos um Tipo Urbem.';
        }
        //Verifica se os dados incluidos nao existe na lista
        if ( is_array($arTipoVeiculo) ) {
            foreach ($arTipoVeiculo as $arTipoVeiculoAux) {
                if ( in_array($arTipoVeiculoAux['cod_tipo_sw'],$_REQUEST['inCodTipoVeiculoSwSelecionados']) ) {
                    $stMensagem = 'Já existe o tipo de veículo vinculado para o tipo selecionado.';
                    break;
                }
            }
        }

        //Se nao houve erros, inclui na lista
        if (!$stMensagem) {

            foreach ($_REQUEST['inCodTipoVeiculoSwSelecionados'] as $arTipoVeiculoSw) {
                $arTipoVeiculo[$inCount]['cod_tipo_tcm'] = $_REQUEST['inCodTipoVeiculoTcm'];
                $arTipoVeiculo[$inCount]['nom_tipo_tcm'] = SistemaLegado::pegaDado('descricao','tcmba.tipo_veiculo'," WHERE cod_tipo_tcm = ".$_REQUEST['inCodTipoVeiculoTcm']);
                $arTipoVeiculo[$inCount]['cod_tipo_sw'] = $arTipoVeiculoSw;
                $arTipoVeiculo[$inCount]['nom_tipo_sw'] = SistemaLegado::pegaDado('nom_tipo','frota.tipo_veiculo',' WHERE cod_tipo = '.$arTipoVeiculoSw);
                $inCount++;
            }

            //Grava na sessao os dados
            Sessao::write('arTipoVeiculo',$arTipoVeiculo);
            //Monta a lista com os dados gravados
            $stJs .= montaLista($arTipoVeiculo);
            //Limpa o formulario de tipo de veiculos
            $stJs .= "passaItem(document.frm.inCodTipoVeiculoSwSelecionados,document.frm.inCodTipoVeiculoSwDisponivel, 'tudo');";
        } else {
            $stJs .=  "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }

        break;

    case 'excluirTipoVeiculo':
        //recupera os dados da sessao
        $arTipoVeiculo = Sessao::read('arTipoVeiculo');

        //Percorre o array de tipos de veiculo procurando pelo dado a ser excluido
        foreach ($arTipoVeiculo as $arTipoVeiculoAux) {
            if ( !(($arTipoVeiculoAux['cod_tipo_sw'] == $_REQUEST['inCodTipoSw']) AND ($arTipoVeiculoAux['cod_tipo_tcm'] == $_REQUEST['inCodTipoTcm'])) ) {
                $arTipoVeiculoNaoExcluir[] = $arTipoVeiculoAux;
            }
        }

        //Grava os dados sem o tipo excluido
        Sessao::write('arTipoVeiculo',$arTipoVeiculoNaoExcluir);

        //Monta a lista com os dados gravados
        $stJs .= montaLista($arTipoVeiculoNaoExcluir);

        break;

    case 'montaLista':
        //Recupera os dados ja incluidos na base
        $obTTipoVeiculoVinculo = new TTBATipoVeiculoVinculo();
        $obTTipoVeiculoVinculo->recuperaTipoVeiculoVinculo($rsTipoVeiculoVinculo);
        //Preenche a sessao com os dados vindos do banco
        $inCount = 0;
        while ( !$rsTipoVeiculoVinculo->eof() ) {
            $arTipoVeiculo[$inCount]['cod_tipo_tcm'] = $rsTipoVeiculoVinculo->getCampo('cod_tipo_tcm');
            $arTipoVeiculo[$inCount]['nom_tipo_tcm'] = $rsTipoVeiculoVinculo->getCampo('nom_tipo_tcm');
            $arTipoVeiculo[$inCount]['cod_tipo_sw'] = $rsTipoVeiculoVinculo->getCampo('cod_tipo_sw');
            $arTipoVeiculo[$inCount]['nom_tipo_sw'] = $rsTipoVeiculoVinculo->getCampo('nom_tipo_sw');
            $inCount++;
            $rsTipoVeiculoVinculo->proximo();
        }

        //Grava os dados na sessao
        Sessao::write('arTipoVeiculo',$arTipoVeiculo);

        //Monta a lista
        $stJs = montaLista($arTipoVeiculo);

        break;

    case 'limparFormulario':
        Sessao::remove('arTipoVeiculo');

        break;
}

echo $stJs;

?>
