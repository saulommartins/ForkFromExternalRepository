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
    * Classe de mapeamento da tabela EMPENHO.EMPENHO_ANULADO_ITEM
    * Data de Criação: 16/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.23
                    uc-02.03.16
                    uc-02.03.03
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.EMPENHO_ANULADO_ITEM
  * Data de Criação: 16/12/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoEmpenhoAnuladoItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoEmpenhoAnuladoItem()
{
    parent::Persistente();
    $this->setTabela('empenho.empenho_anulado_item');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_empenho,exercicio,cod_entidade,timestamp,cod_pre_empenho,num_item');

    $this->AddCampo('cod_entidade'    ,'integer'  ,true,    '' ,true ,true  );
    $this->AddCampo('exercicio'       ,'varchar'  ,true,   '4' ,true ,true  );
    $this->AddCampo('cod_empenho'     ,'integer'  ,true,    '' ,true ,true  );
    $this->AddCampo('timestamp'       ,'timestamp',false,   '' ,true ,false );
    $this->AddCampo('cod_pre_empenho' ,'integer'  ,true,    '' ,true ,true  );
    $this->AddCampo('num_item'        ,'integer'  ,true,    '' ,true ,true  );
    $this->AddCampo('vl_anulado'      ,'numeric'  ,true,'14.2' ,true ,true  );

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoManutencaoDatas(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoManutencaoDatas().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoManutencaoDatas()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "    eai.exercicio,                                               \n";
    //$stSql .= "    eai.num_item,                                                \n";
    $stSql .= "    eai.vl_anulado,                                              \n";
    $stSql .= "    to_char(eai.timestamp,'dd/mm/yyyy') as dt_anulacao,          \n";
    $stSql .= "    replace(replace(eai.timestamp,' ',';'),'.','@') as timestamp_alterado     \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "    empenho.empenho e,                                       \n";
    $stSql .= "    empenho.empenho_anulado ea,                              \n";
    $stSql .= "    empenho.empenho_anulado_item eai                         \n";
    $stSql .= " WHERE                                                           \n";
    $stSql .= "    e.cod_empenho   = ea.cod_empenho     AND                     \n";
    $stSql .= "    e.exercicio     = ea.exercicio       AND                     \n";
    $stSql .= "    e.cod_entidade  = ea.cod_entidade    AND                     \n";
    $stSql .= "                                                                 \n";
    $stSql .= "    ea.exercicio    = eai.exercicio      AND                     \n";
    $stSql .= "    ea.cod_entidade = eai.cod_entidade   AND                     \n";
    $stSql .= "    ea.cod_empenho  = eai.cod_empenho    AND                     \n";
    $stSql .= "    ea.timestamp    = eai.timestamp      AND                     \n";
    $stSql .= "                                                                 \n";
    $stSql .= "    e.cod_empenho   = '".$this->getDado('cod_empenho')."'  AND   \n";
    $stSql .= "    e.cod_entidade  = '".$this->getDado('cod_entidade')."' AND   \n";
    $stSql .= "    e.exercicio     = '".$this->getDado('exercicio')."'          \n";
    $stSql .= " GROUP BY eai.exercicio                                          \n";
    $stSql .= "         ,eai.vl_anulado                                         \n";
    $stSql .= "         ,to_char(eai.timestamp,'dd/mm/yyyy')                    \n";
    $stSql .= "         ,eai.timestamp                                          \n";

    return $stSql;
}
}
