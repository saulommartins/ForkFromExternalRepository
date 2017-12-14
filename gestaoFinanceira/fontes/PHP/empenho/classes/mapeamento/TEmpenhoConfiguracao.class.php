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
    * Classe de mapeamento da tabela EMPENHO.PRE_EMPENHO
    * Data de Criação: 30/11/2004

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2007-07-13 16:18:44 -0300 (Sex, 13 Jul 2007) $

    * Casos de uso: uc-02.01.01

*/

/*
$Log$
Revision 1.3  2007/07/13 18:59:57  cako
Bug#9383#, Bug#9384#

Revision 1.2  2006/09/25 14:07:01  cleisson
Bug #7042#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

class TEmpenhoConfiguracao extends TAdministracaoConfiguracao
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoConfiguracao()
{
    parent::TAdministracaoConfiguracao();
    $this->SetDado( "exercicio",  Sessao::getExercicio() );
    $this->SetDado( "cod_modulo", 10                 );
}

function verificaUtilizacaoContaCaixa(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaVerificaUtilizacaoContaCaixa().$stCondicao;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaUtilizacaoContaCaixa()
{
    $stSql  = "      SELECT  cc.cod_plano                                          \n";
    $stSql .= "             ,lr.cod_lote                                           \n";
    $stSql .= "        FROM contabilidade.lancamento_retencao as lr                \n";
    $stSql .= "             JOIN contabilidade.conta_credito as cc                 \n";
    $stSql .= "             ON (    lr.cod_lote     = cc.cod_lote                  \n";
    $stSql .= "                 AND lr.cod_entidade = cc.cod_entidade              \n";
    $stSql .= "                 AND lr.sequencia    = cc.sequencia                 \n";
    $stSql .= "                 AND lr.tipo         = cc.tipo                      \n";
    $stSql .= "                 AND lr.exercicio    = cc.exercicio                 \n";
    $stSql .= "             )                                                      \n";
    $stSql .= "       WHERE lr.exercicio = '".$this->getDado('exercicio')."'       \n";
    $stSql .= "         AND lr.cod_entidade = ".$this->getDado('cod_entidade')."   \n";
    $stSql .= "         AND cc.cod_plano = ".$this->getDado('cod_plano')."         \n";
    $stSql .= "       LIMIT 1                                                      \n";

    return $stSql;
}

}
