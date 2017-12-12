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
    * Classe de mapeamento da tabela EMPENHO.PRE_EMPENHO_DESPESA
    * Data de Criação: 15/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-03-27 10:37:40 -0300 (Ter, 27 Mar 2007) $

    * Casos de uso: uc-02.03.03
                    uc-02.01.06
*/

/*
$Log$
Revision 1.12  2007/03/27 13:37:40  cako
Bug #8867#

Revision 1.11  2007/03/16 20:27:31  cako
Bug #8634#

Revision 1.10  2007/03/12 13:48:02  vitor
#8577#

Revision 1.9  2007/01/25 15:31:59  luciano
#7864#

Revision 1.8  2007/01/18 18:09:05  luciano
Bug #8009#

Revision 1.7  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.PRE_EMPENHO_DESPESA
  * Data de Criação: 15/12/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoPreEmpenhoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoPreEmpenhoDespesa()
{
    parent::Persistente();
    $this->setTabela('empenho.pre_empenho_despesa');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_pre_empenho');

    $this->AddCampo('cod_pre_empenho','integer',true,  '',true ,true);
    $this->AddCampo('exercicio'      ,'char'   ,true,'04',true ,true);
    $this->AddCampo('cod_conta'      ,'integer',true,  '',false,true);
    $this->AddCampo('cod_despesa'    ,'integer',true,  '',false,true);

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaExistenciaDespesa(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaExistenciaDespesa().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
  $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaExistenciaDespesa()
{
    $stSQl  = " SELECT                                                                                            \n";
    $stSQl .= "         count(PED.cod_despesa) + count(SUPR.cod_despesa) + count(SUPS.cod_despesa) + count(RS.cod_despesa) as total \n";
    $stSQl .= " FROM                                                                                              \n";
    $stSQl .= "         orcamento.despesa as OD                                                                   \n";
    $stSQl .= "                                                                                                   \n";
    $stSQl .= "         LEFT JOIN empenho.pre_empenho_despesa as PED                                              \n";
    $stSQl .= "                     ON ( OD.cod_despesa = PED.cod_despesa                                         \n";
    $stSQl .= "                     AND OD.exercicio = PED.exercicio )                                            \n";
    $stSQl .= "         LEFT JOIN orcamento.conta_despesa as OCD                                                  \n";
    $stSQl .= "                     ON ( OCD.cod_conta = PED.cod_conta                                            \n";
    $stSQl .= "                     AND OCD.exercicio = PED.exercicio )                                           \n";
    $stSQl .= "         LEFT JOIN ( SELECT SUPR.cod_despesa, SUPR.exercicio                                       \n";
    $stSQl .= "                     FROM                                                                          \n";
    $stSQl .= "                     orcamento.suplementacao_reducao as SUPR                                       \n";
    $stSQl .= "                     JOIN orcamento.despesa as OD                                                  \n";
    $stSQl .= "                     ON  ( SUPR.exercicio = OD.exercicio                                           \n";
    $stSQl .= "                     AND SUPR.cod_despesa = OD.cod_despesa )                                       \n";
    $stSQl .= "                     WHERE SUPR.exercicio = '".$this->getDado('exercicio')."'                      \n";
    if($this->getDado('cod_despesa'))
        $stSQl .= "                 AND SUPR.cod_despesa = ".$this->getDado('cod_despesa')."                      \n";
    $stSQl .= "                                                  LIMIT 1 ) as SUPR                                \n";
    $stSQl .= "                     ON ( OD.cod_despesa = SUPR.cod_despesa                                        \n";
    $stSQl .= "                     AND OD.exercicio = SUPR.exercicio )                                           \n";
    $stSQl .= "                                                                                                   \n";
    $stSQl .= "         LEFT JOIN ( SELECT  SUPS.cod_despesa, SUPS.exercicio                                      \n";
    $stSQl .= "                     FROM                                                                          \n";
    $stSQl .= "                     orcamento.suplementacao_suplementada as SUPS                                  \n";
    $stSQl .= "                     JOIN orcamento.despesa as OD                                                  \n";
    $stSQl .= "                     ON  (SUPS.exercicio = OD.exercicio                                            \n";
    $stSQl .= "                     AND SUPS.cod_despesa = OD.cod_despesa)                                        \n";
    $stSQl .= "                     WHERE SUPS.exercicio = '".$this->getDado('exercicio')."'                      \n";
    if($this->getDado('cod_despesa'))
        $stSQl .= "                 AND SUPS.cod_despesa = ".$this->getDado('cod_despesa')."                      \n";
    $stSQl .= "                                                 LIMIT 1) as SUPS                                  \n";
    $stSQl .= "                     ON (SUPS.exercicio = OD.exercicio                                             \n";
    $stSQl .= "                     AND SUPS.cod_despesa = OD.cod_despesa)                                        \n";
    $stSQl .= "                                                                                                   \n";
    $stSQl .= "         LEFT JOIN ( SELECT  RS.cod_despesa, RS.exercicio                                          \n";
    $stSQl .= "                     FROM                                                                          \n";
    $stSQl .= "                     orcamento.reserva_saldos as RS                                                \n";
    $stSQl .= "                     JOIN orcamento.despesa as OD                                                  \n";
    $stSQl .= "                     ON ( OD.cod_despesa = RS.cod_despesa                                          \n";
    $stSQl .= "                     AND OD.exercicio = RS.exercicio )                                             \n";
    $stSQl .= "                     WHERE OD.exercicio = '".$this->getDado('exercicio')."'                        \n";
    if($this->getDado('cod_despesa'))
        $stSQl .= "                 AND OD.cod_despesa = ".$this->getDado('cod_despesa')."                        \n";
    $stSQl .= "                                                 LIMIT 1) as RS                                    \n";
    $stSQl .= "                     ON (OD.cod_despesa = RS.cod_despesa                                           \n";
    $stSQl .= "                     AND OD.exercicio = RS.exercicio)                                              \n";
    $stSQl .= "                                                                                                   \n";
/*
     Não há necessidade desta consulta
                                        */
/*  $stSQl .= "         LEFT JOIN ( SELECT  PRD.cod_despesa, PRD.exercicio                                        \n";
    $stSQl .= "                     FROM                                                                          \n";
    $stSQl .= "                     orcamento.previsao_despesa as PRD                                             \n";
    $stSQl .= "                     JOIN orcamento.despesa as OD                                                  \n";
    $stSQl .= "                     ON ( OD.cod_despesa = PRD.cod_despesa                                         \n";
    $stSQl .= "                     AND OD.exercicio = PRD.exercicio )                                            \n";
    $stSQl .= "                     WHERE OD.exercicio = '".$this->getDado('exercicio')."'                        \n";
    if($this->getDado('cod_despesa'))
        $stSQl .= "                 AND OD.cod_despesa = ".$this->getDado('cod_despesa')."                        \n";
    $stSQl .= "                                                 LIMIT 1) as PRD                                   \n";
    $stSQl .= "                     ON (OD.cod_despesa = PRD.cod_despesa                                          \n";
    $stSQl .= "                     AND OD.exercicio = PRD.exercicio)                                             \n"; */
    $stSQl .= "                                                                                                   \n";
    $stSQl .= " WHERE OD.exercicio = '".$this->getDado('exercicio')."'                                            \n";
    if($this->getDado('cod_despesa'))
        $stSQl .= " AND OD.cod_despesa = ".$this->getDado('cod_despesa')."                                        \n";

    return $stSQl;
}

}
