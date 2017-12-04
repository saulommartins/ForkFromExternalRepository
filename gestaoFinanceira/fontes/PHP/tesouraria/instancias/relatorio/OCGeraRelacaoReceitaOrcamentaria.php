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
    * Página de filtro do relatório
    * Data de Criação   : 31/17/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    $Id: OCGeraRelacaoReceitaOrcamentaria.php 65160 2016-04-28 20:25:34Z michel $

    * Casos de uso: uc-02.04.36
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

$preview = new PreviewBirt(2,30,1);
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt('2.5.0');

$stEntidade = "";

$inCodigoEntidadesSelecionadas = $request->get('inCodigoEntidadesSelecionadas');
if (count($inCodigoEntidadesSelecionadas)>0)
    $stEntidade = implode(',',$inCodigoEntidadesSelecionadas);

if (count($inCodigoEntidadesSelecionadas)>1 || count($inCodigoEntidadesSelecionadas)==0)
    $inCodEntidade = SistemaLegado::pegaDado('valor','administracao.configuracao',"where cod_modulo = 8 AND parametro ILIKE 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."'");
else
    $inCodEntidade = $inCodigoEntidadesSelecionadas[0];

$preview->addParametro( "cod_entidade", $stEntidade );
$preview->addParametro( "entidade", $inCodEntidade );

//seta a data
$preview->addParametro( "data_ini", $request->get('stDataInicial'));
$preview->addParametro( "data_fim", $request->get('stDataFinal'));

//seta as o código estrutural das receitas
if ($request->get('stCodEstruturalInicial', '') != '' AND $request->get('stCodEstruturalFinal', '') != '') {
    $preview->addParametro( "estrutural", " BETWEEN '".$request->get('stCodEstruturalInicial')."' AND '".$request->get('stCodEstruturalFinal')."' " );
} elseif ($request->get('stCodEstruturalInicial', '') == '' AND $request->get('stCodEstruturaFinal', '') != '') {
    $preview->addParametro( "estrutural", " <= '".$request->get('stCodEstruturalFinal')."' " );
} elseif ($request->get('stCodEstruturalInicial', '') != '' AND $request->get('stCodEstruturalFinal', '') == '') {
    $preview->addParametro( "estrutural", " >= '".$request->get('stCodEstruturalInicial')."' " );
} else {
    $preview->addParametro( "estrutural", "" );
}

//seta o cod_reduzido
if ($request->get('inReceitaInicial', '') != '' AND $request->get('inReceitaFinal', '') != '') {
    $preview->addParametro("cod_reduzido", " BETWEEN ".$request->get('inReceitaInicial')." AND ".$request->get('inReceitaFinal'));
} elseif ($request->get('inReceitaInicial', '') == '' AND $request->get('inReceitaFinal', '') != '') {
    $preview->addParametro("cod_reduzido", " <= ".$request->get('inReceitaFinal'));
} elseif ($request->get('inReceitaInicial', '') != '' AND $request->get('inReceitaFinal', '') == '') {
    $preview->addParametro("cod_reduzido", " >= ".$request->get('inReceitaInicial'));
} else {
    $preview->addParametro("cod_reduzido", "");
}

//seta o cod_plano
if ($request->get('inCodContaBancoInicial', '') != '' AND $request->get('inCodContaBancoFinal', '') != '') {
    $preview->addParametro( "conta_banco", " BETWEEN ".$request->get('inCodContaBancoInicial')." AND ".$request->get('inCodContaBancoFinal') );
} elseif ($request->get('inCodContaBancoInicial', '') == '' AND $request->get('inCodContaBancoFinal', '') != '') {
    $preview->addParametro( "conta_banco", " <= ".$request->get('inCodContaBancoFinal') );
} elseif ($request->get('inCodContaBancoInicial', '') != '' AND $request->get('inCodContaBancoFinal', '') == '') {
    $preview->addParametro( "conta_banco", " >= ".$request->get('inCodContaBancoInicial') );
} else {
    $preview->addParametro( "conta_banco", "" );
}

if ($request->get('inCodRecurso', '') != '') {
    $preview->addParametro( 'recurso', $request->get('inCodRecurso') );
} else {
    $preview->addParametro( 'recurso', '' );
}

if( $request->get('inCodUso')<>NULL && $request->get('inCodDestinacao') && $request->get('inCodEspecificacao') )
     $preview->addParametro( 'destinacaorecurso', $request->get('inCodUso').".".$request->get('inCodDestinacao').".".$request->get('inCodEspecificacao') );
else $preview->addParametro( 'destinacaorecurso', '');

if ( $request->get('inCodDetalhamento') )
     $preview->addParametro( 'cod_detalhamento', $request->get('inCodDetalhamento') );
else $preview->addParametro( 'cod_detalhamento', '' );

if ($request->get('stTipoRelatorio', '') != '') {
    $preview->addParametro( 'tipo_relatorio', $request->get('stTipoRelatorio') );
} else {
    $preview->addParametro( 'tipo_relatorio', '' );
    $preview->addParametro( 'ordenacao', ' arrecadacao.timestamp_arrecadacao ASC ');
}

$preview->preview();
?>
