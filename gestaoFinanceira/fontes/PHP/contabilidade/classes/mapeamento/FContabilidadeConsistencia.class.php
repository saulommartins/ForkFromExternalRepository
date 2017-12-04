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
    * Classe de mapeamento da função contabilidadelconsistencia
    * Data de Criação: 24/05/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.32
*/

/*
$Log$
Revision 1.2  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeConsistencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeConsistencia()
{
    parent::Persistente();
    $this->setTabela('contabilidade.fn_consistencia');

}

function recuperaTodos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = "";
    $stSql = $this->montaRecuperaTodos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                                      \n";
    $stSql .= "     *                                                                                       \n";
    $stSql .= " FROM                                                                                        \n";
    $stSql .= "   ".$this->getTabela()."('".$this->getDado("stExercicio")."','".$this->getDado("stEntidades")."','".$this->getDado("stDtInicial")."','".$this->getDado("stDtFinal")."' )\n";

    return $stSql;
}

function recuperaConsistencia1(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, empenho";
    $stSql = $this->montaRecuperaConsistencia1().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia1()
{
    $stSql  = " SELECT * from contabilidade.consistencia_1 \n";

    return $stSql;
}

function recuperaConsistencia2(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, cod_empenho";
    $stSql = $this->montaRecuperaConsistencia2().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia2()
{
    $stSql  = " SELECT * from contabilidade.consistencia_2 \n";

    return $stSql;
}

function recuperaConsistencia3(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY exercicio, cod_entidade";
    $stSql = $this->montaRecuperaConsistencia3().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia3()
{
    $stSql  = " SELECT * from contabilidade.consistencia_3 \n";

    return $stSql;
}

function recuperaConsistencia4(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, cod_lote";
    $stSql = $this->montaRecuperaConsistencia4().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia4()
{
    $stSql  = " SELECT * from contabilidade.consistencia_4 \n";

    return $stSql;
}

function recuperaConsistencia5(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, empenho";
    $stSql = $this->montaRecuperaConsistencia5().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia5()
{
    $stSql  = " SELECT * from contabilidade.consistencia_5 \n";

    return $stSql;
}

function recuperaConsistencia6(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, empenho";
    $stSql = $this->montaRecuperaConsistencia6().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia6()
{
    $stSql  = " SELECT * from contabilidade.consistencia_6 \n";

    return $stSql;
}

function recuperaConsistencia7(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY exercicio, cod_entidade, cod_lote";
    $stSql = $this->montaRecuperaConsistencia7().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia7()
{
    $stSql  = " SELECT * from contabilidade.consistencia_7 \n";

    return $stSql;
}

function recuperaConsistencia8(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, cod_lote";
    $stSql = $this->montaRecuperaConsistencia8().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia8()
{
    $stSql  = " SELECT * from contabilidade.consistencia_8 \n";

    return $stSql;
}

function recuperaConsistencia9(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, cod_lote";
    $stSql = $this->montaRecuperaConsistencia9().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia9()
{
    $stSql  = " SELECT * from contabilidade.consistencia_9 \n";

    return $stSql;
}

function recuperaConsistencia10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" ,
$boTransacao = "") {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, cod_conta";
    $stSql = $this->montaRecuperaConsistencia10().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia10()
{
    $stSql  = " SELECT * from contabilidade.consistencia_10 \n";

    return $stSql;
}

function recuperaConsistencia11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" ,
$boTransacao = "") {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_entidade, cod_plano";
    $stSql = $this->montaRecuperaConsistencia11().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsistencia11()
{
    $stSql  = " SELECT * from contabilidade.consistencia_11 \n";

    return $stSql;
}

}
