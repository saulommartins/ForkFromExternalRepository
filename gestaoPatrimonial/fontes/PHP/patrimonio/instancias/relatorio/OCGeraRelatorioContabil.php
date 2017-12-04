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
    * Página de geração de relatório
    * Data de Criação: 23/04/2015

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    $Id: OCGeraRelatorioContabil.php 66139 2016-07-21 14:22:58Z lisiane $

    * Casos de uso: uc-03.01.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,6,23);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relatorioContabil');

$preview->addParametro( 'entidade', $request->get('inCodEntidade') );
$preview->addParametro( 'exercicio_conta', $request->get('stExercicio') );

//seta a natureza
$natureza = ($request->get('inCodNatureza')!='') ? $request->get('inCodNatureza') : 0;
$stNatureza="Não Encontrado";
$preview->addParametro( 'cod_natureza', $natureza );
if ($natureza!=0) {
    $where = "WHERE natureza.cod_natureza =".$natureza;
    $stNatureza = SistemaLegado::pegaDado('nom_natureza', 'patrimonio.natureza', $where);
}
$preview->addParametro( 'nom_natureza', $stNatureza );

//seta o grupo
$grupo = ($request->get('inCodGrupo')!='') ? $request->get('inCodGrupo') : 0;
$stGrupo="Não Encontrado";
$preview->addParametro( 'cod_grupo', $grupo );
if ($grupo!=0) {
    $where = "WHERE grupo.cod_grupo =".$grupo." AND cod_natureza =".$natureza;
    $stGrupo = SistemaLegado::pegaDado('nom_grupo', 'patrimonio.grupo', $where);
}
$preview->addParametro( 'nom_grupo', $stGrupo );

//seta a especie
$especie = ($request->get('inCodEspecie')!='') ? $request->get('inCodEspecie') : 0;
$stEspecie="Não Encontrado";
$preview->addParametro( 'cod_especie', $especie );
if ($especie!=0) {
    $where = "WHERE especie.cod_especie =".$especie." AND cod_natureza =".$natureza." AND cod_grupo =".$grupo;
    $stEspecie = SistemaLegado::pegaDado('nom_especie', 'patrimonio.especie', $where);
}
$preview->addParametro( 'nom_especie', $stEspecie );

//seta a Periodicidade
$stDataInicialIncorporacao = ($request->get('stDataInicialIncorporacao')!='') ? $request->get('stDataInicialIncorporacao') : 0;
$preview->addParametro( 'dt_inicial', $stDataInicialIncorporacao );

$stDataFinalIncorporacao = ($request->get('stDataFinalIncorporacao')!='') ? $request->get('stDataFinalIncorporacao') : 0;
$preview->addParametro( 'dt_final', $stDataFinalIncorporacao );

$preview->preview();
