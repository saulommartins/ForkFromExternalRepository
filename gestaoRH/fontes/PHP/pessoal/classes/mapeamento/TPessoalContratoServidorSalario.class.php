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
    * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_SALARIO
    * Data de Criação: 08/09/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-02-13 13:27:10 -0200 (Qua, 13 Fev 2008) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_SALARIO
  * Data de Criação: 08/09/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorSalario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorSalario()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_salario');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,timestamp');

    $this->AddCampo('cod_contrato'             , 'integer'   , true  , ''     , true,  true);
    $this->AddCampo('timestamp'                , 'timestamp_now' , true , ''     , true,  false);
    $this->AddCampo('salario'                  , 'numeric'   , true  , '14,2' , false, false);
    $this->AddCampo('horas_mensais'            , 'numeric'   , true  , '5,2'  , false, false);
    $this->AddCampo('horas_semanais'           , 'numeric'   , true  , '5,2'  , false, false);
    $this->AddCampo('vigencia'                 , 'date'      , true  , ''     , false, false);
    $this->AddCampo('cod_periodo_movimentacao' , 'integer'   , true  , ''     , false, true);
    $this->AddCampo('reajuste'                 , 'boolean'   , false , ''     , false, false);

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaRelacionamentio
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT salario.*                                         \n";
    $stSql .= "  FROM pessoal.contrato_servidor_salario as salario      \n";
    $stSql .= "     , (  SELECT cod_contrato                            \n";
    $stSql .= "               , max(timestamp) as timestamp             \n";
    $stSql .= "            FROM pessoal.contrato_servidor_salario       \n";
    $stSql .= "        GROUP BY cod_contrato ) max_salario              \n";
    $stSql .= " WHERE salario.cod_contrato = max_salario.cod_contrato   \n";
    $stSql .= "   AND salario.timestamp    = max_salario.timestamp      \n";

    return $stSql;
}

function recuperaRelacionamentoPeriodoMovimentacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoPeriodoMovimentacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoPeriodoMovimentacao()
{
    $stSql .= "SELECT *                                                                                 \n";
    $stSql .= "  FROM pessoal.contrato_servidor_salario as salario                                      \n";
    $stSql .= "  JOIN ( SELECT contrato_servidor_salario.cod_contrato                                   \n";
    $stSql .= "                , max(contrato_servidor_salario.timestamp) as timestamp                  \n";
    $stSql .= "           FROM pessoal.contrato_servidor_salario                                        \n";
    $stSql .= "          WHERE to_char(contrato_servidor_salario.timestamp,'yyyy-mm-dd') <= '".$this->getDado('timestamp_situacao')."'\n";
    $stSql .= "            AND contrato_servidor_salario.vigencia <= '".$this->getDado('dt_final')."'   \n";
    $stSql .= "       GROUP BY contrato_servidor_salario.cod_contrato) as max_contrato_servidor_salario \n";
    $stSql .= "    ON max_contrato_servidor_salario.cod_contrato = salario.cod_contrato                 \n";
    $stSql .= "   AND max_contrato_servidor_salario.timestamp = salario.timestamp                       \n";

    return $stSql;
}

}
