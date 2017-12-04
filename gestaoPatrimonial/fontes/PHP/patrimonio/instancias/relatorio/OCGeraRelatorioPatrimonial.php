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
  * Data de criação : 27/09/2006

    * @author Analista:
    * @author Programador: Lucas Teixeira Stephano

    $Revision: 17292 $
    $Name$
    $Author: domluc $
    $Date: 2006-10-30 15:50:15 -0300 (Seg, 30 Out 2006) $

    Caso de uso: uc-03.01.21
**/

/*
$Log$
Revision 1.3  2006/10/30 18:50:15  domluc
Adicionada Totalização

Revision 1.2  2006/10/03 15:08:07  domluc
Corrigido caso de uso

Revision 1.1  2006/09/27 17:49:24  domluc
Caso de Uso 03.01.21

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,6,1);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relatorioPatrimonial');

$exercicio = Sessao::getExercicio();
$preview->addParametro( 'exercicio', $exercicio );
$preview->addParametro( 'entidade', $request->get('inCodEntidade') );
$filtro="1'";

//seta a natureza
$natureza = ($request->get('inCodNatureza')!='') ? $request->get('inCodNatureza') : 0;
$stNatureza="Não Encontrado";
$preview->addParametro( 'cod_natureza', $natureza );
if ($natureza!=0) {
    $filtro .= " AND natureza.cod_natureza =".$natureza;
    $where = "WHERE natureza.cod_natureza =".$natureza;
    $stNatureza = SistemaLegado::pegaDado('nom_natureza', 'patrimonio.natureza', $where);
}
$preview->addParametro( 'nom_natureza', $stNatureza );

//seta o grupo
$grupo = ($request->get('inCodGrupo')!='') ? $request->get('inCodGrupo') : 0;
$stGrupo="Não Encontrado";
$preview->addParametro( 'cod_grupo', $grupo );
if ($grupo!=0) {
    $filtro .= " AND grupo.cod_grupo =".$grupo;
    $where = "WHERE grupo.cod_grupo =".$grupo." AND cod_natureza =".$natureza;
    $stGrupo = SistemaLegado::pegaDado('nom_grupo', 'patrimonio.grupo', $where);
}
$preview->addParametro( 'nom_grupo', $stGrupo );

//seta a especie
$especie = ($request->get('inCodEspecie')!='') ? $request->get('inCodEspecie') : 0;
$stEspecie="Não Encontrado";
$preview->addParametro( 'cod_especie', $especie );
if ($especie!=0) {
    $filtro .= " AND especie.cod_especie =".$especie;
    $where = "WHERE especie.cod_especie =".$especie." AND cod_natureza =".$natureza." AND cod_grupo =".$grupo;
    $stEspecie = SistemaLegado::pegaDado('nom_especie', 'patrimonio.especie', $where);
}
$preview->addParametro( 'nom_especie', $stEspecie );

$filtro .=" AND '1'='1";

$preview->addParametro( 'filtro', $filtro );

//seta o cod_bem
$preview->addParametro( 'cod_bem_inicial', $request->get('inCodBemInicio') );
$preview->addParametro( 'cod_bem_final', $request->get('inCodBemFinal') );

//periodo aquisicao
$preview->addParametro( 'periodo_inicial_aquisicao', $request->get('stDataInicialAquisicao') );
$preview->addParametro( 'periodo_final_aquisicao', $request->get('stDataFinalAquisicao') );

//periodo incorporacao
$preview->addParametro( 'periodo_inicial_incorporacao', $request->get('stDataInicialIncorporacao') );
$preview->addParametro( 'periodo_final_incorporacao', $request->get('stDataFinalIncorporacao') );

$preview->addParametro( 'cod_orgao',$request->get('hdnUltimoOrgaoSelecionado') );
$stOrgao="Não Encontrado";
if ($request->get('hdnUltimoOrgaoSelecionado')>0) {
    $where = "WHERE cod_orgao =".$request->get('hdnUltimoOrgaoSelecionado');
    $stOrgao = SistemaLegado::pegaDado('descricao', 'organograma.orgao_descricao', $where);
}
$preview->addParametro( 'nom_orgao',$stOrgao );

$preview->addParametro( 'ano_exercicio', '' );

$preview->addParametro( 'cod_local', $request->get('inCodLocal') );
$stLocal="Não Encontrado";
if ($request->get('inCodLocal')>0) {
    $where = "WHERE cod_local =".$request->get('inCodLocal');
    $stLocal = SistemaLegado::pegaDado('descricao', 'organograma.local', $where);
}
$preview->addParametro( 'nom_local',$stLocal );

$preview->addParametro( 'tipo_relatorio', $request->get('inTipoRelatorio') );

$preview->preview();
