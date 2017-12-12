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
    * Classe de mapeamento da função fn_relatorio_boletim_tesouraria
    * Data de Criação: 01/08/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Autor:$
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.01.22
*/

/*
$Log$
Revision 1.2  2006/08/04 13:48:35  jose.eduardo
Ajustes

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FSTNRGFAnexo3 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FSTNRGFAnexo3()
{
    parent::Persistente();
}

function recuperaDadosReceitaLiquida(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosReceitaLiquida();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosReceitaLiquida()
{
    $stSql  = "SELECT *                                                                                      \n";
    $stSql .= "FROM stn.fn_rgf_receita_liquida_anexo3 ('".$this->getDado("stExercicio")."') as retorno(      \n";
    $stSql .= "    descricao                      varchar,                                                   \n";
    $stSql .= "    receita_exercicio_anterior     numeric,                                                   \n";
    $stSql .= "    receita_primeiro_quadrimestre  numeric,                                                   \n";
    $stSql .= "    receita_segundo_quadrimestre   numeric,                                                   \n";
    $stSql .= "    receita_terceiro_quadrimestre  numeric)                                                   \n";

    return $stSql;
}

function recuperaDadosRelatorioAnexo3(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosRelatorioAnexo3();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosRelatorioAnexo3()
{
    $stSql .= "select *                                                                        \n";
    $stSql .= "from stn.fn_rgf_anexo3('".$this->getDado("stExercicio")."',               \n";
    $stSql .= "                             '".$this->getDado("stEntidade") ."') as retorno(   \n";
    $stSql .= "descricao                      varchar,                                         \n";
    $stSql .= "saldo_exercicio_anterior       numeric,                                         \n";
    $stSql .= "saldo_primeiro_quadrimestre    numeric,                                         \n";
    $stSql .= "saldo_segundo_quadrimestre     numeric,                                         \n";
    $stSql .= "saldo_terceiro_quadrimestre    numeric,                                         \n";
    $stSql .= "sequencia                      numeric,                                         \n";
    $stSql .= "tipo                           varchar)                                         \n";

    return $stSql;
}

}
