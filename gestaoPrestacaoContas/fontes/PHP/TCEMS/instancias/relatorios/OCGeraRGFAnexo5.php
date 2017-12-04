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
    * Página de Relatório RGF Anexo5
    * Data de Criação   : 08/03/2008

    * @author Bruce

    * @ignore

     * Casos de uso : uc-06.01.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$inCodEntidade = $request->get('inCodEntidade');

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , $request->get('stExercicio') );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$inCodEntidade).")" );

$obErro = new Erro();

$preview = new PreviewBirt(6,57,2);
$preview->setTitulo('Dem Disponibilidades de Caixa');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

$preview->addParametro( 'cod_entidade', implode(',', $request->get('inCodEntidade') ) );
if ( count($request->get('inCodEntidade')) == 1 ) {

    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));

} else {

    $rsEntidade->setPrimeiroElemento();
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));

    while ( !$rsEntidade->eof() ) {
        if (preg_match("/prefeitura.*/i", $rsEntidade->getCampo( 'nom_cgm' ))) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm'));
            break;
        }
        $rsEntidade->proximo();
    }
}

$stDataInicial = "01/01/".$request->get('stExercicio');

if ($request->get('stTipoRelatorio') == 'UltimoQuadrimestre') {
    switch ($request->get('cmbQuadrimestre')) {
        case 3: $stDataFinal = '31/12'; break;
    }
    $nuPeriodo = $request->get('cmbQuadrimestre') ;
    $preview->addParametro( 'tipo_periodo'  , 'Quadrimestre' );
} elseif ($request->get('stTipoRelatorio') == 'UltimoSemestre') {
    switch ($request->get('cmbSemestre')) {
        case 2: $stDataFinal = '31/12'; break;
    }
    $nuPeriodo = $request->get('cmbSemestre') ;
    $preview->addParametro( 'tipo_periodo'  , 'Semestre' );
}
$stDataFinal = "$stDataFinal/".$request->get('stExercicio');


$preview->addParametro( 'cod_entidade' , $request->get('inCodEntidade') );
$preview->addParametro( 'data_inicio'  , $stDataInicial             );
$preview->addParametro( 'data_fim'     , $stDataFinal               );
$preview->addParametro( 'exercicio'    , $request->get('stExercicio')   );
$preview->addParametro( 'periodo'      , $nuPeriodo                 );
$preview->addParametro( 'poder'        , 'Legislativo'              );

$preview->addAssinaturas(Sessao::read('assinaturas'));
if( !$obErro->ocorreu() )
    $preview->preview();
else
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=$stAcao", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
