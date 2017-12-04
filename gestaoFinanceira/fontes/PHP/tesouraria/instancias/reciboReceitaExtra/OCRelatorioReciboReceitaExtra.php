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
    * Página de geração do recordSet para o Relatório Metas de Execução da Receita
    * Data de Criação   : 28/08/2006

    * @author Analista: Diego Vitoria
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.29
*/

/*
$Log$
Revision 1.10  2007/03/02 18:33:11  vitor
7956

Revision 1.9  2006/11/23 21:19:23  cako
Bug #7614#

Revision 1.8  2006/11/23 20:25:06  cako
Bug #7614#

Revision 1.7  2006/11/08 22:42:12  cleisson
Bug #7306#

Revision 1.6  2006/10/23 13:18:24  larocca
Bug #7252#

Revision 1.5  2006/10/23 11:33:50  larocca
Bug #7216#

Revision 1.4  2006/10/04 15:14:45  bruce
colocada tag de log

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                    );
include_once ( CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoAnalitica.class.php'                      );
include_once ( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php'                                 );

$obRelatorio = new RRelatorio;

$arDados = Sessao::read('arDados');
/// gerando arrays para impressão do Recibo de Receita Extra
///// Dados da Entidade
$arEntidade = array();
$obEntidade = new TOrcamentoEntidade;
$stFiltro   = " and  E.exercicio = '" . $arDados['exercicio'] . "' and E.cod_entidade = " . $arDados['inCodEntidade'];
$obEntidade->recuperaRelacionamento ( $rsEntidade, $stFiltro );

$arEntidade[]['entidade'] = 'Entidade: ' . $arDados['inCodEntidade']. ' - '. $rsEntidade->getCampo('nom_cgm');
$arEntidade[]['entidade'] = '';

///// Data e Valor
$arDataValor = array();
$arDataValor[0]['valor']  = 'Valor do Recibo: R$'.$arDados['txtValor'] . ' ('. SistemaLegado::extenso(str_replace(',','.',str_replace('.','',$arDados['txtValor']))).' )';

//// Dados do credor
$arCredor = array();

///// essa gaMbiarra é pra colocar um linha em branco antes e uma depois do credor no relatório
$arCredor[0]['credor'] = '';
if ($arDados['inCodCredor'] != '') {
    $arCredor[1]['credor'] = 'Credor: '.$arDados['inCodCredor'] .' - '. $arDados['stNomCredor'];
} else
    $arCredor[1]['credor'] = 'Credor:';
$arCredor[2]['credor'] = '';

///Conta de Receita
$obTContPlanoAnalitica = new TContabilidadePlanoAnalitica;
$stFiltro= " where pa.cod_plano = " . $arDados['inCodContaReceita']. " and pa.exercicio = '". $arDados['exercicio'] . "' ";
$obTContPlanoAnalitica->recuperaRelacionamento( $rsConta, $stFiltro );
//$obTContPlanoAnalitica->debug();

$arContas = array();
//// Linha de titulo
$arContas[0]['nom_conta']      = '';
$arContas[0]['cod_estrutural'] = 'Conta Caixa/Banco:';
$arContas[0]['cod_conta']      = '';

////Conta Caixa Banco
if ($arDados['inCodContaBanco']) {
    $stFiltro= " where pa.cod_plano = " . $arDados['inCodContaBanco']. " and pa.exercicio = '". $arDados['exercicio'] . "' ";
    $obTContPlanoAnalitica->recuperaRelacionamento( $rsContaBanco, $stFiltro );
//    $obTContPlanoAnalitica->debug();
    $arContas[1]['nom_conta']      = $rsContaBanco->getCampo('nom_conta');
    $arContas[1]['cod_estrutural'] = $rsContaBanco->getCampo('cod_estrutural');
    $arContas[1]['cod_conta']      = $rsContaBanco->getCampo('cod_plano');
} else {
    $arContas[1]['nom_conta']      = '';
    $arContas[1]['cod_estrutural'] = '';
    $arContas[1]['cod_conta']      = '';
}

//// Linha de titulo
$arContas[2]['nom_conta']      = '';
$arContas[2]['cod_estrutural'] = 'Conta de Receita:';
$arContas[2]['cod_conta']      = '';

$inCountC = 3;
$stNomConta      = str_replace( chr(10) , "", $rsConta->getCampo('nom_conta'));
$stNomConta      = wordwrap( $stNomConta, 50, chr(13) );
$arNomConta      = explode( chr(13), $stNomConta);
foreach ($arNomConta as $stNomConta) {
     $arContas[$inCountC]['nom_conta'] = $stNomConta;
     $inCountC++;
$arContas[4]['cod_estrutural'] = "";
$arContas[4]['cod_conta']      = "";
}

//$arContas[3]['nom_conta']      = $rsConta->getCampo('nom_conta');
$arContas[3]['cod_estrutural'] = $rsConta->getCampo('cod_estrutural');
$arContas[3]['cod_conta']      = $rsConta->getCampo('cod_plano');

////Historico
$arTexto = array();

$stHistorico = $arDados['txtHistorico'];
$stHistorico = str_replace( chr(10), "", $stHistorico );
$stHistorico = wordwrap ( $stHistorico, 113 , chr(13)) ;
$arTexto = explode ( chr(13), $stHistorico );

$arHistorico = array();
$inCont = 0;
foreach ($arTexto as $historico) {
    $arHistorico[$inCont]['historico'] = $historico;
    $arHistorico[$inCont]['titulo']    = '';
    $inCont++;
}
$arHistorico[0]['titulo']    = 'Histórico: ';

$arHistorico[$inCont+1]['historico'] = '';
$arHistorico[$inCont+1]['titulo'] = '';

////Recurso
$arRecurso = array();
if ($arDados['stDestinacaoRecurso']) {
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEspecificacaoDestinacaoRecurso.class.php"        );
    $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;
    $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaTodos( $rsEspecDestinacao, " WHERE cod_especificacao = ".$arDados['inCodEspecificacao']." AND exercicio = '".$arDados['exercicio']."' " );
    $arDados['stDescricaoRecurso'] = $rsEspecDestinacao->getCampo('descricao');
    $arDados['inCodRecurso'] = substr($arDados['stDestinacaoRecurso'],0,6);
}
if ($arDados['inCodRecurso'] != '') {
    $arRecurso[0]['recurso'] = 'Recurso: '.$arDados['inCodRecurso'] .' - '. $arDados['stDescricaoRecurso'];
} else
    $arRecurso[0]['recurso'] = 'Recurso: ';

/// passando os o controle para o gerador do Relatório em PDF

//unset( sessao->transf5 );

$arRel = array();
$arRel['entidade']  = $arEntidade;
$arRel['dataValor'] = $arDataValor;
$arRel['credor']    = $arCredor;
$arRel['contas']    = $arContas;
$arRel['historico'] = $arHistorico;
$arRel['recurso']   = $arRecurso;
$arRel['stNomeCredor'] = $arDados['stNomCredor'];
$arRel['numeroRecibo'] = $arDados['numeroRecibo'];
$arRel['codEntidade']  = $arDados['inCodEntidade'];
$arRel['exercicio']    = $arDados['exercicio'];

Sessao::write('arDados', $arRel);

$obRelatorio->executaFrameOculto( 'OCGeraRelatorioReciboReceitaExtra.php' );

?>
