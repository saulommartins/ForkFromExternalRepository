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
    * Classe de mapeamento da tabela ORCAMENTO.RESERVA_SALDOS_ANULADA
    * Data de Criação: 28/04/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.01.08
                    uc-02.01.28
                    uc-03.04.02

    $Id: TOrcamentoReservaSaldosAnulada.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.RESERVA_SALDOS_ANULADA
  * Data de Criação: 28/04/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TOrcamentoReservaSaldosAnulada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoReservaSaldosAnulada()
{
    parent::Persistente();
    $this->setTabela('orcamento.reserva_saldos_anulada');

    $this->setCampoCod('cod_reserva');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_reserva','integer',true,'',true,true);
    $this->AddCampo('dt_anulacao','date',true,'',false,false);
    $this->AddCampo('motivo_anulacao','varchar',true,'80',false,true);

}

function recuperaRelacionamentoPorAutorizacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoPorAutorizacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaRelacionamentoPorAutorizacao()
{
    $stSql = "SELECT                                                                \n";
    $stSql .= "    rsa.cod_reserva,                                                  \n";
    $stSql .= "    rsa.exercicio,                                                    \n";
    $stSql .= "    rsa.dt_anulacao,                                                  \n";
    $stSql .= "    rsa.motivo_anulacao                                               \n";
    $stSql .= "FROM                                                                  \n";
    $stSql .= "    empenho.autorizacao_reserva         as ar,                        \n";
    $stSql .= "    orcamento.reserva_saldos            as rs,                        \n";
    $stSql .= "    orcamento.reserva_saldos_anulada    as rsa                        \n";
    $stSql .= "WHERE                                                                 \n";
    $stSql .= "    ar.cod_reserva  = rs.cod_reserva    AND                           \n";
    $stSql .= "    ar.exercicio    = rs.exercicio      AND                           \n";
    $stSql .= "                                                                      \n";
    $stSql .= "    rs.cod_reserva  = rsa.cod_reserva   AND                           \n";
    $stSql .= "    rs.exercicio    = rsa.exercicio                                   \n";

    return $stSql;
}

}
