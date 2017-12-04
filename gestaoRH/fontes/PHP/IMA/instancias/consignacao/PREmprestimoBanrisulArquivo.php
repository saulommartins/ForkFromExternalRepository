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
 * Filtro de empréstimos Banrisul
 * Data de Criação   : 01/09/2009

 * @author Analista      Dagine Rodrigues Vieira
 * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_ARQUIVO_TEXTO;
include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConsignacaoEmprestimoBanrisul.class.php';
include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConsignacaoEmprestimoBanrisulConfiguracao.class.php';
include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConsignacaoEmprestimoBanrisulErro.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "EmprestimoBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgProcArq  = "PR".$stPrograma."Arquivo.php";
$pgFormImp  = "FMImportar".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
Sessao::setTrataExcecao(true);
$obErro = new Erro();
$obTIMAConsignacaoEmprestimoBanrisul = new TIMAConsignacaoEmprestimoBanrisul;
$obTIMAConsignacaoEmprestimoBanrisulConfiguracao = new TIMAConsignacaoEmprestimoBanrisulConfiguracao;

$obTIMAConsignacaoEmprestimoBanrisulConfiguracao->setDado('cod_periodo_movimentacao', $_REQUEST['inCodPeriodoMovimentacao']);
$obTIMAConsignacaoEmprestimoBanrisulConfiguracao->recuperaPorChave($rsConfigucaracao);

$obTIMAConsignacaoEmprestimoBanrisul->setDado('cod_periodo_movimentacao', $_REQUEST['inCodPeriodoMovimentacao']);
$stFiltro = Sessao::read('stFiltro');
$obTIMAConsignacaoEmprestimoBanrisul->recuperaRelacionamento($rsRegistro, $stFiltro , 'num_linha');

$stFiltro = " WHERE consignacao_emprestimo_banrisul.cod_periodo_movimentacao = ".$_REQUEST['inCodPeriodoMovimentacao'];

/*Código abaixo para testar a quantidade de contratos a ser inserido*/
$arContratos = Sessao::read("arContratos");
if (!empty($arContratos)) {
    foreach ($arContratos as $chave => $arValor) {
        $stListaContratos.= $arValor['cod_contrato'].",";
    }
    $stListaContratos = substr($stListaContratos, 0, -1);
    $stFiltro .= " AND consignacao_emprestimo_banrisul.cod_contrato in (".$stListaContratos.")";
}

/*Código abaixo para testar se o filtro for diferente do Geral*/
if ($_REQUEST['stCadastro'] == 'Pensionistas') { //E
    $stFiltro.=" AND recuperarSituacaoDoContrato(consignacao_emprestimo_banrisul.cod_contrato,".$_REQUEST['inCodPeriodoMovimentacao'].",'') ='E' \n";
} elseif ($_REQUEST['stCadastro'] == 'Aposentados') { //P
    $stFiltro.=" AND recuperarSituacaoDoContrato(consignacao_emprestimo_banrisul.cod_contrato,".$_REQUEST['inCodPeriodoMovimentacao'].",'') ='P' \n";
 } elseif ($_REQUEST['stCadastro'] == 'Rescindidos') { //R
    $stFiltro.=" AND recuperarSituacaoDoContrato(consignacao_emprestimo_banrisul.cod_contrato,".$_REQUEST['inCodPeriodoMovimentacao'].",'') ='R' \n";
 } elseif ($_REQUEST['stCadastro'] == 'Ativos') { //A
    $stFiltro.=" AND recuperarSituacaoDoContrato(consignacao_emprestimo_banrisul.cod_contrato,".$_REQUEST['inCodPeriodoMovimentacao'].",'') ='A' \n";
 }

$obTIMAConsignacaoEmprestimoBanrisul->recuperaSomatorio($rsRodape, $stFiltro);

