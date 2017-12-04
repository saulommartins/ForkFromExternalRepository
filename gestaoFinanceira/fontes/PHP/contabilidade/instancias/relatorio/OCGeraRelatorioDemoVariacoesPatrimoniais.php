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
    * Data de Criação   : 12/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    * $Id: OCDemoRecDespExtraOrcamento.php 46609 2012-05-18 13:07:51Z tonismar $

    * Casos de uso: uc-02.02.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$stDataIncial = $request->get('stDataInicial');
$stDataFinal  = $request->get('stDataFinal');

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$request->get('inCodEntidade')).")" );

if ($request->get("stEstrutural") == "false") {
    //demonstrarVariacoesPatrimoniais.rptdesign
    $preview = new PreviewBirt(2,9,14);
    $preview->addParametro( 'dt_inicial', $stDataIncial );
    $preview->addParametro( 'dt_final'  , $stDataFinal  );
    $preview->addParametro( 'data_inicial_nota', sistemaLegado::dataToSql($stDataIncial) );
    $preview->addParametro( 'data_final_nota'  , sistemaLegado::dataToSql($stDataFinal));
} else {
    //demonstrarVariacoesPatrimoniaisEstrutural.rptdesign
    $preview = new PreviewBirt(2,9,19);
    $preview->addParametro( 'dt_inicial'         , '01/01/'.Sessao::getExercicio() );
    $preview->addParametro( 'dt_inicial_anterior', '01/01/'.(Sessao::getExercicio()-1) );
    $preview->addParametro( 'dt_final'           , $stDataFinal );
    $preview->addParametro( 'dt_final_anterior'  , Sistemalegado::somaOuSubtraiData($stDataFinal, false ,1, 'year') );
    $preview->addParametro( 'data_inicial_nota'  , Sessao::getExercicio().'-01-01' );
    $preview->addParametro( 'data_final_nota'    , sistemaLegado::dataToSql($stDataFinal) );
}

$preview->setTitulo('Demonstração das Variações do Patrimoniais');
$preview->addParametro ( 'exercicio', Sessao::getExercicio() );
$preview->addParametro ( 'exercicio_anterior', (Sessao::getExercicio() - 1));
$preview->addParametro ( 'cod_entidades', implode(',',$request->get('inCodEntidade')));

if ( count($request->get('inCodEntidade')) == 1 ) {    
    $arCodEntidades = $request->get('inCodEntidade');
    $inCodEntidade  = $arCodEntidades[0];

    $preview->addParametro( 'entidade', $inCodEntidade );
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    $rsEntidade->setPrimeiroElemento();
    while ( !$rsEntidade->eof() ) {
        if (preg_match("/prefeitura.*/i", $rsEntidade->getCampo( 'nom_cgm' ))) {
            $preview->addParametro( 'entidade', $rsEntidade->getCampo('cod_entidade') );
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm')  );
            break;
        }
        $rsEntidade->proximo();
    }
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();

?>