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
  * Página de Mapeamento para funcao VerificaSuspensao
  * Data de criação : 14/03/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Marcelo B. Paulino

    * $Id: FARRVerificaSuspensao.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.3  2006/11/16 16:45:46  cercato
correcao para calculo/lancamento individual cadastro economico.

Revision 1.2  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

set_time_limit(0);

class FARRVerificaSuspensao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FARRVerificaSuspensao()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.verificaSuspensao');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo( 'valor'         ,'numeric', true, '',false, false );
    $this->AddCampo( 'cod_suspensao' ,'integer', true, '',false, false );
    $this->AddCampo( 'cod_lancamento','integer', true, '',false, false );
}
function montaRecuperaTodos()
{
    $stSql = "SELECT *                                                                    \n";
    $stSql .= "FROM ".$this->getTabela()."( '".$this->getDado('stFiltro')."' )             \n";
    $stSql .= " as retorno( valor numeric, cod_suspensao integer, cod_lancamento integer ) \n";

    return $stSql;
}

function recuperaSuspensao(&$rsRecordSet, $stFiltro ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaSuspensao( $stFiltro );
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSuspensao($stFiltro)
{
    $stSql  = "   SELECT \n";
    $stSql .= "     L.valor, \n";
    $stSql .= "     S.cod_suspensao, \n";
    $stSql .= "     S.cod_lancamento \n";
    $stSql .= "   FROM \n";
    $stSql .= "     arrecadacao.lancamento L, \n";
    $stSql .= "     arrecadacao.suspensao S \n";
    $stSql .= "   LEFT JOIN  \n";
    $stSql .= "     arrecadacao.suspensao_termino ST \n";
    $stSql .= "   ON \n";
    $stSql .= "     ST.cod_suspensao = S.cod_suspensao \n";
    $stSql .= "   WHERE \n";
    $stSql .= "     ST.cod_suspensao IS NULL AND \n";
    $stSql .= "     L.cod_lancamento = S.cod_lancamento AND \n";
    $stSql .= "     L.cod_lancamento IN \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             distinct(cod_lancamento) \n";
    $stSql .= "         FROM \n";
    $stSql .= "             arrecadacao.lancamento_calculo \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             cod_calculo in \n";
    $stSql .= "             ( \n";
    $stSql .= "                 select \n";
    $stSql .= "                     cod_calculo \n";
    $stSql .= "                 from  \n";
    $stSql .= "                     arrecadacao.cadastro_economico_calculo \n";
    $stSql .= "                 where inscricao_economica = ".$stFiltro." \n";
    $stSql .= "             ) \n";
    $stSql .= "     ) \n";

    return $stSql;
}

}