if ($rsRodape->getNumLinhas() > 0) {
    //implementar mensagem de erro se rsRodape vier zerado

    Sessao::encerraExcecao();

    $stMesAno = substr($rsConfigucaracao->getCampo('ano_mes'), 4, 2) . substr($rsConfigucaracao->getCampo('ano_mes'), 0,4);
    $stFiller = $rsRegistro->getCampo('filler');

    /*
     * CABECALHO
     */
    $stCabecalho = 'BCER00';
    $stCabecalho.= str_pad($rsConfigucaracao->getCampo('cod_convenio'), 8, '0',STR_PAD_LEFT);
    $stCabecalho.= str_pad($rsConfigucaracao->getCampo('nom_convenio'), 50, ' ');
    $stCabecalho.= $rsConfigucaracao->getCampo('ano_mes');
    //filler no fim da linha
    $stCabecalho = str_pad($stCabecalho, 199, ' ');
    $stCabecalho.= $stFiller;
    /*
     * REGISTRO
     */
    $arRegistro = array();
    while (!$rsRegistro->eof()) {
        $rsRegistro->getCampo('num_linha');
        $rsRegistro->getCampo('cod_periodo_movimentacao');
        $rsRegistro->getCampo('cod_contrato');
        $stRegistro  = 'BCER10';
        $stRegistro .= str_pad($rsRegistro->getCampo('oa'),6, '0',STR_PAD_LEFT);
        $stRegistro .= str_pad($rsRegistro->getCampo('matricula'),15, '0',STR_PAD_LEFT);
        //$stRegistro .= str_pad('0',15, '0',STR_PAD_LEFT);
        $stRegistro .= str_pad($rsRegistro->getCampo('cpf'),11, '0',STR_PAD_LEFT);
        $stRegistro .= str_pad($rsRegistro->getCampo('nom_funcionario'),35, ' ');
        $stRegistro .= str_pad($rsRegistro->getCampo('cod_canal'),5, '0',STR_PAD_LEFT);
        $stRegistro .= str_pad($rsRegistro->getCampo('nro_contrato'),20, ' ');
        $stRegistro .= str_pad($rsRegistro->getCampo('prestacao'),7, ' ');
        $stRegistro .= str_pad($rsRegistro->getCampo('val_consignar'),15, '0',STR_PAD_LEFT);
        $stRegistro .= str_pad($rsRegistro->getCampo('val_consignado'),15, '0',STR_PAD_LEFT);
        $stRegistro .= str_pad($rsRegistro->getCampo('cod_motivo_rejeicao'),2, ' ');
        //filler no fim da linha
        $stRegistro  = str_pad($stRegistro, 199, ' ');
        $stRegistro .= $rsRegistro->getCampo('filler');
        $arRegistro[] = $stRegistro;
        $rsRegistro->proximo();
    }

    /*
     * RODAPE
     */
    $stRodape  = 'BCER99';
    $stRodape .= str_pad($rsRodape->getCampo('num_registros')+2, 15, '0',STR_PAD_LEFT);
    /*$stRodape .= str_pad($rsRodape->getCampo('sum_consignado'), 15, '0',STR_PAD_LEFT);
    --Necessário alterar a variável sum_consignado para sum_consignar, pois não estava somando os valores*/
    $stRodape .= str_pad($rsRodape->getCampo('sum_consignar'), 15, '0',STR_PAD_LEFT);

    //filler no fim da linha
    $stRodape  = str_pad($stRodape, 199, ' ');
    $stRodape .= $stFiller;

    /*
     * ARQUIVO
     */
    $stArquivo = (ini_get('upload_tmp_dir')=== null ? ini_get('upload_tmp_dir') : '/tmp').'/BCER'.$stMesAno.'.TXT';
    $obArquivo = new ArquivoTexto($stArquivo);
    $obArquivo->addLinha($stCabecalho);
    foreach ($arRegistro as $stRegistro) {
        $obArquivo->addLinha($stRegistro);
    }
    $obArquivo->addLinha($stRodape);
    $obErro = $obArquivo->Gravar();
}else{
    $obErro->setDescricao("Nenhum registro encontrado!");
}

if (!$obErro->ocorreu()) {
    $obArquivo->Show();
} else {
    echo $obErro->getDescricao();
}

?>
