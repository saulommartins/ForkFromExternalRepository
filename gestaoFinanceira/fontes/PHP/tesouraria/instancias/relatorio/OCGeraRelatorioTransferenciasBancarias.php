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
    * Data de Criação   : 30/11/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31130 $
    $Name$
    $Autor:$
    $Date: 2008-01-15 11:46:16 -0200 (Ter, 15 Jan 2008) $

	$Id: OCGeraRelatorioTransferenciasBancarias.php 60879 2014-11-20 13:53:10Z michel $

    * Casos de uso: uc-02.04.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF.'RRelatorio.class.php';

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroEntidades = Sessao::read('filtroEntidades');
$inCodigoUFSistema = SistemaLegado::pegaConfiguracao('cod_uf');

// Adicionar logo nos relatorios
if (count($arFiltro['inCodigoEntidadesSelecionadas']) == 1) {
    $obRRelatorio->setCodigoEntidade   ($arFiltro['inCodigoEntidadesSelecionadas'][0]);
    $obRRelatorio->setExercicioEntidade(Sessao::getExercicio());
}

$obRRelatorio->setExercicio     (Sessao::getExercicio());
$obRRelatorio->recuperaCabecalho($arConfiguracao);
$obPDF->setModulo            ('Relatorio');
$obPDF->setTitulo            ('Dados para Transferências Bancárias');
$obPDF->setSubTitulo         ('Exercício - '.Sessao::getExercicio());
$obPDF->setUsuario           (Sessao::getUsername());
$obPDF->setEnderecoPrefeitura($arConfiguracao );

foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $inCodEntidade) {
    $arNomEntidade[] = $arFiltroEntidades[$inCodEntidade];
}

$obPDF->addFiltro('Entidades Relacionadas', $arNomEntidade);
$obPDF->addFiltro('Exercício '            ,  $arFiltro['stExercicio']);

if ($arFiltro['stDataInicial']) {
    $obPDF->addFiltro('Periodicidade ', $arFiltro['stDataInicial'].' até '.$arFiltro['stDataFinal']);
}

if (($arFiltro['inContaBancoInicial'] != 0) || ($arFiltro['inContaBancoFinal'] != 0)) {
    $obPDF->addFiltro('Conta Banco: ',  $arFiltro['inContaBancoInicial'].' até '.$arFiltro['inContaBancoFinal']);
}

if ($arFiltro['inCodTipoTransferencia'] != 0) {
    $stCampo  = 'descricao';
    $stTabela = 'tesouraria.tipo_transferencia';
    $stFiltro = ' WHERE cod_tipo = '.$arFiltro['inCodTipoTransferencia'];
    $stDescricaoTransferencia = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
    $obPDF->addFiltro('Tipo de Transferência: ', $stDescricaoTransferencia);
}

$arDados = Sessao::read('arDados');

$obPDF->addRecordSet($arDados);

$obPDF->setAlinhamento ('C');
$obPDF->addCabecalho   ('Data', 8, 10);

//Se Municipio é TO, Insere a Coluna Tipo de Transferência
if($inCodigoUFSistema==27){
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Lote', 9, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Tipo', 13, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Conta a Débito', 30, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Conta a Crédito', 30, 10);
}else{
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Lote', 10, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Conta a Débito', 33, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Conta a Crédito', 33, 10);
}

$obPDF->setAlinhamento ('C');
$obPDF->addCabecalho   ('Valor', 10, 10);
$obPDF->addQuebraPagina('pagina',1);

$obPDF->setAlinhamento('C');
$obPDF->addCampo      ('data', 8);
$obPDF->setAlinhamento('C');
$obPDF->addCampo      ('lote', 8);

//Se Municipio é TO, Insere o Valor do Tipo de Transferência 
if($inCodigoUFSistema==27){
    $obPDF->setAlinhamento('L');
    $obPDF->addCampo      ('tipo_transferencia', 8);
}

$obPDF->setAlinhamento('L');
$obPDF->addCampo      ('debito', 8);
$obPDF->setAlinhamento('L');
$obPDF->addCampo      ('credito', 8);
$obPDF->setAlinhamento('R');
$obPDF->addCampo      ('valor', 8);

$arAssinaturas = Sessao::read('assinaturas');

if (count($arAssinaturas['selecionadas']) > 0) {
    include_once CAM_FW_PDF.'RAssinaturas.class.php';
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas($arAssinaturas['selecionadas']);
    $obPDF->setAssinaturasDefinidas  ($obRAssinaturas->getArAssinaturas());
}

$obPDF->show();

?>
