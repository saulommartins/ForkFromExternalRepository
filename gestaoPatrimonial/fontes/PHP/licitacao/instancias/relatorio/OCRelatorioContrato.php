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
    * Página de Filtro para Relatório de Ïtens
    * Data de Criação   : 24/01/2006

    * Casos de uso : uc-03.05.31

    $Id: OCRelatorioContrato.php 64212 2015-12-17 12:38:12Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3, 37, 1 );
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt( '2.5.0' );

$preview->setNomeArquivo('contrato');

$stEntidade = $request->get('inNumCGM', '');
if ( is_array( $stEntidade ) )
    $stEntidade = implode ( ' , ', $stEntidade );

$preview->addParametro ( 'inNumContrato'  , $request->get('inNumContrato')   );
$preview->addParametro ( 'stObjeto'       , $request->get('stObjeto')        );
$preview->addParametro ( 'stDtInicial'    , $request->get('stDtInicial')     );
$preview->addParametro ( 'stDtFinal'      , $request->get('stDtFinal')       );
$preview->addParametro ( 'inCodFornecedor', $request->get('inCodFornecedor') );
$preview->addParametro ( 'stEntidades'    , $stEntidade                      );
$preview->addParametro ( 'stAnulados'     , $request->get('snAnulados')      );
$preview->addParametro ( 'tipoContrato'   , $request->get('tipoContrato')    );

if ($request->get('dtVlPagos'))
    $preview->addParametro ( 'dtPagosAte' , $request->get('dtVlPagos') );
else
    $preview->addParametro ( 'dtPagosAte', date );

$stOrgaosSel = $request->get('inCodOrgaoSelecionados', '');
if ( is_array( $stOrgaosSel ) && count( $stOrgaosSel ) > 0 )
    $stOrgaosSel = implode ( ' , ', $stOrgaosSel );

if ( !( ( $request->get('stDtInicialAssinatura', '')=='' ) && ( $request->get('stDtFinalAssinatura', '')=='' ) ) ) {
    $dtIni = $request->get('stDtInicialAssinatura');
    $dtFim = $request->get('stDtFinalAssinatura');
    $periodo = "AND contrato.dt_assinatura BETWEEN to_date('".$dtIni."','dd/mm/yyyy') AND to_date('".$dtFim."','dd/mm/yyyy')";
    $preview->addParametro ( 'periodoAssinatura', $periodo);
}

if (!( ($request->get('stDtInicialInicioExec', '')=='') && ($request->get('stDtFinalInicioExec', '')=='') ) ) {
    $dtIni = $request->get('stDtInicialInicioExec');
    $dtFim = $request->get('stDtFinalInicioExec');
    $periodo = "AND contrato.inicio_execucao BETWEEN to_date('".$dtIni."','dd/mm/yyyy') AND to_date('".$dtFim."','dd/mm/yyyy')";
    $preview->addParametro ( 'periodoInicioExec', $periodo);
}

if (!( ($request->get('stDtInicialFimExec', '')=='') && ($request->get('stDtFinalFimExec', '')=='') ) ) {
    $dtIni = $request->get('stDtInicialFimExec');
    $dtFim = $request->get('stDtFinalFimExec');
    $periodo = "AND contrato.fim_execucao BETWEEN to_date('".$dtIni."','dd/mm/yyyy') AND to_date('".$dtFim."','dd/mm/yyyy')";
    $preview->addParametro ( 'periodoFimExec', $periodo);
}

$preview->addParametro ( 'inCodOrgaoSelecionados', $stOrgaosSel );

$preview->preview();
