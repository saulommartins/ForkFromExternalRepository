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
    * Página de Relatório RGF Anexo1
    * Data de Criação   : 08/10/2007

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: OCGeraRGFAnexo1.php 42520 2009-09-30 19:18:31Z hboaventura $

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

if (!$request->get('cmbBimestre') && !$request->get('cmbQuadrimestre') && !$request->get('cmbSemestre')) {
    $obErro->setDescricao('É preciso selecionar ao menos um '.$request->get('stTipoRelatorio').'.');
}

$stAno = $request->get('stExercicio');

$preview = new PreviewBirt(6,57,1);
$preview->setTitulo('Demonstrativo da Despesa com Pessoal');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

$preview->addParametro( 'cod_entidade', implode(',', $request->get('inCodEntidade') ) );

if (count($request->get('inCodEntidade')) == 1) {
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

if (($request->get('cmbBimestre') == 6 ) || ($request->get('cmbQuadrimestre') == 3) || ($request->get('cmbSemestre') == 2)) {
    $preview->addParametro('show_emp', 'false');
} else {
    $preview->addParametro('show_emp', 'true');
}

$preview->addParametro('percentagem_lim_max', 54);
$preview->addParametro('percentagem_lim_pru', 0.513);

switch ($request->get('stTipoRelatorio')) {
    case 'Quadrimestre':
        $preview->addParametro('periodo', $request->get('cmbQuadrimestre'));
        $numPeriodo = $request->get('cmbQuadrimestre');
    break;
    case 'Semestre':
        $preview->addParametro('periodo', $request->get('cmbSemestre'));
        $numPeriodo = $request->get('cmbSemestre');
    break;
}

$preview->addParametro('tipo_periodo', $request->get('stTipoRelatorio'));

$inPeriodo = $request->get('cmbQuadrimestre') != '' ? $request->get('cmbQuadrimestre') : $request->get('cmbSemestre');

switch ($inPeriodo) {
    case 1:
        if ($request->get('stTipoRelatorio') == 'Quadrimestre') {
            $data_fim = '30/04/'.$stAno;
            $data_ini = '01/05/'.($stAno - 1);
        } elseif ($request->get('stTipoRelatorio') == 'Semestre') {
            $data_fim = '30/06/'.$stAno;
            $data_ini = '01/07/'.($stAno - 1);
        }
    break;

    case 2:
        if ($request->get('stTipoRelatorio') == 'Quadrimestre') {
            $data_fim = '31/08/'.$stAno;
            $data_ini = '01/09/'.($stAno - 1);
        } elseif ($request->get('stTipoRelatorio') == 'Semestre') {
            $data_fim = '31/12/'.$stAno;
            $data_ini = '01/01/'.$stAno;
        }
   break;

    case 3:
        $data_fim = '31/12/'.$stAno;
        $data_ini = '01/01/'.$stAno;
   break;
}

$preview->addParametro( 'data_ini', $data_ini );
$preview->addParametro( 'data_fim'   , $data_fim );

$preview->addParametro('poder', 'Legislativo');
$preview->addParametro('limite_maximo', '1,32%');
$preview->addParametro('limite_prudencial', '1,25%');

// verificando se foi selecionado Câmara e outra entidade junto
$rsEntidade->setPrimeiroElemento();
if (!$obErro->ocorreu() && (count($request->get('inCodEntidade')) != 1)) {
    while (!$rsEntidade->eof()) {
        if (preg_match("/câmara.*/i", $rsEntidade->getCampo('nom_cgm')) || preg_match( "/camara.*/i", $rsEntidade->getCampo('nom_cgm'))) {
            $obErro->setDescricao("Entidade ".$rsEntidade->getCampo('nom_cgm')." deve ser selecionada sozinha.");
            $boPreview = false;
            break;
        }
        $rsEntidade->proximo();
    }
}

$preview->addAssinaturas(Sessao::read('assinaturas'));

if( !$obErro->ocorreu() )
    $preview->preview();
else
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=".$request->get('stAcao')."", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
