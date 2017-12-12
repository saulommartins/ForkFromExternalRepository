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
    * Página de Relatório
    * Data de Criação   : 28/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: OCGeraRelatorioBalanceteVerificacao.php 64153 2015-12-09 19:16:02Z evandro $

    * Casos de uso: uc-02.02.22
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once CAM_FW_PDF.'RRelatorio.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF('L');
$rsVazio      = new RecordSet;

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');

if (count($arFiltro['inCodEntidade']) == 1) {
    $obRRelatorio->setCodigoEntidade   ($arFiltro['inCodEntidade'][0]);
    $obRRelatorio->setExercicioEntidade(Sessao::getExercicio());
}

$obRRelatorio->setExercicio  (Sessao::getExercicio());
$obRRelatorio->recuperaCabecalho($arConfiguracao);

$obPDF->setModulo            ('Relatorio');
$obPDF->setTitulo            ('BALANCETE DE VERIFICACAO');
$obPDF->setSubTitulo         ('Período: '.$arFiltro['stDataInicial'].' até '.$arFiltro['stDataFinal']);
$obPDF->setUsuario           (Sessao::getUsername());
$obPDF->setEnderecoPrefeitura($arConfiguracao);

foreach ($arFiltro as $key => $valor) {
    if (substr($key, 0, 6) == 'grupo_') {
        $stGrupos .= substr($key,6,99).', ';
        $boGrupo = true;
    }
    if (substr($key, 0, 8) == 'sistema_') {
        $stSistemas .= substr($key,8,99).', ';
        $boSistema = true;
    }
}

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado          ('exercicio', Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, 'and e.cod_entidade in ('.implode(',', $arFiltro['inCodEntidade']).')');

$arNomEntidade = array();

while (!$rsEntidade->eof()) {
    $arNomEntidade[] = $rsEntidade->getCampo('nom_cgm');
    $rsEntidade->proximo();
}

$obPDF->addFiltro('Entidades Relacionadas', $arNomEntidade);

$rsRecordSet = Sessao::read('rsRecordSet');

$obPDF->addRecordSet($rsRecordSet);
$obPDF->addIndentacao  ('nivel', 'nom_conta', '      ');
$obPDF->setAlinhamento ('L');

if ($arFiltro['stEstrutural'] == 'S') {
    $obPDF->addCabecalho('CÓDIGO', 13, 9);
    $obPDF->addCabecalho   ('DESCRIÇÃO DA CONTA', 38, 10);
} else {
    $obPDF->addCabecalho   ('DESCRIÇÃO DA CONTA', 52, 10);
}

$obPDF->setAlinhamento ('C');

if ( Sessao::getExercicio() > '2012' ) {
    $obPDF->addCabecalho   ('N.C.'              , 4 , 10);
} else {
    $obPDF->addCabecalho   ('S.C.'              , 4 , 10);
}

$obPDF->addCabecalho   ('I.S.'              , 4 , 10);

$obPDF->setAlinhamento ('R');
$obPDF->addCabecalho   ('SALDO ANTERIOR'    , 11, 9);
$obPDF->addCabecalho   ('DÉBITOS'           , 11, 9);
$obPDF->addCabecalho   ('CRÉDITOS'          , 11, 9);
$obPDF->addCabecalho   ('SALDO ATUAL'       , 11, 9);

$obPDF->setAlinhamento ('L');

if ($arFiltro['stEstrutural'] == 'S') {
    $obPDF->addCampo('cod_estrutural', 8);
}

$obPDF->addCampo       ('nom_conta'          , 8);
$obPDF->setAlinhamento ('C');
$obPDF->addCampo       ('cod_sistema'        , 8);
$obPDF->addCampo       ('indicador_superavit', 8);
$obPDF->setAlinhamento ('R' );
$obPDF->addCampo       ('vl_saldo_anterior'  , 8);
$obPDF->addCampo       ('vl_saldo_debitos'   , 8);
$obPDF->addCampo       ('vl_saldo_creditos'  , 8);
$obPDF->addCampo       ('vl_saldo_atual'     , 8);
$obPDF->show();
die();
?>
