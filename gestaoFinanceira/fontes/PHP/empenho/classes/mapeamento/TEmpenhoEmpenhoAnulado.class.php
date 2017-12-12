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
    * Classe de mapeamento da tabela EMPENHO.EMPENHO_ANULADO
    * Data de Criação: 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-09-05 16:20:22 -0300 (Qua, 05 Set 2007) $

    * Casos de uso: uc-02.01.23
                    uc-02.03.03
*/

/*
$Log$
Revision 1.7  2007/09/05 19:18:42  leandro.zis
esfinge

Revision 1.6  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.EMPENHO_ANULADO
  * Data de Criação: 06/12/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoEmpenhoAnulado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoEmpenhoAnulado()
{
    parent::Persistente();
    $this->setTabela('empenho.empenho_anulado');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_empenho,exercicio,cod_entidade,timestamp');

    $this->AddCampo('cod_entidade' ,'integer'   ,true,  '',true ,true  );
    $this->AddCampo('exercicio'    ,'varchar'   ,true, '4',true ,true  );
    $this->AddCampo('cod_empenho'  ,'integer'   ,true,  '',true ,true  );
    $this->AddCampo('timestamp'    ,'timestamp' ,false, '',true ,false );
    $this->AddCampo('motivo'       ,'text'      ,true,  '',false,false );

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT  ea.cod_entidade                                               \n";
    $stSql .= "       ,ea.exercicio                                                  \n";
    $stSql .= "       ,ea.cod_empenho                                                \n";
    $stSql .= "       ,TO_CHAR(ea.timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp \n";
    $stSql .= "       ,ea.motivo                                                     \n";
    $stSql .= "       ,ei.num_item                                                   \n";
    $stSql .= "       ,ei.vl_anulado                                                 \n";
    $stSql .= "FROM  empenho.empenho_anulado      AS ea                              \n";
    $stSql .= "     ,empenho.empenho_anulado_item AS ei                              \n";
    $stSql .= "WHERE ei.exercicio    = ea.exercicio                                  \n";
    $stSql .= "AND   ei.timestamp    = ea.timestamp                                  \n";
    $stSql .= "AND   ei.cod_empenho  = ea.cod_empenho                                \n";
    $stSql .= "AND   ei.cod_entidade = ea.cod_entidade                               \n";

    return $stSql;
}

/**
    * Seta os dados pra fazer o recuperaEstornoEmpenhoEsfinge
    * @access Private
    * @return $stSql
*/
function montaRecuperaEstornoEmpenhoEsfinge()
{
    $stSql  = "
select empenho_anulado.cod_entidade
      ,empenho_anulado.cod_empenho
      ,to_char(empenho_anulado.timestamp, 'dd/mm/yyyy') as timestamp
      ,empenho_anulado.motivo
      ,total_empenho_anulado.vl_anulado
from empenho.empenho_anulado
join ( select exercicio, cod_empenho, sum(vl_anulado) as vl_anulado
       from empenho.empenho_anulado_item
       group by exercicio, cod_empenho) as total_empenho_anulado
   on empenho_anulado.cod_empenho = total_empenho_anulado.cod_empenho
  and empenho_anulado.exercicio = total_empenho_anulado.exercicio

where empenho_anulado.cod_entidade in (".$this->getDado('cod_entidade').")
  and empenho_anulado.exercicio = '".$this->getDado('exercicio')."'
  and empenho_anulado.timestamp  between to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')
  and to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')";

  return $stSql;

}

/**
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaEstornoEmpenhoEsfinge(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEstornoEmpenhoEsfinge();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
