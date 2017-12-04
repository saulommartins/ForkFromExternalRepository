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
 * Arquivo que passa os filtros para o relatório do BIRT
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$preview = new PreviewBirt(6, 42, 2);
$preview->setTitulo('Balancete Financeiro');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

$stExercicio = Sessao::getExercicio();
$inCodEntidade = $request->get('inCodEntidade');

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', $stExercicio );
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, "and e.cod_entidade in (".implode(',',$inCodEntidade).")" );

$obErro = new Erro();

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

$preview->addParametro('periodo', $request->get('stPeriodo'));
$preview->addParametro('quadrimestre', $request->get('inCodPeriodo'));
$preview->addParametro('demonstrar_despesa', $request->get('stDemonstrarDespesa'));
$preview->addParametro('tipo_relatorio', $request->get('inCodTipoRelatorio'));

$preview->addParametro( 'exercicio', $stExercicio );

$preview->preview();
